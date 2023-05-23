<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Libraries\Mail\ValueObject\Mail;

/**
 * @package Chamilo\Libraries\Mail\Mailer
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface MailerInterface
{

    public function sendMail(Mail $mail);

    /**
     * @param \Chamilo\Libraries\Mail\ValueObject\Mail[] $mails
     */
    public function sendMails(array $mails = []);
}