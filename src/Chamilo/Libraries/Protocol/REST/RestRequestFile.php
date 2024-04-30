<?php

namespace Chamilo\Libraries\Protocol\REST;

class RestRequestFile
{
    protected string $filename;
    protected string $filePath;

    public function __construct(string $filename, string $filePath)
    {
        $this->filename = $filename;
        $this->filePath = $filePath;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): RestRequestFile
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): RestRequestFile
    {
        $this->filePath = $filePath;
        return $this;
    }
}