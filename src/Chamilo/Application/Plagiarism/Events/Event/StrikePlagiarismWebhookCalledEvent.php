<?php


namespace Chamilo\Application\Plagiarism\Events\Event;

use Symfony\Contracts\EventDispatcher\Event;

class StrikePlagiarismWebhookCalledEvent extends Event
{
    protected string $callbackUrl;
    protected ?string $signature;
    protected ?string $documentId;

    public function __construct(string $callbackUrl, ?string $signature, ?string $documentId)
    {
        $this->callbackUrl = $callbackUrl;
        $this->signature = $signature;
        $this->documentId = $documentId;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function getDocumentId(): ?string
    {
        return $this->documentId;
    }
}