<?php
namespace Chamilo\Libraries\Mail\Mailer\PhpMailer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Storage\DataClass\MailLog;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Mail\Mailer\AbstractMailer;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

/**
 * PHPMailer mailer service
 *
 * @package Chamilo\Libraries\Mail\Mailer\PhpMailer
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Mailer extends AbstractMailer
{
    protected PHPMailer $phpMailer;

    protected SystemPathBuilder $systemPathBuilder;

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function __construct(ConfigurationConsulter $configurationConsulter, SystemPathBuilder $systemPathBuilder)
    {
        parent::__construct($configurationConsulter);

        $this->systemPathBuilder = $systemPathBuilder;
        $this->initializePhpMailer();
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function addAttachments(Mail $mail)
    {
        foreach ($mail->getAttachments() as $mailFile)
        {
            $this->phpMailer->addAttachment($mailFile->getPath(), $mailFile->getFilename());
        }
    }

    protected function addContent(Mail $mail)
    {
        $this->phpMailer->Body = $mail->getMessage();
        $this->phpMailer->Subject = $mail->getSubject();
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function addEmbeddedImages(Mail $mail)
    {
        foreach ($mail->getEmbeddedImages() as $index => $mailFile)
        {
            $this->phpMailer->addEmbeddedImage(
                $mailFile->getPath(), $index, $mailFile->getFilename(), 'base64', $mailFile->getMimeType()
            );
        }
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function addRecipients(Mail $mail)
    {
        foreach ($mail->getTo() as $index => $recipient)
        {
            $this->phpMailer->addAddress($recipient, $recipient);
        }

        foreach ($mail->getCc() as $recipient)
        {
            $this->phpMailer->addCC($recipient, $recipient);
        }

        foreach ($mail->getBcc() as $recipient)
        {
            $this->phpMailer->addBCC($recipient, $recipient);
        }
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function addReplyInformation(Mail $mail)
    {
        if (!is_null($mail->getReplyEmail()))
        {
            $this->phpMailer->addReplyTo($this->determineReplyEmail($mail), $this->determineReplyName($mail));
            $this->phpMailer->addCustomHeader('Return-Path: <' . $this->determineReplyEmail($mail) . '>');
        }
        else
        {
            $this->phpMailer->addCustomHeader('Return-Path: <' . $this->phpMailer->From . '>');
        }
    }

    /**
     * Adds the information about the sender
     *
     * @param \Chamilo\Libraries\Mail\ValueObject\Mail $mail
     */
    protected function addSenderInformation(Mail $mail)
    {
        $this->phpMailer->From = $this->determineFromEmail($mail);
        $this->phpMailer->Sender = $this->phpMailer->From;
        $this->phpMailer->FromName = $this->determineFromName($mail);
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function initializePhpMailer()
    {
        if (!isset($this->phpMailer))
        {
            global $phpMailerConfiguration;
            require_once($this->getSystemPathBuilder()->getStoragePath() . 'configuration/phpmailer.conf.php');

            $this->phpMailer = new PHPMailer();

            $this->phpMailer->isHTML(true);
            $this->phpMailer->CharSet = 'utf-8';
            $this->phpMailer->Mailer = $phpMailerConfiguration['SMTP_MAILER'];
            $this->phpMailer->Host = $phpMailerConfiguration['SMTP_HOST'];
            $this->phpMailer->Port = $phpMailerConfiguration['SMTP_PORT'];

            if ($phpMailerConfiguration['SMTP_SECURE'])
            {
                $this->phpMailer->SMTPSecure = $phpMailerConfiguration['SMTP_SECURE'];
            }

            if ($phpMailerConfiguration['SMTP_AUTH'])
            {
                $this->phpMailer->SMTPAuth = 1;
                $this->phpMailer->Username = $phpMailerConfiguration['SMTP_USER'];
                $this->phpMailer->Password = $phpMailerConfiguration['SMTP_PASS'];
            }

            $this->phpMailer->Priority = 3;
            $this->phpMailer->addCustomHeader('Errors-To: ' . $phpMailerConfiguration['SMTP_FROM_EMAIL']);

            $this->phpMailer->SMTPKeepAlive = true;
        }
    }

    /**
     * Resets the mailer after sending each mail
     */
    protected function resetMailer()
    {
        $this->phpMailer->clearAllRecipients();
        $this->phpMailer->clearAttachments();
        $this->phpMailer->clearCustomHeaders();
        $this->phpMailer->clearReplyTos();
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function send(Mail $mail)
    {
        if (!$this->phpMailer->send())
        {
            $this->logMail($mail, MailLog::STATE_FAILED, $this->phpMailer->ErrorInfo);
            throw new RuntimeException('Could not send e-mail');
        }
        else
        {
            $this->logMail($mail);
        }
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Exception
     */
    protected function sendIndividually(Mail $mail)
    {
        $recipientsFailed = [];

        foreach ($mail->getTo() as $recipient)
        {
            $this->phpMailer->addAddress($recipient, $recipient);

            try
            {
                $this->send($mail);
            }
            catch (Exception $ex)
            {
                $recipientsFailed[] = $recipient;
            }

            $this->phpMailer->clearAllRecipients();
        }

        if (count($recipientsFailed) > 0)
        {
            throw new Exception('Some mails could not be send (' . implode(', ', $recipientsFailed) . ')');
        }
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendMail(Mail $mail)
    {
        $this->addSenderInformation($mail);
        $this->addReplyInformation($mail);
        $this->addEmbeddedImages($mail);
        $this->addAttachments($mail);
        $this->addContent($mail);

        if (!$mail->getSendIndividually())
        {
            $this->addRecipients($mail);
            $this->send($mail);
        }
        else
        {
            $this->sendIndividually($mail);
        }

        $this->resetMailer();
    }
}
