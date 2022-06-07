<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Infrastructure\Service;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Mail notification handler for portfolio items
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MailNotificationHandler extends \Chamilo\Core\Repository\Feedback\Infrastructure\Service\MailNotificationHandler
{

    /**
     * The portfolio user
     * 
     * @var User
     */
    protected $portfolioUser;

    /**
     * The selected content object to which the feedback has been added
     * 
     * @var ContentObject
     */
    protected $contentObject;

    /**
     * The URL to the feedback
     * 
     * @var string
     */
    protected $feedbackURL;

    /**
     * MailNotificationHandler constructor.
     * 
     * @param MailerInterface $mailer
     * @param User $portfolioUser
     * @param ContentObject $contentObject
     * @param string $feedbackURL
     */
    public function __construct(MailerInterface $mailer, User $portfolioUser, ContentObject $contentObject, $feedbackURL)
    {
        parent::__construct($mailer);
        
        $this->portfolioUser = $portfolioUser;
        $this->contentObject = $contentObject;
        $this->feedbackURL = $feedbackURL;
    }

    /**
     * Returns the subject for the mail
     * 
     * @param Feedback $feedback
     *
     * @return string
     */
    protected function getMailSubject(Feedback $feedback)
    {
        return $this->getTranslation(
            'NewFeedbackForItemSubject', 
            array(
                'USER' => $this->portfolioUser->get_fullname(), 
                'CONTENT_OBJECT_TITLE' => $this->contentObject->get_title()));
    }

    /**
     * Returns the content for the mail
     * 
     * @param Feedback $feedback
     *
     * @return string
     */
    protected function getMailContent(Feedback $feedback)
    {
        $html = [];
        
        $html[] = '<!DOCTYPE html>';
        $html[] = '<html lang="en">';
        $html[] = '<head>';
        $html[] = '<meta charset="utf-8">';
        $html[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        $html[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $html[] = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">';
        $html[] = '</head>';
        $html[] = '<body>';
        
        $html[] = '<div class="container-fluid" style="margin-top: 20px;">';
        
        $html[] = $this->getTranslation(
            'NewFeedbackForItem', 
            array(
                'USER' => $this->portfolioUser->get_fullname(), 
                'CONTENT_OBJECT_TITLE' => $this->contentObject->get_title()));
        
        $html[] = '<p style="margin-top: 10px;">';
        
        $html[] = $this->getTranslation(
            'FeedbackWrittenBy', 
            array(
                'USER' => $feedback->get_user()->get_fullname(), 
                'DATE' => DatetimeUtilities::getInstance()->formatLocaleDate(null, $feedback->get_creation_date())));
        
        $html[] = '<pre>';
        $html[] = $feedback->get_comment();
        $html[] = '</pre>';
        
        $html[] = '</p>';
        
        $html[] = $this->getTranslation('ViewFeedbackOnline', array('URL' => $this->feedbackURL));
        
        $html[] = '</div>';
        $html[] = '</body>';
        $html[] = '</html>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Helper function to return the translation of a variable
     * 
     * @param string $variable
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters, 
        $context = 'Chamilo\Core\Repository\ContentObject\Portfolio\Display')
    {
        return Translation::getInstance()->getTranslation($variable, $parameters, $context);
    }
}