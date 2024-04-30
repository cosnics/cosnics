<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;

class UploadDocumentRequestParameters extends StrikePlagiarismRequestParameters
{
    const ACTION_CHECK = 'check';
    const ACTION_INDEX = 'index';

    #[SerializedName('languageCode')]
    protected string $languageCode;
    protected string $faculty;
    protected string $action;
    protected string $callback;
    #[SerializedName('userEmail')]
    protected string $userEmail;
    protected string $id;
    protected string $title;
    protected string $author;
    protected string $coordinator;

    /**
     * @var string[]
     */
    protected array $reviewer;
    #[SerializedName('documentKind')]
    protected string $documentKind;
    #[SerializedName('assignmentId')]
    protected string $assignmentId;
    #[SerializedName('userId')]
    protected string $userId;
    #[SerializedName('unitName')]
    protected string $unitName;
    #[SerializedName('aiDetection')]
    protected string $aiDetection;

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): UploadDocumentRequestParameters
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    public function getFaculty(): string
    {
        return $this->faculty;
    }

    public function setFaculty(string $faculty): UploadDocumentRequestParameters
    {
        $this->faculty = $faculty;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): UploadDocumentRequestParameters
    {
        if($action != self::ACTION_INDEX && $action != self::ACTION_CHECK)
            throw new \InvalidArgumentException('Invalid action');

        $this->action = $action;
        return $this;
    }

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function setCallback(string $callback): UploadDocumentRequestParameters
    {
        $this->callback = $callback;
        return $this;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): UploadDocumentRequestParameters
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): UploadDocumentRequestParameters
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): UploadDocumentRequestParameters
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): UploadDocumentRequestParameters
    {
        $this->author = $author;
        return $this;
    }

    public function getCoordinator(): string
    {
        return $this->coordinator;
    }

    public function setCoordinator(string $coordinator): UploadDocumentRequestParameters
    {
        $this->coordinator = $coordinator;
        return $this;
    }

    public function getReviewer(): array
    {
        return $this->reviewer;
    }

    public function setReviewer(array $reviewer): UploadDocumentRequestParameters
    {
        $this->reviewer = $reviewer;
        return $this;
    }

    public function getDocumentKind(): string
    {
        return $this->documentKind;
    }

    public function setDocumentKind(string $documentKind): UploadDocumentRequestParameters
    {
        $this->documentKind = $documentKind;
        return $this;
    }

    public function getAssignmentId(): string
    {
        return $this->assignmentId;
    }

    public function setAssignmentId(string $assignmentId): UploadDocumentRequestParameters
    {
        $this->assignmentId = $assignmentId;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): UploadDocumentRequestParameters
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUnitName(): string
    {
        return $this->unitName;
    }

    public function setUnitName(string $unitName): UploadDocumentRequestParameters
    {
        $this->unitName = $unitName;
        return $this;
    }

    public function getAiDetection(): string
    {
        return $this->aiDetection;
    }

    public function setAiDetection(string $aiDetection): UploadDocumentRequestParameters
    {
        $this->aiDetection = $aiDetection;
        return $this;
    }
}