<?php

namespace Chamilo\Application\Plagiarism\Service\StrikePlagiarism;

use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\WebhookRequestData;
use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventNotifier;

class WebhookHandler
{
    protected WebhookManager $webhookManager;
    protected PlagiarismEventNotifier $plagiarismEventNotifier;

    public function __construct(WebhookManager $webhookManager, PlagiarismEventNotifier $plagiarismEventNotifier)
    {
        $this->webhookManager = $webhookManager;
        $this->plagiarismEventNotifier = $plagiarismEventNotifier;
    }


    public function handleWebhookRequest(WebhookRequestData $webhookRequestData, string $signature): void
    {
        if (!$this->webhookManager->validateSignature($webhookRequestData->getId(), $signature))
        {
            throw new PlagiarismException('The given signature is not correct');
        }

        $submissionStatus = new SubmissionStatus(
            $webhookRequestData->getId(), SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS
        );

        $documentMetadata = $this->submissionService->getDocumentMetadata($webhookRequestData->getId());
        if($documentMetadata->isChecked())
        {
            $submissionStatus = new SubmissionStatus(
                $webhookRequestData->getId(), SubmissionStatus::STATUS_REPORT_GENERATED,
                $documentMetadata->getFactor1()
            );
        }

        $this->plagiarismEventNotifier->submissionStatusChanged($submissionStatus);
    }
}