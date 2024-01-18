<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

class DocumentMetadata
{
    protected string $title;
    protected string $author;
    protected string $coordinator;
    #[SerializedName('reportReady')]
    protected bool $reportReady;
    protected bool $indexed;
    protected string $status;
    protected double $factor1;
    protected double $factor2;
    protected double $factor3;
    protected double $factor4;
    protected double $factor5;
    #[SerializedName('internetSourcesFactor')]
    protected double $internetSourcesFactor;
    #[SerializedName('localSourcesFactor')]
    protected double $localSourcesFactor;
    #[SerializedName('otherSourcesFactor')]
    protected double $otherSourcesFactor;
    #[SerializedName('legalSourcesFactor')]
    protected double $legalSourcesFactor;
    #[SerializedName('refbooksSourcesFactor')]
    protected double $refbooksSourcesFactor;
    #[SerializedName('reversedFactor1')]
    protected double $reversedFactor1;
    #[SerializedName('quotationsFactor')]
    protected double $quotationsFactor;
    #[SerializedName('foreignAlphabetAlert')]
    protected double $foreignAlphabetAlert;

    /**
     * @var DocumentMetadataCrossCheck[]
     */
    #[SerializedName('cross-checks')]
    protected array $crossChecks;
    #[SerializedName('aiDetectionFactor')]
    protected double $aiDetectionFactor;
    #[SerializedName('documentSize')]
    protected int $documentSize;
    protected string $md5sum;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): DocumentMetadata
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): DocumentMetadata
    {
        $this->author = $author;
        return $this;
    }

    public function getCoordinator(): string
    {
        return $this->coordinator;
    }

    public function setCoordinator(string $coordinator): DocumentMetadata
    {
        $this->coordinator = $coordinator;
        return $this;
    }

    public function isReportReady(): bool
    {
        return $this->reportReady;
    }

    public function setReportReady(bool $reportReady): DocumentMetadata
    {
        $this->reportReady = $reportReady;
        return $this;
    }

    public function isIndexed(): bool
    {
        return $this->indexed;
    }

    public function setIndexed(bool $indexed): DocumentMetadata
    {
        $this->indexed = $indexed;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): DocumentMetadata
    {
        $this->status = $status;
        return $this;
    }

    public function getFactor1(): float
    {
        return $this->factor1;
    }

    public function setFactor1(float $factor1): DocumentMetadata
    {
        $this->factor1 = $factor1;
        return $this;
    }

    public function getFactor2(): float
    {
        return $this->factor2;
    }

    public function setFactor2(float $factor2): DocumentMetadata
    {
        $this->factor2 = $factor2;
        return $this;
    }

    public function getFactor3(): float
    {
        return $this->factor3;
    }

    public function setFactor3(float $factor3): DocumentMetadata
    {
        $this->factor3 = $factor3;
        return $this;
    }

    public function getFactor4(): float
    {
        return $this->factor4;
    }

    public function setFactor4(float $factor4): DocumentMetadata
    {
        $this->factor4 = $factor4;
        return $this;
    }

    public function getFactor5(): float
    {
        return $this->factor5;
    }

    public function setFactor5(float $factor5): DocumentMetadata
    {
        $this->factor5 = $factor5;
        return $this;
    }

    public function getInternetSourcesFactor(): float
    {
        return $this->internetSourcesFactor;
    }

    public function setInternetSourcesFactor(float $internetSourcesFactor): DocumentMetadata
    {
        $this->internetSourcesFactor = $internetSourcesFactor;
        return $this;
    }

    public function getLocalSourcesFactor(): float
    {
        return $this->localSourcesFactor;
    }

    public function setLocalSourcesFactor(float $localSourcesFactor): DocumentMetadata
    {
        $this->localSourcesFactor = $localSourcesFactor;
        return $this;
    }

    public function getOtherSourcesFactor(): float
    {
        return $this->otherSourcesFactor;
    }

    public function setOtherSourcesFactor(float $otherSourcesFactor): DocumentMetadata
    {
        $this->otherSourcesFactor = $otherSourcesFactor;
        return $this;
    }

    public function getLegalSourcesFactor(): float
    {
        return $this->legalSourcesFactor;
    }

    public function setLegalSourcesFactor(float $legalSourcesFactor): DocumentMetadata
    {
        $this->legalSourcesFactor = $legalSourcesFactor;
        return $this;
    }

    public function getRefbooksSourcesFactor(): float
    {
        return $this->refbooksSourcesFactor;
    }

    public function setRefbooksSourcesFactor(float $refbooksSourcesFactor): DocumentMetadata
    {
        $this->refbooksSourcesFactor = $refbooksSourcesFactor;
        return $this;
    }

    public function getReversedFactor1(): float
    {
        return $this->reversedFactor1;
    }

    public function setReversedFactor1(float $reversedFactor1): DocumentMetadata
    {
        $this->reversedFactor1 = $reversedFactor1;
        return $this;
    }

    public function getQuotationsFactor(): float
    {
        return $this->quotationsFactor;
    }

    public function setQuotationsFactor(float $quotationsFactor): DocumentMetadata
    {
        $this->quotationsFactor = $quotationsFactor;
        return $this;
    }

    public function getForeignAlphabetAlert(): float
    {
        return $this->foreignAlphabetAlert;
    }

    public function setForeignAlphabetAlert(float $foreignAlphabetAlert): DocumentMetadata
    {
        $this->foreignAlphabetAlert = $foreignAlphabetAlert;
        return $this;
    }

    public function getCrossChecks(): array
    {
        return $this->crossChecks;
    }

    public function setCrossChecks(array $crossChecks): DocumentMetadata
    {
        $this->crossChecks = $crossChecks;
        return $this;
    }

    public function getAiDetect(): float
    {
        return $this->aiDetect;
    }

    public function setAiDetect(float $aiDetect): DocumentMetadata
    {
        $this->aiDetect = $aiDetect;
        return $this;
    }

    public function getDocumentSize(): int
    {
        return $this->documentSize;
    }

    public function setDocumentSize(int $documentSize): DocumentMetadata
    {
        $this->documentSize = $documentSize;
        return $this;
    }

    public function getMd5sum(): string
    {
        return $this->md5sum;
    }

    public function setMd5sum(string $md5sum): DocumentMetadata
    {
        $this->md5sum = $md5sum;
        return $this;
    }
}
