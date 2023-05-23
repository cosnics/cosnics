<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Storage\DataClass\MailLog;
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

    /**
     * Determines the reply name for the given mail
     */
    protected function determineReplyName(Mail $mail): string
    {
        if (!is_null($mail->getReplyName()))
        {
            return $mail->getReplyName();
        }

        return $this->getAdministratorName();
    }

    /**
     * Returns the name of the administrator from the platform settings
     *
     * @return string
     */
    protected function getAdministratorName(): string
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        return $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_firstname']) .
            $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_surname']);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * Logs a send (or not send) mail to the database
     *
     * @throws \RuntimeException
     */
    protected function logMail(Mail $mail, int $state = MailLog::STATE_SUCCESSFUL, ?string $message = null)
    {
        // $log = new MailLog();
        // $log->set_sender($this->determineFromEmail($mail));
        // $log->set_recipient(json_encode($mail->getTo()));
        // $log->set_date(time());
        // $log->set_subject($mail->getSubject());
        // $log->set_host(gethostname());
        // $log->set_state($state);
        // $log->set_message($message);
        //
        // if(!$log->create())
        // {
        // throw new \RuntimeException('Could not create a mail log');
        // }
    }

    /**
     * @param \Chamilo\Libraries\Mail\ValueObject\Mail[] $mails
     */
    public function sendMails(array $mails = [])
    {
        foreach ($mails as $mail)
        {
            $this->sendMail($mail);
        }
    }
}