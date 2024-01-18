<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;

class UploadDocumentRequestParameters extends StrikePlagiarismRequestParameters
{
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
    protected string $reviewer;
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
    protected string $file;

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): StrikePlagiarismRequestParameters
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    public function getFaculty(): string
    {
        return $this->faculty;
    }

    public function setFaculty(string $faculty): StrikePlagiarismRequestParameters
    {
        $this->faculty = $faculty;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): StrikePlagiarismRequestParameters
    {
        $this->action = $action;
        return $this;
    }

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function setCallback(string $callback): StrikePlagiarismRequestParameters
    {
        $this->callback = $callback;
        return $this;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): StrikePlagiarismRequestParameters
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): StrikePlagiarismRequestParameters
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): StrikePlagiarismRequestParameters
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): StrikePlagiarismRequestParameters
    {
        $this->author = $author;
        return $this;
    }

    public function getCoordinator(): string
    {
        return $this->coordinator;
    }

    public function setCoordinator(string $coordinator): StrikePlagiarismRequestParameters
    {
        $this->coordinator = $coordinator;
        return $this;
    }

    public function getReviewer(): string
    {
        return $this->reviewer;
    }

    public function setReviewer(string $reviewer): StrikePlagiarismRequestParameters
    {
        $this->reviewer = $reviewer;
        return $this;
    }

    public function getDocumentKind(): string
    {
        return $this->documentKind;
    }

    public function setDocumentKind(string $documentKind): StrikePlagiarismRequestParameters
    {
        $this->documentKind = $documentKind;
        return $this;
    }

    public function getAssignmentId(): string
    {
        return $this->assignmentId;
    }

    public function setAssignmentId(string $assignmentId): StrikePlagiarismRequestParameters
    {
        $this->assignmentId = $assignmentId;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): StrikePlagiarismRequestParameters
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUnitName(): string
    {
        return $this->unitName;
    }

    public function setUnitName(string $unitName): StrikePlagiarismRequestParameters
    {
        $this->unitName = $unitName;
        return $this;
    }

    public function getAiDetection(): string
    {
        return $this->aiDetection;
    }

    public function setAiDetection(string $aiDetection): StrikePlagiarismRequestParameters
    {
        $this->aiDetection = $aiDetection;
        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): StrikePlagiarismRequestParameters
    {
        $this->file = $file;
        return $this;
    }
}