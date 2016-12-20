<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\EmailNotification;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
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
        $targetUsers = array();
        foreach ($this->users as $user)
        {
            $targetUsers[] = $user->get_email();
        }

        $site_name = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'site_name'));

        $subject = '[' . $site_name . '] ' . $this->action_title . ' ' . $this->topic->get_title();

        $message = $this->action_body . ' ' . $this->topic->get_title() . '<br/>' . '-' . '<br/>';
        $message = $message . $this->post->get_content();

        $message = preg_replace(
            '/\[quote=("|&quot;)(.*)("|&quot;)\]/',
            "<div class=\"quotetitle\">$2 " . Translation::get('Wrote') . ":</div><div class=\"quotecontent\">",
            $message
        );

        $message = str_replace('[/quote]', '</div>', $message);
        $message = $message . '<br/>' . Translation::get("By") . ': ' . $this->action_user->get_firstname() . ' ' .
            $this->action_user->get_lastname();

        if ($this->first_post_text)
        {
            $message = $message . '<br/>' . $this->first_post_text;
        }

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
