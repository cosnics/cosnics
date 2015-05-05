<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\EmailNotification;

use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
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
        $subject = "";
        $message = "";
        foreach ($this->users as $user)
        {
            $site_name = PlatformSetting :: get('site_name', 'Chamilo\Core\Admin');

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
            $message = $message . '<br/>' . Translation :: get("By") . ': ' . $this->action_user->get_firstname() . ' ' .
                 $this->action_user->get_lastname();

            $admin_email = PlatformSetting :: get('administrator_email', 'Chamilo\Core\Admin');
            $admin_name = PlatformSetting :: get('admin_surname', 'Chamilo\Core\Admin') . ' ' .
                 PlatformSetting :: get('admin_firstname', 'Chamilo\Core\Admin');

            $mail = Mail :: factory(
                $subject,
                $message,
                $user->get_email(),
                array(Mail :: NAME => $admin_name, Mail :: EMAIL => $admin_email));
            $mail->send();
        }
    }
}
