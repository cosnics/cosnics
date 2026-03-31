<?php
namespace Chamilo\Libraries\Protocol\Mail\Service;

use Chamilo\Libraries\Protocol\Mail\Architecture\Domain\Mail;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;

/**
 * @package Chamilo\Libraries\Protocol\Mail\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractMailer implements MailerInterface
{
    public function __construct(
        protected string $administratorName, protected string $administratorEmail, protected ?string $noRepyEmail = null
    )
    {
    }

    /**
     * Determines the default e-mail address when no valid e-mail is given
     */
    protected function determineDefaultEmail(): string
    {
        return $this->noRepyEmail ?: $this->administratorEmail;
    }

    /**
     * Determines the sender e-mail for the given mail
     */
    protected function determineFromEmail(Mail $mail): string
    {
        return $mail->getFromEmail() ?: $this->determineDefaultEmail();
    }

    /**
     * Determines the sender name for the given mail
     */
    protected function determineFromName(Mail $mail): string
    {
        return $mail->getFromName() ?: $this->administratorName;
    }

    /**
     * Determines the reply e-mail for the given mail
     */
    protected function determineReplyEmail(Mail $mail): string
    {
        return $mail->getReplyEmail() ?: $this->determineDefaultEmail();
    }

    protected function determineReplyName(Mail $mail): string
    {
        return $mail->getReplyName() ?: $this->administratorName;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\Mail[] $mails
     */
    public function sendMails(array $mails = []): void
    {
        foreach ($mails as $mail) {
            $this->sendMail($mail);
        }
    }
}