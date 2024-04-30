<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;

abstract class StrikePlagiarismRequestParameters
{
    #[SerializedName('APIKEY')]
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