<?php
namespace Chamilo\Libraries\Protocol\Mail\Architecture\Interface;

use Chamilo\Libraries\Protocol\Mail\Architecture\Domain\Mail;

/**
 * @package Chamilo\Libraries\Mail\Mailer
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface MailerInterface
{

    public function sendMail(Mail $mail): void;

    /**
     * @param \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\Mail[] $mails
     */
    public function sendMails(array $mails = []): void;
}