<?php
namespace Chamilo\Libraries\Mail\ValueObject;

use InvalidArgumentException;

/**
 * Describes the content and metadata for an e-mail
 *
 * @package Chamilo\Libraries\Mail\ValueObject
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Mail
{

    /**
     * The name of sender of the mail
     *
     * @var string
     */
    protected $fromName;

    /**
     * The email address of the sender of the mail
     *
     * @var string
     */
    protected $fromEmail;

    /**
     * The name to which a receiver of the mail can reply to
     *
     * @var string
     */
    protected $replyName;

    /**
     * The name to which a receiver of the mail can reply to
     *
     * @var string
     */
    protected $replyEmail;

    /**
     * Array of receiver email addresses in the TO field of the mail
     *
     * @var string[]
     */
    protected $to;

    /**
     * Whether or not this mail should be send individually to the target users or not
     *
     * @var string[]
     */
    protected $sendIndividually;

    /**
     * Array of receiver email addresses in the CC field of the mail
     *
     * @var string[]
     */
    protected $cc;

    /**
     * Array of receiver email addresses in the BCC field of the mail
     *
     * @var string[]
     */
    protected $bcc;

    /**
     * The subject of the mail
     *
     * @var string[]
     */
    protected $subject;

    /**
     * The message of the mail
     *
     * @var string
     */
    protected $message;

    /**
     * The embedded images
     *
     * @var \Chamilo\Libraries\Mail\ValueObject\MailFile[]
     */
    protected $embeddedImages;

    /**
     * The attachments
     *
     * @var MailFile[]
     */
    protected $attachments;

    /**
     * Constructor
     *
     * @param string $subject
     * @param string $message
     * @param string[] $to
     * @param boolean $sendIndividually
     * @param string[] $cc
     * @param string[] $bcc
     * @param string $fromName
     * @param string $fromEmail
     * @param string $replyName
     * @param string $replyEmail
     * @param \Chamilo\Libraries\Mail\ValueObject\MailFile[] $embeddedImages
     * @param \Chamilo\Libraries\Mail\ValueObject\MailFile[] $attachments
     */
    public function __construct(
        $subject, $message, $to = [], $sendIndividually = true, $cc = [], $bcc = [], $fromName = null,
        $fromEmail = null, $replyName = null, $replyEmail = null, $embeddedImages = [], $attachments = []
    )
    {
        $this->subject = $subject;
        $this->message = $message;

        $this->fromName = $fromName;
        $this->fromEmail = $fromEmail;
        $this->replyName = $replyName;
        $this->replyEmail = $replyEmail;
        $this->embeddedImages = $embeddedImages;
        $this->attachments = $attachments;

        $this->setRecipients($sendIndividually, $to, $cc, $bcc);
    }

    /**
     *
     * @return \Chamilo\Libraries\Mail\ValueObject\MailFile[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     *
     * @return \string[]
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     *
     * @return \string[]
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     *
     * @return \Chamilo\Libraries\Mail\ValueObject\MailFile[]
     */
    public function getEmbeddedImages()
    {
        return $this->embeddedImages;
    }

    /**
     *
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @return string
     */
    public function getReplyEmail()
    {
        return $this->replyEmail;
    }

    /**
     *
     * @return string
     */
    public function getReplyName()
    {
        return $this->replyName;
    }

    /**
     *
     * @return \string[]
     */
    public function getSendIndividually()
    {
        return $this->sendIndividually;
    }

    /**
     *
     * @return \string[]
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     *
     * @return \string[]
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Validates and sets the recipients
     *
     * @param boolean $sendIndividually
     * @param string[] $to
     * @param string[] $cc
     * @param string[] $bcc
     */
    protected function setRecipients($sendIndividually = false, $to = [], $cc = [], $bcc = [])
    {
        if ($sendIndividually && (!empty($cc) || !empty($bcc)))
        {
            throw new InvalidArgumentException(
                'A mail that is set to send individually to the target users should not include cc or bcc recipients'
            );
        }

        $this->to = is_array($to) ? $to : array($to);
        $this->cc = is_array($cc) ? $cc : array($cc);
        $this->bcc = is_array($bcc) ? $bcc : array($bcc);
        $this->sendIndividually = $sendIndividually;
    }
}
