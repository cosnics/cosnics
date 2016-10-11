<?php
namespace Chamilo\Core\Repository\Feedback\Infrastructure\Service;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;

/**
 * Notifies users about new feedback through mail
 *
 * @package Chamilo\Core\Repository\Feedback\Infrastructure\Service
 */
abstract class MailNotificationHandler implements NotificationHandlerInterface
{

    /**
     *
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * MailNotificationHandler constructor.
     *
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handles a single notification for a new feedback object
     *
     * @param Feedback $feedback
     * @param Notification[] $notifications
     */
    public function handleNotifications(Feedback $feedback, array $notifications = array())
    {
        if(empty($notifications))
        {
            return;
        }
        
        $targetUsers = array();

        foreach ($notifications as $notification)
        {
            $user = $notification->get_user();
            $targetUsers[] = $user->get_email();
        }

        $targetUsers = array_unique($targetUsers);

        if(empty($targetUsers))
        {
            return;
        }

        $mail = new Mail($this->getMailSubject($feedback), $this->getMailContent($feedback), $targetUsers);
        $this->mailer->sendMail($mail);
    }

    /**
     * Returns the subject for the mail
     *
     * @param Feedback $feedback
     *
     * @return string
     */
    abstract protected function getMailSubject(Feedback $feedback);

    /**
     * Returns the content for the mail
     *
     * @param Feedback $feedback
     *
     * @return string
     */
    abstract protected function getMailContent(Feedback $feedback);
}