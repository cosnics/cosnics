<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Mail\ValueObject\Mail;

/**
 * @package Chamilo\Libraries\Mail\Mailer
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractMailer implements MailerInterface
{

    protected ConfigurationConsulter $configurationConsulter;

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Determines the default e-mail address when no valid e-mail is given
     */
    protected function determineDefaultEmail(): string
    {
        $noReplyEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'no_reply_email']);

        if (!empty($noReplyEmail))
        {
            return $noReplyEmail;
        }

        return $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'administrator_email']);
    }

    /**
     * Determines the sender e-mail for the given mail
     */
    protected function determineFromEmail(Mail $mail): string
    {
        if (!is_null($mail->getFromEmail()))
        {
            return $mail->getFromEmail();
        }

        return $this->determineDefaultEmail();
    }

    /**
     * Determines the sender name for the given mail
     */
    protected function determineFromName(Mail $mail): string
    {
        if (!is_null($mail->getFromName()))
        {
            return $mail->getFromName();
        }

        return $this->getAdministratorName();
    }

    /**
     * Determines the reply e-mail for the given mail
     */
    protected function determineReplyEmail(Mail $mail): string
    {
        if (!is_null($mail->getReplyEmail()))
        {
            return $mail->getReplyEmail();
        }

        return $this->determineDefaultEmail();
    }

    protected function determineReplyName(Mail $mail): string
    {
        if (!is_null($mail->getReplyName()))
        {
            return $mail->getReplyName();
        }

        return $this->getAdministratorName();
    }

    protected function getAdministratorName(): string
    {
        return $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'administrator_name']);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Libraries\Mail\ValueObject\Mail[] $mails
     */
    public function sendMails(array $mails = []): void
    {
        foreach ($mails as $mail)
        {
            $this->sendMail($mail);
        }
    }
}