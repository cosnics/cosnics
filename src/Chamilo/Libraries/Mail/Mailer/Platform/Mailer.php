<?php
namespace Chamilo\Libraries\Mail\Mailer\Platform;

use Chamilo\Configuration\Storage\DataClass\MailLog;
use Chamilo\Libraries\Mail\Mailer\AbstractMailer;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use RuntimeException;

/**
 * @package Chamilo\Libraries\Mail\Mailer\Platform
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Mailer extends AbstractMailer
{

    protected function send(Mail $mail, string $recipients, string $headers)
    {
        if (!mail($recipients, $mail->getSubject(), $mail->getMessage(), $headers))
        {
            $this->logMail($mail, MailLog::STATE_FAILED);
            throw new RuntimeException('Could not send e-mail');
        }

        $this->logMail($mail);
    }

    /**
     * @throws \RuntimeException
     */
    public function sendMail(Mail $mail)
    {
        $headers = [];

        $cc = $mail->getCc();
        if (!empty($cc))
        {
            $headers[] = 'Cc: ' . implode(', ', $cc);
        }

        $bcc = $mail->getBcc();
        if (!empty($bcc))
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
}
