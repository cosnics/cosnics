<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;

class AccessReportRequestParameters extends StrikePlagiarismRequestParameters
{
    #[SerializedName('documentId')]
    protected string $documentId;
    #[SerializedName('viewOnly')]
    protected bool $viewOnly;

    #[SerializedName('expirationRedirection')]
    protected string $expirationRedirection;

    public function getDocumentId(): string
    {
        return $this->documentId;
    }

    public function setDocumentId(string $documentId): AccessReportRequestParameters
    {
        $this->documentId = $documentId;
        return $this;
    }

    public function getViewOnly(): bool
    {
        return $this->viewOnly;
    }

    public function setViewOnly(bool $viewOnly): AccessReportRequestParameters
    {
        $this->viewOnly = $viewOnly;
        return $this;
    }

    public function getExpirationRedirection(): string
    {
        return $this->expirationRedirection;
    }

    public function setExpirationRedirection(string $expirationRedirection): AccessReportRequestParameters
    {
        $this->expirationRedirection = $expirationRedirection;
        return $this;
    }

}