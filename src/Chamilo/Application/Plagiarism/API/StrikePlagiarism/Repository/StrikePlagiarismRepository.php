<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Repository;

use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\DocumentMetadata;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\AccessReportRequestParameters;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\DocumentIndexRequestParameters;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\GetDocumentMetadataRequestParameters;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\UploadDocumentRequestParameters;
use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\UploadDocumentResponse;
use Chamilo\Libraries\Protocol\REST\RestClient;
use Chamilo\Libraries\Protocol\REST\RestRequest;

class StrikePlagiarismRepository
{
    protected RestClient $restClient;

    public function __construct(RestClient $restClient)
    {
        $this->restClient = $restClient;
    }

    public function uploadDocument(UploadDocumentRequestParameters $uploadDocumentRequestParameters): UploadDocumentResponse
    {
        $restRequest = new RestRequest(
            RestRequest::METHOD_POST, 'api/v2/documents/add', UploadDocumentResponse::class,
            bodyObject: $uploadDocumentRequestParameters
        );

        return $this->restClient->executeRequest($restRequest);
    }

    public function getDocumentMetadata(string $documentId): DocumentMetadata
    {
        $documentMetadataRequestParameters = new GetDocumentMetadataRequestParameters();
        $documentMetadataRequestParameters->setDocumentId($documentId);

        $restRequest = new RestRequest(
            RestRequest::METHOD_POST, 'api/v2/documents', DocumentMetadata::class, bodyObject: $documentMetadataRequestParameters
        );

        return $this->restClient->executeRequest($restRequest);
    }

    public function getReportUrl(AccessReportRequestParameters $accessReportRequestParameters): string
    {
        $restRequest = new RestRequest(RestRequest::METHOD_POST, 'report/api/token', bodyObject: $accessReportRequestParameters);

        return $this->restClient->executeRequest($restRequest);
    }

    public function addDocumentToReferenceDatabase(string $documentId)
    {
        $documentIndexRequestParameters = new DocumentIndexRequestParameters();
        $documentIndexRequestParameters->setDocumentId($documentId);

        $restRequest = new RestRequest(RestRequest::METHOD_POST, 'api/v2/documents/add-to-database', bodyObject: $documentIndexRequestParameters);

        return $this->restClient->executeRequest($restRequest);
    }

    public function removeDocumentFromReferenceDatabase(string $documentId)
    {
        $documentIndexRequestParameters = new DocumentIndexRequestParameters();
        $documentIndexRequestParameters->setDocumentId($documentId);

        $restRequest = new RestRequest(RestRequest::METHOD_POST, 'api/v2/documents/remove-from-database', bodyObject: $documentIndexRequestParameters);

        return $this->restClient->executeRequest($restRequest);
    }
}