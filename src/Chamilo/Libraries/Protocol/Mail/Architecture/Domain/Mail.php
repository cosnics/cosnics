<?php
namespace Chamilo\Libraries\Protocol\Mail\Architecture\Domain;

use InvalidArgumentException;

/**
 * Describes the content and metadata for an e-mail
 *
 * @package Chamilo\Libraries\Protocol\Mail\Architecture\Domain
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Mail
{
    /**
     * The attachments
     *
     * @var MailFile[]
     */
    protected array $attachments;

    /**
     * Array of receiver email addresses in the BCC field of the mail
     *
     * @var string[]
     */
    protected array $bcc;

    /**
     * Array of receiver email addresses in the CC field of the mail
     *
     * @var string[]
     */
    protected array $cc;

    /**
     * The embedded images
     *
     * @var \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[]
     */
    protected array $embeddedImages;

    protected ?string $fromEmail;

    protected ?string $fromName;

    protected string $message;

    protected ?string $replyEmail;

    protected ?string $replyName;

    /**
     * Whether this mail should be sent individually to the target users or not
     */
    protected bool $sendIndividually;

    protected string $subject;

    protected array $to;

    /**
     * @param string[] $to
     * @param string[] $cc
     * @param string[] $bcc
     * @param \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[] $embeddedImages
     * @param \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[] $attachments
     */
    public function __construct(
        string $subject, string $message, array $to = [], bool $sendIndividually = true, array $cc = [],
        array $bcc = [], ?string $fromName = null, ?string $fromEmail = null, ?string $replyName = null,
        ?string $replyEmail = null, array $embeddedImages = [], array $attachments = []
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
     * @return \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return \string[]
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @return \string[]
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[]
     */
    public function getEmbeddedImages(): array
    {
        return $this->embeddedImages;
    }

    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getReplyEmail(): ?string
    {
        return $this->replyEmail;
    }

    public function getReplyName(): ?string
    {
        return $this->replyName;
    }

    public function getSendIndividually(): bool
    {
        return $this->sendIndividually;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param string[] $to
     * @param string[] $cc
     * @param string[] $bcc
     */
    protected function setRecipients(bool $sendIndividually = false, array $to = [], array $cc = [], array $bcc = []
    ): void
    {
        if ($sendIndividually && (!empty($cc) || !empty($bcc))) {
            throw new InvalidArgumentException(
                'A mail that is set to send individually to the target users should not include cc or bcc recipients'
            );
        }

        $this->to = is_array($to) ? $to : [$to];
        $this->cc = is_array($cc) ? $cc : [$cc];
        $this->bcc = is_array($bcc) ? $bcc : [$bcc];
        $this->sendIndividually = $sendIndividually;
    }
}
