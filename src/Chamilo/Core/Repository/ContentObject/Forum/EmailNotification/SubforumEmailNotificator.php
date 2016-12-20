<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\EmailNotification;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Translation;

/**
 * the email notificator of a post extends abstract email notificator
 * 
 * @author Mattias De Pauw
 */
class SubforumEmailNotificator extends EmailNotificator
{

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    private $forum;

    private $subforum;

    /**
     * sets the forum
     * 
     * @param Forum $forum
     */
    public function set_forum($forum)
    {
        $this->forum = $forum;
    }

    public function set_subforum($forum)
    {
        $this->subforum = $forum;
    }

    /**
     * send a message to all the subscribers
     */
    public function send_emails()
    {
        $targetUsers = array();
        foreach ($this->users as $user)
        {
            $targetUsers[] = $user->get_email();
        }
        
        $site_name = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'site_name'));
        
        $subject = '[' . $site_name . '] ' . $this->action_title . ' ' . $this->forum->get_title();
        
        $message = $this->action_body . ' ' . $this->forum->get_title() . '<br/>' . '-' . '<br/>';
        $message = $message . $this->subforum->get_title() . '<br/>' . $this->subforum->get_description();
        $message = str_replace('[/quote]', '</div>', $message);
        $message = $message . '<br/>' . Translation::get("By") . ': ' . $this->action_user->get_firstname() . ' ' .
             $this->action_user->get_lastname();

        $mail = new Mail(
            $subject, $message, $targetUsers, true, array(), array(), $this->action_user->get_fullname(),
            $this->action_user->get_email()
        );
        
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();
        
        try
        {
            $mailer->sendMail($mail);
        }
        catch (\Exception $ex)
        {
        }
    }
}
