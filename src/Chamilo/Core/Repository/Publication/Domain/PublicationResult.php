<?php
namespace Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Core\Repository\Publication\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationResult
{
    public const STATUS_FAILURE = 2;
    public const STATUS_SUCCESS = 1;

    private string $message;

    private int $status;

    private ?string $url;

    public function __construct(int $status, string $message, ?string $url = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->url = $url;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function setUrl(?string $url)
    {
        $this->url = $url;
    }
}