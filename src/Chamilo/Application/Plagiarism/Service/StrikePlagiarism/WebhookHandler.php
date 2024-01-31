<?php

namespace Chamilo\Application\Plagiarism\Service\StrikePlagiarism;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventNotifier;

class WebhookHandler
{
    protected WebhookManager $webhookManager;
    protected PlagiarismEventNotifier $plagiarismEventNotifier;
    protected SubmissionService $submissionService;

    public function __construct(WebhookManager $webhookManager, PlagiarismEventNotifier $plagiarismEventNotifier, SubmissionService $submissionService)
    {
        $this->webhookManager = $webhookManager;
        $this->plagiarismEventNotifier = $plagiarismEventNotifier;
        $this->submissionService = $submissionService;
    }


    public function handleWebhookRequest(string $documentId, string $signature): void
    {
        if (!$this->webhookManager->validateSignature($documentId, $signature))
        {
            throw new PlagiarismException('The given signature is not correct');
        }

        $submissionStatus = new SubmissionStatus(
            $documentId, SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS
        );

        $documentMetadata = $this->submissionService->getDocumentMetadata($documentId);

        $submissionStatus = new SubmissionStatus(
            $documentId, SubmissionStatus::STATUS_REPORT_GENERATED,
            round($documentMetadata->getFactor1() * 100)
        );

        $this->plagiarismEventNotifier->submissionStatusChanged($submissionStatus);
    }
}