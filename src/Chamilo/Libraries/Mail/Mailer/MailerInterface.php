<?php
namespace Chamilo\Libraries\Mail\Mailer;

/**
 * Interface for mailer services
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
    public function sendMail(\Chamilo\Libraries\Mail\ValueObject\Mail $mail);

    /**
     * Sends multiple mails
     * 
     * @param \Chamilo\Libraries\Mail\ValueObject\Mail[] $mails
     */
    public function sendMails($mails = array());
}