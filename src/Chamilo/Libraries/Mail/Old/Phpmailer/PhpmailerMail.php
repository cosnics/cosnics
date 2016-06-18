<?php
namespace Chamilo\Libraries\Mail\Phpmailer;

use Chamilo\Configuration\Storage\DataClass\MailLog;
use Chamilo\Libraries\Mail\Mail;
use PHPMailer;

/**
 * $Id: phpmailer_mail.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.mail.phpmailer
 */
/**
 * This class implements the abstract Mail class and uses the phpmailer project to send the emails.
 */
class PhpmailerMail extends Mail
{

    public function send()
    {
        global $phpMailerConfiguration;
        require_once (__DIR__ . '/phpmailer.conf.php');
        $mail = new PHPMailer();
        $mail->isHtml(true);
        $mail->CharSet = 'utf-8';
        $mail->Mailer = $phpMailerConfiguration['SMTP_MAILER'];
        $mail->Host = $phpMailerConfiguration['SMTP_HOST'];
        $mail->Port = $phpMailerConfiguration['SMTP_PORT'];
        if ($phpMailerConfiguration['SMTP_AUTH'])
        {
            $mail->SMTPAuth = 1;
            $mail->Username = $phpMailerConfiguration['SMTP_USER'];
            $mail->Password = $phpMailerConfiguration['SMTP_PASS'];
        }

        $mail->Priority = 3; // 5=low, 1=high
        $mail->AddCustomHeader('Errors-To: ' . $phpMailerConfiguration['SMTP_FROM_EMAIL']);

        $mail->SMTPKeepAlive = true;

        if (! is_null($this->get_from()))
        {
            $mail->From = $this->get_from_email();
            $mail->Sender = $this->get_from_email();
            $mail->FromName = $this->get_from_name();
            $mail->AddCustomHeader('Return-Path: <' . $this->get_from_email() . '>');
        }

        if (! is_null($this->get_reply()))
        {

            $mail->AddReplyTo($this->get_reply_email(), $this->get_reply_name());
            $mail->AddCustomHeader('Return-Path: <' . $this->get_reply_email() . '>');
        }
        else
        {
            $mail->From = $phpMailerConfiguration['SMTP_FROM_EMAIL'];
            $mail->Sender = $phpMailerConfiguration['SMTP_FROM_EMAIL'];
            $mail->FromName = $phpMailerConfiguration['SMTP_FROM_NAME'];
        }

        foreach ($this->get_embedded_images() as $index => $mail_embedded_object)
        {
            $mail->AddEmbeddedImage(
                $mail_embedded_object->get_path(),
                $index,
                $mail_embedded_object->get_filename(),
                'base64',
                $mail_embedded_object->get_mime_type());
        }

        foreach ($this->get_attachments() as $fn => $alias)
        {
            $mail->AddAttachment($fn, $alias);
        }

        $mail->Body = $this->get_message();
        $mail->Subject = $this->get_subject();

        foreach ($this->get_to() as $index => $recipient)
        {
            $mail->AddAddress($recipient, $recipient);
        }

        foreach ($this->get_bcc() as $recipient)
        {
            $mail->AddBCC($recipient, $recipient);
        }

        $log = new MailLog();
        $log->set_sender($mail->From);
        $log->set_recipient($recipient);
        $log->set_date(time());
        $log->set_subject($mail->Subject);
        $log->set_host(gethostname());

        if (! $mail->Send())
        {
            $log->set_state(MailLog :: STATE_FAILED);
            $log->set_message($mail->ErrorInfo);
            $log->create();
            return false;
        }
        else
        {
            $log->set_state(MailLog :: STATE_SUCCESSFUL);
            $log->create();
        }

        $mail->ClearAddresses();
        return true;
    }
}
