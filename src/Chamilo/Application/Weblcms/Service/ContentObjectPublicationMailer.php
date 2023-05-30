<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\CourseRepositoryInterface;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\PublicationRepositoryInterface;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Mail\ValueObject\MailFile;
use DOMDocument;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * Service class that mails a content object publication to a user
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationMailer
{

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ContentObjectRepository $contentObjectRepository;

    protected CourseRepositoryInterface $courseRepository;

    protected MailerInterface $mailer;

    protected PublicationRepositoryInterface $publicationRepository;

    protected ThemePathBuilder $themeWebPathBuilder;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected UserService $userService;

    public function __construct(
        MailerInterface $mailer, Translator $translator, CourseRepositoryInterface $courseRepository,
        PublicationRepositoryInterface $publicationRepository, ContentObjectRepository $contentObjectRepository,
        UserService $userService, ThemePathBuilder $themeWebPathBuilder,
        ConfigurablePathBuilder $configurablePathBuilder, UrlGenerator $urlGenerator
    )
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->courseRepository = $courseRepository;
        $this->publicationRepository = $publicationRepository;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->userService = $userService;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->urlGenerator = $urlGenerator;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
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
        $parameters = [];

        $parameters[Manager::PARAM_CONTEXT] = Manager::CONTEXT;
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
        $parameters[Manager::PARAM_COURSE] = $contentObjectPublication->get_course_id();
        $parameters[Manager::PARAM_TOOL] = $contentObjectPublication->get_tool();

        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;

        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] =
            $contentObjectPublication->getId();

        return $this->getUrlGenerator()->fromParameters($parameters);
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
        $target_email = [];

        $target_email[] = $publisher->get_email();
        $target_users = $this->publicationRepository->findPublicationTargetUsers($contentObjectPublication);

        foreach ($target_users as $target_user)
        {
            if (!array_key_exists(User::PROPERTY_ACTIVE, $target_user) || $target_user[User::PROPERTY_ACTIVE] == 1)
            {
                $target_email[] = $target_user[User::PROPERTY_EMAIL];
            }
        }

        $unique_email = array_unique($target_email);

        return $unique_email;
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return $this->themeWebPathBuilder;
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
    public function getTranslation(
        string $variable, array $parameters = [], string $context = 'Chamilo\Application\Weblcms'
    ): ?string
    {
        return $this->translator->trans($variable, $parameters, $context);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * Logs the progress of the mailing
     *
     * @param string $logMessage
     */
    protected function logMailProgress($logMessage)
    {
        if (Configuration::getInstance()->get_setting(['Chamilo\Application\Weblcms', 'log_mails']))
        {
            $dir = $this->getConfigurablePathBuilder()->getLogPath() . 'mail';

            if (!file_exists($dir) and !is_dir($dir))
            {
                mkdir($dir);
            }

            $today = date('Ymd', time());
            $logfile = $dir . '//' . "mails_sent_$today" . '.log';
            $mail_log = new FileLogger($logfile, true);
            $mail_log->log_message($logMessage);
        }
    }

    /**
     * Mails the given publication to the target users
     *
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @throws \Exception
     */
    public function mailPublication(ContentObjectPublication $contentObjectPublication)
    {
        set_time_limit(3600);

        $user = $this->getUserService()->findUserByIdentifier($contentObjectPublication->get_publisher_id());
        $content_object = $contentObjectPublication->get_content_object();
        $tool = $contentObjectPublication->get_tool();
        $link = $this->getContentObjectPublicationUrl($contentObjectPublication);
        $course = $this->courseRepository->findCourse($contentObjectPublication->get_course_id());

        $webPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(WebPathBuilder::class);
        $cssPath = $webPathBuilder->getCssPath('Chamilo/Libraries');

        $body = '<!DOCTYPE html><html lang="en"><head>';
        $body .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'cosnics.vendor.bootstrap.min.css' .
            '" />';
        $body .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'cosnics.vendor.jquery.min.css' . '" />';
        $body .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'cosnics.vendor.min.css' . '" />';
        $body .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'cosnics.common.' .
            $this->getThemeWebPathBuilder()->getTheme() . '.min.css' . '" />';
        $body .= '</head><body><div class="container-fluid" style="margin-top: 15px;">';

        $body .= $this->getTranslation('NewPublicationMailDescription') . ' ' . $course->get_title() . ' : <a href="' .
            $link . '" target="_blank">' . utf8_decode($content_object->get_title()) . '</a><br />--<br />';

        $body .= $content_object->get_description();
        $body .= '--<br />';

        $body .= $user->get_fullname() . ' - ' . $course->get_visual_code() . ' - ' . $course->get_title() . ' - ' .
            $this->getTranslation('TypeName', null, 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $tool);

        $targetUsers = $this->getTargetUserEmails($contentObjectPublication, $user);

        $mailFiles = [];
        $body = $this->parseResources($body, $mailFiles);

        if ($content_object->has_attachments())
        {
            $body .= '<br ><br >' . $this->getTranslation('AttachmentWarning', ['LINK' => $link]);
        }

        $body .= '</div></body></html>';

        $log = 'mail for publication ' . $contentObjectPublication->getId() . ' in course ';
        $log .= $course->get_title();
        $log .= " to: \n";

        $subject = $this->getTranslation(
            'NewPublicationMailSubject',
            ['COURSE' => $course->get_title(), 'CONTENTOBJECT' => $content_object->get_title()]
        );

        $mail = new Mail(
            $subject, $body, $targetUsers, true, [], [], $user->get_fullname(), $user->get_email(), null, null,
            $mailFiles
        );

        try
        {
            $this->mailer->sendMail($mail);

            $log .= " (successfull)\n";
        }
        catch (Exception $ex)
        {
            $log .= " (unsuccessfull)\n";
        }

        $this->logMailProgress($log);

        $contentObjectPublication->set_email_sent(true);
        $contentObjectPublication->update();
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
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $body);
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
                        $object->get_filename(), $object->get_full_path(), $object->get_mime_type()
                    );

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
}
