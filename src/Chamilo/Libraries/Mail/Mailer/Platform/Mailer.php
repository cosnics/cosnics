<?php
namespace Chamilo\Libraries\Mail\Mailer\Platform;

use Chamilo\Configuration\Storage\DataClass\MailLog;
use Chamilo\Libraries\Mail\Mailer\AbstractMailer;
use Chamilo\Libraries\Mail\ValueObject\Mail;

/**
 * Default platform mailer
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Mailer extends AbstractMailer
{

    /**
     * Sends a single mail
     * 
     * @param Mail $mail
     *
     * @throws \RuntimeException
     */
    public function sendMail(Mail $mail)
    {
        $headers = array();
        
        $cc = $mail->getCc();
        if (! empty($cc))
        {
            $headers[] = 'Cc: ' . implode(', ', $cc);
        }
        
        $bcc = $mail->getBcc();
        if (! empty($bcc))
        {
            $headers[] = 'Bcc: ' . implode(', ', $bcc);
        }
        
        $headers[] = 'From: ' . $this->determineFromEmail($mail);
        $headers[] = 'Reply-To: ' . $this->determineReplyEmail($mail);
        $headers[] = 'Content-type: text/html; charset="utf8"';
        
        $headers = implode(PHP_EOL, $headers);
        
        if ($mail->getSendIndividually())
        {
            foreach ($mail->getTo() as $recipient)
            {
                $this->send($mail, $recipient, $headers);
            }
        }
        else
        {
            $this->send($mail, implode(',', $mail->getTo()), $headers);
        }
    }

    /**
     * Sends the actual mail to the given recipients
     * 
     * @param Mail $mail
     * @param string $recipients
     * @param array $headers
     */
    protected function send(Mail $mail, $recipients, $headers = array())
    {
        if (! mail($recipients, $mail->getSubject(), $mail->getMessage(), $headers))
        {
            $this->logMail($mail, MailLog::STATE_FAILED);
            throw new \RuntimeException('Could not send e-mail');
        }
        
        $this->logMail($mail);
    }
}
