<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request;

abstract class StrikePlagiarismRequestParameters
{
    protected string $apiKey;

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): StrikePlagiarismRequestParameters
    {
        $this->apiKey = $apiKey;
        return $this;
    }


}