<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\EmailNotification;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Exception;

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
     * @throws \Exception
     */
    protected function getActiveMailer(): MailerInterface
    {
        /**
         * @var \Chamilo\Libraries\Mail\Mailer\MailerInterface
         */
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\Mail\Mailer\ActiveMailer'
        );
    }

    /**
     * Send a message to all the subscribers
     *
     * @throws \Exception
     */
    public function send_emails()
    {
        $targetUsers = [];
        foreach ($this->users as $user)
        {
            $targetUsers[] = $user->get_email();
        }

        $site_name = Configuration::getInstance()->get_setting(['Chamilo\Core\Admin', 'site_name']);

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
        $message = $message . '<br/>' . Translation::get('By') . ': ' . $this->action_user->get_firstname() . ' ' .
            $this->action_user->get_lastname();

        $mail = new Mail(
            $subject, $message, $targetUsers, true, [], [], $this->action_user->get_fullname(),
            $this->action_user->get_email()
        );

        $mailer = $this->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (Exception $ex)
        {
        }
    }

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
}
