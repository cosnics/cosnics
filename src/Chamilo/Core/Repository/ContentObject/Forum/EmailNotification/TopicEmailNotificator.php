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
class TopicEmailNotificator extends EmailNotificator
{

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    private $forum;

    private $is_topic_edited;

    private $previous_title;

    /**
     * sets the forum
     * 
     * @param Forum $forum
     */
    public function set_forum($forum)
    {
        $this->forum = $forum;
    }

    /**
     * if the topic is edited then is_topic_edited op true
     * 
     * @param Boolean $edited
     */
    public function set_is_topic_edited($edited)
    {
        $this->is_topic_edited = $edited;
    }

    /**
     * if the topic is edited then sets the previous title
     * 
     * @param String $title
     */
    public function set_previous_title($title)
    {
        $this->previous_title = $title;
    }

    /**
     * Send a message to all the subscribers
     */
    public function send_emails()
    {
        $targetUsers = array();
        foreach ($this->users as $user)
        {
            $targetUsers[] = $user->get_email();
        }
        
        $site_name = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'site_name'));
        
        if ($this->is_topic_edited)
        {
            $subject = '[' . $site_name . '] ' . $this->action_title . ' ' . $this->previous_title;
            $message = $this->action_body . '<br/>' . $this->topic->get_title() . '<br/>' . '-';
            $message = $message . '<br/>' . $this->topic->get_description();
        }
        else
        {
            $subject = '[' . $site_name . '] ' . $this->action_title . ' ' . $this->forum->get_title();
            $message = $this->action_body . ' ' . $this->forum->get_title() . '<br/>' . '-' . '<br/>';
            $message = $message . $this->topic->get_title() . '<br/>' . $this->topic->get_description();
        }
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
