<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\EmailNotification;

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
     * send a message to all the subscribers
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

        $site_name = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'site_name']);

        $subject = '[' . $site_name . '] ' . $this->action_title . ' ' . $this->forum->get_title();

        $message = $this->action_body . ' ' . $this->forum->get_title() . '<br/>' . '-' . '<br/>';
        $message = $message . $this->subforum->get_title() . '<br/>' . $this->subforum->get_description();
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

    public function set_subforum($forum)
    {
        $this->subforum = $forum;
    }
}
