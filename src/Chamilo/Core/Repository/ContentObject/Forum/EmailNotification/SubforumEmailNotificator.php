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
        foreach ($this->users as $user)
        {
            $site_name = PlatformSetting :: get('site_name', 'Chamilo\Core\Admin');

            $subject = '[' . $site_name . '] ' . $this->action_title . ' ' . $this->forum->get_title();

            $message = $this->action_body . ' ' . $this->forum->get_title() . '<br/>' . '-' . '<br/>';
            $message = $message . $this->subforum->get_title() . '<br/>' . $this->subforum->get_description();
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
