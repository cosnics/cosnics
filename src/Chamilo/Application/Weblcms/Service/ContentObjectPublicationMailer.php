<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\CourseRepositoryInterface;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\PublicationRepositoryInterface;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Mail\ValueObject\MailFile;
use Chamilo\Libraries\Platform\Translation;

/**
 * Service class that mails a content object publication to a user
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationMailer
{

    /**
     *
     * @var MailerInterface
     */
    protected $mailer;

    /**
     *
     * @var Translation
     */
    protected $translator;

    /**
     *
     * @var CourseRepositoryInterface
     */
    protected $courseRepository;

    /**
     *
     * @var PublicationRepositoryInterface
     */
    protected $publicationRepository;

    /**
     *
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * ContentObjectPublicationMailer constructor.
     * 
     * @param MailerInterface $mailer
     * @param Translation $translator
     * @param CourseRepositoryInterface $courseRepository
     * @param PublicationRepositoryInterface $publicationRepository
     * @param ContentObjectRepository $contentObjectRepository
     * @param UserRepository $userRepository
     */
    public function __construct(MailerInterface $mailer, Translation $translator, 
        CourseRepositoryInterface $courseRepository, PublicationRepositoryInterface $publicationRepository, 
        ContentObjectRepository $contentObjectRepository, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->courseRepository = $courseRepository;
        $this->publicationRepository = $publicationRepository;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Mails the given publication to the target users
     * 
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function mailPublication(ContentObjectPublication $contentObjectPublication)
    {
        set_time_limit(3600);
        
        $user = $this->userRepository->findUserById($contentObjectPublication->get_publisher_id());
        $content_object = $contentObjectPublication->get_content_object();
        $tool = $contentObjectPublication->get_tool();
        $link = $this->getContentObjectPublicationUrl($contentObjectPublication);
        $course = $this->courseRepository->findCourse($contentObjectPublication->get_course_id());
        
        $body = $this->getTranslation('NewPublicationMailDescription') . ' ' . $course->get_title() . ' : <a href="' .
             $link . '" target="_blank">' . utf8_decode($content_object->get_title()) . '</a><br />--<br />';
        
        $body .= $content_object->get_description();
        $body .= '--<br />';
        
        $body .= $user->get_fullname() . ' - ' . $course->get_visual_code() . ' - ' . $course->get_title() . ' - ' .
             $this->getTranslation('TypeName', null, 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $tool);
        
        $targetUsers = $this->getTargetUserEmails($contentObjectPublication, $user);
        
        $mailFiles = array();
        $body = $this->parseResources($body, $mailFiles);
        
        if ($content_object->has_attachments())
        {
            $body .= '<br ><br >' . $this->getTranslation('AttachmentWarning', array('LINK' => $link));
        }
        
        $log = '';
        $log .= "mail for publication " . $contentObjectPublication->getId() . " in course ";
        $log .= $course->get_title();
        $log .= " to: \n";
        
        $subject = $this->getTranslation(
            'NewPublicationMailSubject', 
            array('COURSE' => $course->get_title(), 'CONTENTOBJECT' => $content_object->get_title()));
        
        $mail = new Mail(
            $subject, 
            $body, 
            $targetUsers, 
            true, 
            array(), 
            array(), 
            $user->get_fullname(), 
            $user->get_email(), 
            null, 
            null, 
            $mailFiles);
        
        try
        {
            $this->mailer->sendMail($mail);
            
            $log .= " (successfull)\n";
        }
        catch (\Exception $ex)
        {
            $log .= " (unsuccessfull)\n";
        }
        
        $this->logMailProgress($log);
        
        $contentObjectPublication->set_email_sent(true);
        $contentObjectPublication->update();
    }

    /**
     * Builds and returns the link to the content object publicatoin
     * 
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return string
     */
    protected function getContentObjectPublicationUrl(ContentObjectPublication $contentObjectPublication)
    {
        $parameters = array();
        
        $parameters[Manager::PARAM_CONTEXT] = Manager::package();
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
        $parameters[Manager::PARAM_COURSE] = $contentObjectPublication->get_course_id();
        $parameters[Manager::PARAM_TOOL] = $contentObjectPublication->get_tool();
        
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $contentObjectPublication->getId();
        
        $redirect = new Redirect($parameters);
        
        return $redirect->getUrl();
    }

    /**
     * Parses the resources and if they are images, add them as attachments
     * 
     * @param string $body
     * @param array $mailFiles
     *
     * @return string
     */
    protected function parseResources($body, &$mailFiles)
    {
        $doc = new \DOMDocument();
        $doc->loadHTML($body);
        $elements = $doc->getElementsByTagName('resource');
        
        $index = 0;
        
        foreach ($elements as $i => $element)
        {
            $type = $element->attributes->getNamedItem('type')->value;
            $id = $element->attributes->getNamedItem('source')->value;
            if ($type == 'file')
            {
                /** @var File $object */
                $object = $this->contentObjectRepository->findById($id);
                
                if ($object->is_image())
                {
                    $mailFiles[] = new MailFile(
                        $object->get_filename(), 
                        $object->get_full_path(), 
                        $object->get_mime_type());
                    
                    $elem = $doc->createElement('img');
                    $elem->setAttribute('src', 'cid:' . $index);
                    $elem->setAttribute('alt', $object->get_filename());
                    $element->parentNode->replaceChild($elem, $element);
                    
                    $index ++;
                }
                else
                {
                    $element->parentNode->removeChild($element);
                }
            }
            else
            {
                $element->parentNode->removeChild($element);
            }
        }
        
        return $doc->saveHTML();
    }

    /**
     * Returns the email addresses of the target users for the publication
     * 
     * @param ContentObjectPublication $contentObjectPublication
     * @param User $publisher
     *
     * @return string[]
     */
    protected function getTargetUserEmails(ContentObjectPublication $contentObjectPublication, User $publisher)
    {
        $target_email = array();
        
        $target_email[] = $publisher->get_email();
        $target_users = $this->publicationRepository->findPublicationTargetUsers($contentObjectPublication);
        
        foreach ($target_users as $target_user)
        {
            $target_email[] = $target_user[User::PROPERTY_EMAIL];
        }
        
        $unique_email = array_unique($target_email);
        
        return $unique_email;
    }

    /**
     * Logs the progress of the mailing
     * 
     * @param string $logMessage
     */
    protected function logMailProgress($logMessage)
    {
        if (Configuration::getInstance()->get_setting(array('Chamilo\Application\Weblcms', 'log_mails')))
        {
            $dir = Path::getInstance()->getLogPath() . 'mail';
            
            if (! file_exists($dir) and ! is_dir($dir))
            {
                mkdir($dir);
            }
            
            $today = date("Ymd", mktime());
            $logfile = $dir . '//' . "mails_sent_$today" . ".log";
            $mail_log = new FileLogger($logfile, true);
            $mail_log->log_message($logMessage, true);
        }
    }

    /**
     * Helper function to get the translation
     * 
     * @param string $variable
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    public function getTranslation($variable, $parameters = array(), $context = 'Chamilo\Application\Weblcms')
    {
        return $this->translator->getTranslation($variable, $parameters, $context);
    }
}
