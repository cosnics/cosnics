<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\MailLog;
use Chamilo\Libraries\Mail\ValueObject\Mail;

/**
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AbstractMailer implements MailerInterface
{

    /**
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * Mailer constructor.
     * 
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        if (! $configuration instanceof Configuration)
        {
            $configuration = Configuration::getInstance();
        }
        
        $this->configuration = $configuration;
    }

    /**
     * Sends multiple mails
     * 
     * @param Mail[] $mails
     */
    public function sendMails($mails = array())
    {
        foreach ($mails as $mail)
        {
            $this->sendMail($mail);
        }
    }

    /**
     * Determines the sender name for the given mail
     * 
     * @param Mail $mail
     *
     * @return string
     */
    protected function determineFromName(Mail $mail)
    {
        if (! is_null($mail->getFromName()))
        {
            return $mail->getFromName();
        }
        
        return $this->configuration->get_setting(array('Chamilo\Core\Admin', 'administrator_name'));
    }

    /**
     * Determines the sender e-mail for the given mail
     * 
     * @param Mail $mail
     *
     * @return string
     */
    protected function determineFromEmail(Mail $mail)
    {
        if (! is_null($mail->getFromEmail()))
        {
            return $mail->getFromEmail();
        }
        
        return $this->determineDefaultEmail();
    }

    /**
     * Determines the reply name for the given mail
     * 
     * @param Mail $mail
     *
     * @return string
     */
    protected function determineReplyName(Mail $mail)
    {
        if (! is_null($mail->getReplyName()))
        {
            return $mail->getReplyName();
        }
        
        return $this->configuration->get_setting(array('Chamilo\Core\Admin', 'administrator_name'));
    }

    /**
     * Determines the reply e-mail for the given mail
     * 
     * @param Mail $mail
     *
     * @return string
     */
    protected function determineReplyEmail(Mail $mail)
    {
        if (! is_null($mail->getReplyEmail()))
        {
            return $mail->getReplyEmail();
        }
        
        return $this->determineDefaultEmail();
    }

    /**
     * Determines the default e-mail address when no valid e-mail is given
     * 
     * @return string
     */
    protected function determineDefaultEmail()
    {
        $noReplyEmail = $this->configuration->get_setting(array('Chamilo\Core\Admin', 'no_reply_email'));
        if (! empty($noReplyEmail))
        {
            return $noReplyEmail;
        }
        
        return $this->configuration->get_setting(array('Chamilo\Core\Admin', 'administrator_email'));
    }

    /**
     * Logs a send (or not send) mail to the database
     * 
     * @param Mail $mail
     * @param int $state
     * @param string $message
     *
     * @throws \RuntimeException
     */
    protected function logMail(Mail $mail, $state = MailLog::STATE_SUCCESSFUL, $message = null)
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
}