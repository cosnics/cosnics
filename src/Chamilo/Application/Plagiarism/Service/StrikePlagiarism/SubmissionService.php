<?php

namespace Chamilo\Application\Plagiarism\Service\StrikePlagiarism;

use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\DocumentMetadata;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\AccessReportRequestParameters;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\UploadDocumentRequestParameters;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\UploadDocumentResponse;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Repository\StrikePlagiarismRepository;
use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\UUID;
use Hogent\Extension\Chamilo\Application\Plagiarism\Service\UserConverter;

class SubmissionService
{
    protected StrikePlagiarismRepository $strikePlagiarismRepository;
    protected WebhookManager $webhookManager;

    protected UserConverter $userConverter;

    public function __construct(StrikePlagiarismRepository $strikePlagiarismRepository, WebhookManager $webhookManager, UserConverter $userConverter)
    {
        $this->strikePlagiarismRepository = $strikePlagiarismRepository;
        $this->webhookManager = $webhookManager;
        $this->userConverter = $userConverter;
    }

    public function uploadDocument(
        User $submitter, User $owner, string $title, string $filePath, string $filename
    ): string
    {
        $uploadDocumentRequestParameters = new UploadDocumentRequestParameters();

        $documentId = UUID::v4();

        $submitterId = $this->userConverter->convertUserToId($owner);
        //var_dump($this->webhookManager->getWebhookUrlForDocumentId('839b1a06-20d1-4c5a-a781-3800116612bf')); exit;
        $uploadDocumentRequestParameters->setLanguageCode('nl')
            ->setAction(UploadDocumentRequestParameters::ACTION_CHECK)
            ->setCallback($this->webhookManager->getWebhookUrlForDocumentId($documentId))
            //    ->setCallback('https://webhook.site/8fbce4a4-4e39-4bcf-a067-7f234e244eff')
            ->setId($documentId)
            ->setAiDetection('false')
            ->setTitle($title)
            ->setAuthor($owner->get_fullname())
            ->setCoordinator($submitter->get_fullname())
            ->setDocumentKind(6)
            ->setUserId($submitterId);

        $response = $this->strikePlagiarismRepository->uploadDocument($uploadDocumentRequestParameters, $filename, $filePath);
        if($response->hasError())
        {
            throw new PlagiarismException('The document could not be uploaded');
        }

        return $response->getId();
    }

    public function getDocumentMetadata(string $documentId): DocumentMetadata
    {
        return $this->strikePlagiarismRepository->getDocumentMetadata($documentId);
    }

    public function getViewReportToken(string $documentId): string
    {
        $accessReportRequestParameters = new AccessReportRequestParameters();
        $accessReportRequestParameters->setDocumentId($documentId);
        $accessReportRequestParameters->setViewOnly(true);

        return $this->strikePlagiarismRepository->getViewReportToken($accessReportRequestParameters);
    }

    public function addDocumentToReferenceDatabase(string $documentId)
    {
        return $this->strikePlagiarismRepository->addDocumentToReferenceDatabase($documentId);
    }

    public function removeDocumentFromReferenceDatabase(string $documentId)
    {
        return $this->strikePlagiarismRepository->removeDocumentFromReferenceDatabase($documentId);
    }

}