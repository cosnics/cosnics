<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;

class DocumentIndexRequestParameters
{
    #[SerializedName('id')]
    protected string $documentId;

    public function getDocumentId(): string
    {
        return $this->documentId;
    }

    public function setDocumentId(string $documentId): DocumentIndexRequestParameters
    {
        $this->documentId = $documentId;
        return $this;
    }
}