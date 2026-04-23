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
    protected array $blindCarbonCopies;

    /**
     * Array of receiver email addresses in the CC field of the mail
     *
     * @var string[]
     */
    protected array $carbonCopies;

    /**
     * The embedded images
     *
     * @var \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[]
     */
    protected array $embeddedImages;

    protected ?string $fromEmail;

    protected ?string $fromName;

    protected string $message;

    protected array $recipients;

    protected ?string $replyEmail;

    protected ?string $replyName;

    /**
     * Whether this mail should be sent individually to the target users or not
     */
    protected bool $sendIndividually;

    protected string $subject;

    /**
     * @param string[] $to
     * @param string[] $carbonCopies
     * @param string[] $blindCarbonCopies
     * @param \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[] $embeddedImages
     * @param \Chamilo\Libraries\Protocol\Mail\Architecture\Domain\MailFile[] $attachments
     */
    public function __construct(
        string $subject, string $message, array $to = [], bool $sendIndividually = true, array $carbonCopies = [],
        array $blindCarbonCopies = [], ?string $fromName = null, ?string $fromEmail = null, ?string $replyName = null,
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

        $this->setRecipients($sendIndividually, $to, $carbonCopies, $blindCarbonCopies);
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
    public function getBlindCarbonCopies(): array
    {
        return $this->blindCarbonCopies;
    }

    /**
     * @return \string[]
     */
    public function getCarbonCopies(): array
    {
        return $this->carbonCopies;
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

    /**
     * @return string[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param string[] $to
     * @param string[] $carbonCopies
     * @param string[] $blindCarbonCopies
     */
    protected function setRecipients(
        bool $sendIndividually = false, array $to = [], array $carbonCopies = [], array $blindCarbonCopies = []
    ): void
    {
        if ($sendIndividually && (!empty($carbonCopies) || !empty($blindCarbonCopies))) {
            throw new InvalidArgumentException(
                'A mail that is set to send individually to the target users should not include cc or bcc recipients'
            );
        }

        $this->recipients = is_array($to) ? $to : [$to];
        $this->carbonCopies = is_array($carbonCopies) ? $carbonCopies : [$carbonCopies];
        $this->blindCarbonCopies = is_array($blindCarbonCopies) ? $blindCarbonCopies : [$blindCarbonCopies];
        $this->sendIndividually = $sendIndividually;
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
}
