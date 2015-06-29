<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\EmailNotification;

use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

/**
 * The email notificator of a post extends abstract email notificator
 *
 * @author Mattias De Pauw - Hogeschool Gent
 */
class PostEmailNotificator extends EmailNotificator
{

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    private $post;

    private $first_post_text;

    /**
     * set the post which is changed
     *
     * @param $post
     */
    public function set_post($post)
    {
        $this->post = $post;
    }

    /**
     * set if the post is the first post of a topic
     *
     * @param $first_post_text
     */
    public function set_first_post_text($first_post_text)
    {
        $this->first_post_text = $first_post_text;
    }

    /**
     * send a message to all the subscribers
     */
    public function send_emails()
    {
        foreach ($this->users as $user)
        {
            $site_name = PlatformSetting :: get('site_name', 'Chamilo\Core\Admin');

            $subject = '[' . $site_name . '] ' . $this->action_title . ' ' . $this->topic->get_title();

            $message = $this->action_body . ' ' . $this->topic->get_title() . '<br/>' . '-' . '<br/>';
            $message = $message . $this->post->get_content();

            $message = preg_replace(
                '/\[quote=("|&quot;)(.*)("|&quot;)\]/',
                "<div class=\"quotetitle\">$2 " . Translation :: get('Wrote') . ":</div><div class=\"quotecontent\">",
                $message);

            $message = str_replace('[/quote]', '</div>', $message);
            $message = $message . '<br/>' . Translation :: get("By") . ': ' . $this->action_user->get_firstname() . ' ' .
                 $this->action_user->get_lastname();

            if ($this->first_post_text)
            {
                $message = $message . '<br/>' . $this->first_post_text;
            }

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
