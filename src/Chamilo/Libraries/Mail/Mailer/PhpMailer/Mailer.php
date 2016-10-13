<?php
namespace Chamilo\Libraries\Mail\Mailer\PhpMailer;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\MailLog;
use Chamilo\Libraries\Mail\Mailer\AbstractMailer;
use Chamilo\Libraries\Mail\ValueObject\Mail;

/**
 * PHPMailer mailer service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Mailer extends AbstractMailer
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var \PHPMailer
     */
    protected $phpMailer;

    /**
     * Mailer constructor.
     *
     * @param Configuration $configuration
     * @param \PHPMailer $phpMailer
     */
    public function __construct(Configuration $configuration = null, \PHPMailer $phpMailer = null)
    {
        parent::__construct($configuration);

        $this->setPHPMailer($phpMailer);
    }

    /**
     * Sends a single mail
     *
     * @param Mail $mail
     */
    public function sendMail(Mail $mail)
    {
        $this->addSenderInformation($mail);
        $this->addReplyInformation($mail);
        $this->addEmbeddedImages($mail);
        $this->addAttachments($mail);
        $this->addContent($mail);

        if(!$mail->getSendIndividually())
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

    /**
     * Adds the information about the sender
     *
     * @param Mail $mail
     */
    protected function addSenderInformation(Mail $mail)
    {
        $this->phpMailer->From = $this->determineFromEmail($mail);
        $this->phpMailer->Sender = $this->phpMailer->From;
        $this->phpMailer->FromName = $this->determineFromName($mail);
    }

    /**
     * Adds optionally reply information
     *
     * @param Mail $mail
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
     * Adds the embedded images
     *
     * @param Mail $mail
     */
    protected function addEmbeddedImages(Mail $mail)
    {
        foreach ($mail->getEmbeddedImages() as $index => $mailFile)
        {
            $this->phpMailer->addEmbeddedImage(
                $mailFile->getPath(),
                $index,
                $mailFile->getFilename(),
                'base64',
                $mailFile->getMimeType()
            );
        }
    }

    /**
     * Adds the attachments
     *
     * @param Mail $mail
     *
     * @throws \phpmailerException
     */
    protected function addAttachments(Mail $mail)
    {
        foreach ($mail->getAttachments() as $mailFile)
        {
            $this->phpMailer->addAttachment($mailFile->getPath(), $mailFile->getFilename());
        }
    }

    /**
     * Adds the content
     *
     * @param Mail $mail
     */
    protected function addContent(Mail $mail)
    {
        $this->phpMailer->Body = $mail->getMessage();
        $this->phpMailer->Subject = $mail->getSubject();
    }

    /**
     * Adds the recipients
     *
     * @param Mail $mail
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
     * Sends the actual mail
     *
     * @param Mail $mail
     *
     * @throws \phpmailerException
     */
    protected function send(Mail $mail)
    {
        if (!$this->phpMailer->send())
        {
            $this->logMail($mail, MailLog::STATE_FAILED, $this->phpMailer->ErrorInfo);
            throw new \RuntimeException('Could not send e-mail');
        }
        else
        {
            $this->logMail($mail);
        }
    }

    /**
     * Sends the mail individually
     *
     * @param Mail $mail
     *
     * @throws \Exception
     */
    protected function sendIndividually(Mail $mail)
    {
        $recipientsFailed = array();

        foreach($mail->getTo() as $recipient)
        {
            $this->phpMailer->addAddress($recipient, $recipient);

            try
            {
                $this->send($mail);
            }
            catch(\Exception $ex)
            {
                $recipientsFailed[] = $recipient;
            }

            $this->phpMailer->clearAllRecipients();
        }

        if(count($recipientsFailed) >  0)
        {
            throw new \Exception('Some mails could not be send (' . implode(', ', $recipientsFailed) . ')');
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
     * Sets and optionally initializes the phpmailer
     *
     * @param \PHPMailer $phpMailer
     */
    protected function setPHPMailer(\PHPMailer $phpMailer = null)
    {
        if (!isset($phpMailer) || !$phpMailer instanceof \PHPMailer)
        {
            global $phpMailerConfiguration;
            require_once(\Chamilo\Libraries\File\Path :: getInstance()->getStoragePath() . 'configuration/phpmailer.conf.php');

            $phpMailer = new \PHPMailer();

            $phpMailer->isHTML(true);
            $phpMailer->CharSet = 'utf-8';
            $phpMailer->Mailer = $phpMailerConfiguration['SMTP_MAILER'];
            $phpMailer->Host = $phpMailerConfiguration['SMTP_HOST'];
            $phpMailer->Port = $phpMailerConfiguration['SMTP_PORT'];

            if ($phpMailerConfiguration['SMTP_AUTH'])
            {
                $phpMailer->SMTPAuth = 1;
                $phpMailer->Username = $phpMailerConfiguration['SMTP_USER'];
                $phpMailer->Password = $phpMailerConfiguration['SMTP_PASS'];
            }

            $phpMailer->Priority = 3;
            $phpMailer->addCustomHeader('Errors-To: ' . $phpMailerConfiguration['SMTP_FROM_EMAIL']);

            $phpMailer->SMTPKeepAlive = true;
        }

        $this->phpMailer = $phpMailer;
    }
}
