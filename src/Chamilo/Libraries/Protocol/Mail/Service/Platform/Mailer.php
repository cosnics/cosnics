<?php
namespace Chamilo\Libraries\Protocol\Mail\Service\Platform;

use Chamilo\Libraries\Protocol\Mail\Architecture\Domain\Mail;
use Chamilo\Libraries\Protocol\Mail\Service\AbstractMailer;
use RuntimeException;

/**
 * @package Chamilo\Libraries\Protocol\Mail\Service\Platform
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Mailer extends AbstractMailer
{
    protected function send(Mail $mail, string $recipients, string $headers): void
    {
        if (!mail($recipients, $mail->getSubject(), $mail->getMessage(), $headers)) {
            throw new RuntimeException('Could not send e-mail');
        }
    }

    public function sendMail(Mail $mail): void
    {
        $headers = [];

        $cc = $mail->getCc();
        if (!empty($cc)) {
            $headers[] = 'Cc: ' . implode(', ', $cc);
        }

        $bcc = $mail->getBcc();
        if (!empty($bcc)) {
            $headers[] = 'Bcc: ' . implode(', ', $bcc);
        }

        $headers[] = 'From: ' . $this->determineFromEmail($mail);
        $headers[] = 'Reply-To: ' . $this->determineReplyEmail($mail);
        $headers[] = 'Content-type: text/html; charset="utf8"';

        $headers = implode(PHP_EOL, $headers);

        if ($mail->getSendIndividually()) {
            foreach ($mail->getTo() as $recipient) {
                $this->send($mail, $recipient, $headers);
            }
        }
        else {
            $this->send($mail, implode(',', $mail->getTo()), $headers);
        }
    }
}
