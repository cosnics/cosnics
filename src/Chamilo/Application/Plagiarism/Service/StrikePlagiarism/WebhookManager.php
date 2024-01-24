<?php

namespace Chamilo\Application\Plagiarism\Service\StrikePlagiarism;

use Chamilo\Application\Plagiarism\Component\StrikePlagiarismWebhookComponent;
use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\ConfigurationWriter;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

class WebhookManager
{
    protected ConfigurationConsulter $configurationConsulter;
    protected ConfigurationWriter $configurationWriter;

    public function __construct(
        ConfigurationConsulter $configurationConsulter,
        ConfigurationWriter $configurationWriter
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->configurationWriter = $configurationWriter;
    }

    public function getWebhookUrlForDocumentId(string $documentId): string
    {
        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => Manager::context(),
                Application::PARAM_ACTION => Manager::ACTION_STRIKEPLAGIARISM_WEBHOOK,
                StrikePlagiarismWebhookComponent::SIGNATURE => $this->calculateSignatureForDocumentId($documentId)
            ]
        );

        return $redirect->getUrl();
    }

    public function validateSignature(string $documentId, string $signature): bool
    {
        if(empty($signature))
            return false;

        $calculatedSignature = $this->calculateSignatureForDocumentId($documentId);
        return $calculatedSignature === $signature;
    }

    protected function calculateSignatureForDocumentId(string $documentId): string
    {
        return hash_hmac('sha256', $documentId, base64_decode($this->getOrCreateWebhookSecret()));
    }

    protected function storeWebhookSecret(string $webhookSecret): void
    {
        $this->configurationWriter->writeSetting(
            'Chamilo\Application\Plagiarism', 'strike_plagiarism_webhook_secret', $webhookSecret
        );
    }

    protected function getWebhookSecret(): ?string
    {
        return $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Plagiarism', 'strike_plagiarism_webhook_secret']
        );
    }

    protected function getOrCreateWebhookSecret(): string
    {
        $webhookSecret = $this->getWebhookSecret();
        if(empty($webhookSecret))
        {
            $webhookSecret = base64_encode(hash('sha256', uniqid()));
            $this->storeWebhookSecret($webhookSecret);
        }

        return $webhookSecret;
    }

}