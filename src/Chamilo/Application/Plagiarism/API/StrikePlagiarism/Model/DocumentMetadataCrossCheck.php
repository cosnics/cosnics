<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

class DocumentMetadataCrossCheck
{
    #[SerializedName('documentTitle')]
    protected string $documentTitle;
    #[SerializedName('documentAuthor')]
    protected string $documentAuthor;
    protected double $similarity;

    public function getDocumentTitle(): string
    {
        return $this->documentTitle;
    }

    public function setDocumentTitle(string $documentTitle): DocumentMetadataCrossCheck
    {
        $this->documentTitle = $documentTitle;
        return $this;
    }

    public function getDocumentAuthor(): string
    {
        return $this->documentAuthor;
    }

    public function setDocumentAuthor(string $documentAuthor): DocumentMetadataCrossCheck
    {
        $this->documentAuthor = $documentAuthor;
        return $this;
    }

    public function getSimilarity(): float
    {
        return $this->similarity;
    }

    public function setSimilarity(float $similarity): DocumentMetadataCrossCheck
    {
        $this->similarity = $similarity;
        return $this;
    }



}