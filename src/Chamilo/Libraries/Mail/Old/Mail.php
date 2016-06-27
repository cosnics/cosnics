<?php
namespace Chamilo\Libraries\Mail;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: mail.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.mail
 */
/**
 * An abstract class for sending emails.
 * Implement new mail methods by creating a class which extends this abstract
 * class.
 *
 * @todo : Add functionality for extra headers, names of receivers & sender, maybe HTML email and attachments?
 */
abstract class Mail
{
    const NAME = 'name';
    const EMAIL = 'email';

    /**
     * The sender of the mail An array containing the name AND the e-mail address
     */
    private $from;

    /**
     * The reply address of the mail An array containing the name AND the e-mail address
     */
    private $reply;

    /**
     * Array of receivers in the TO field of the mail
     */
    private $to;

    /**
     * Array of receivers in the CC field of the mail
     */
    private $cc;

    /**
     * Array of receivers in the BCC field of the mail
     */
    private $bcc;

    /**
     * The subject of the mail
     */
    private $subject;

    /**
     * The message of the mail
     */
    private $message;

    /**
     * The embedded images
     *
     * @var MailEmbeddedObject[]
     */
    private $embedded_images;

    private $attachments;

    /**
     * Constructor
     */
    public function __construct($subject, $message, $to, $from = null, $cc = array (), $bcc = array ())
    {
        $this->subject = $subject;
        $this->message = $message;

        if (! is_array($to))
        {
            $to = array($to);
        }

        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->from = $from;
        $this->embedded_images = array();
        $this->attachments = array();
    }

    /**
     * Adds an image to be embedded in this email and returns its identification for use in img tags.
     *
     * @param MailEmbeddedObject[] $object
     *
     * @return int - the index of the embedded image
     */
    public function add_embedded_image($object)
    {
        $this->embedded_images[] = $object;
        return count($this->embedded_images) - 1;
    }

    /**
     * Adds an attachment to the mail with an alias (short name).
     *
     * @param string $filename The path and filename of the attachment.
     * @param string $alias
     */
    public function add_attachment($filename, $alias)
    {
        $this->attachments[$filename] = $alias;
    }

    /**
     * Create a new mail instance.
     *
     * @todo This function now uses the DefaultMail-class. The class to use should be configurable.
     */
    public static function factory($subject, $message, $to, $from = array(), $cc = array(), $bcc = array())
    {
        // TODO: This value should come from configuration and can be one of the available mail-implementations
        $mail_file = 'phpmailer';
        $package = (string) StringUtilities :: getInstance()->createString($mail_file)->upperCamelize();

        $mail_class = __NAMESPACE__ . '\\' . $package . '\\' . $package . 'Mail';
        return new $mail_class($subject, $message, $to, $from, $cc, $bcc);
    }

    public function get_attachments()
    {
        return $this->attachments;
    }

    public function get_embedded_images()
    {
        return $this->embedded_images;
    }

    /**
     * Retrieves the subject for the email
     *
     * @return string
     */
    public function get_subject()
    {
        return $this->subject;
    }

    /**
     * Retrieves the message for the email
     *
     * @return string
     */
    public function get_message()
    {
        return $this->message;
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    /**
     * Retrieves the receiver(s) in the TO-field of the email
     *
     * @return array
     */
    public function get_to()
    {
        return $this->to;
    }

    public function set_to($to)
    {
        if (! is_array($to))
        {
            $to = array($to);
        }
        $this->to = $to;
    }

    /**
     * Retrieves the receiver(s) in the CC-field of the email
     *
     * @return array
     */
    public function get_cc()
    {
        return $this->cc;
    }

    /**
     * Retrieves the receiver(s) in the BCC-field of the email
     *
     * @return array
     */
    public function get_bcc()
    {
        return $this->bcc;
    }

    /**
     * Sets the bcc receivers
     *
     * @param string[] $bcc
     */
    public function set_bcc($bcc)
    {
        $this->bcc = $bcc;
    }

    /**
     * Retrieves the sender of the email
     *
     * @return array
     */
    public function get_from()
    {
        return $this->from;
    }

    public function get_from_name()
    {
        return $this->from[self :: NAME];
    }

    public function get_from_email()
    {
        return $this->from[self :: EMAIL];
    }

    /**
     * Retrieves the reply-to of the email
     *
     * @return array
     */
    public function get_reply()
    {
        return $this->from;
    }

    public function set_reply($reply)
    {
        return $this->reply = $reply;
    }

    public function get_reply_name()
    {
        return $this->reply[self :: NAME];
    }

    public function get_reply_email()
    {
        return $this->reply[self :: EMAIL];
    }

    /**
     * Send the email
     *
     * @return boolean True if the mail was successfully sent, false if not.
     */
    abstract public function send();
}
