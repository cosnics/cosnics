<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Libraries\Mail\ValueObject\Mail;

/**
 * Interface for mailer services
 *
 * @package Chamilo\Libraries\Mail\Mailer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface MailerInterface
{

    /**
     * Sends a single mail
     *
     * @param \Chamilo\Libraries\Mail\ValueObject\Mail $mail
     */
    public function sendMail(Mail $mail);

    /**
     * Sends multiple mails
     *
     * @param \Chamilo\Libraries\Mail\ValueObject\Mail[] $mails
     */
    public function sendMails($mails = []);
}