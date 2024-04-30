<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request;

class GetDocumentMetadataRequestParameters extends StrikePlagiarismRequestParameters
{
    protected string $id;
    protected string $md5sum;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): GetDocumentMetadataRequestParameters
    {
        $this->id = $id;
        return $this;
    }

    public function getMd5sum(): string
    {
        return $this->md5sum;
    }

    public function setMd5sum(string $md5sum): GetDocumentMetadataRequestParameters
    {
        $this->md5sum = $md5sum;
        return $this;
    }


}