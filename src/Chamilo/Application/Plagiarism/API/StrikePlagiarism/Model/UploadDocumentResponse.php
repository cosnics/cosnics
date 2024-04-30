<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model;

class UploadDocumentResponse
{
    protected string $status;
    protected string $message;
    protected string $id;

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): UploadDocumentResponse
    {
        $this->status = $status;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): UploadDocumentResponse
    {
        $this->message = $message;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): UploadDocumentResponse
    {
        $this->id = $id;
        return $this;
    }

    public function hasError()
    {
        return $this->status == 'error';
    }

}