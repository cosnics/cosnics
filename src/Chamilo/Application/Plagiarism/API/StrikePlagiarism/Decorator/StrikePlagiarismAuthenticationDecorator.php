<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Decorator;

use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\StrikePlagiarismRequestParameters;
use Chamilo\Libraries\Protocol\REST\Configuration\TokenBasedConfiguration;
use Chamilo\Libraries\Protocol\REST\Decorator\RestRequestDecoratorInterface;
use Chamilo\Libraries\Protocol\REST\RestClient;
use Chamilo\Libraries\Protocol\REST\RestRequest;

class StrikePlagiarismAuthenticationDecorator implements RestRequestDecoratorInterface
{
    protected TokenBasedConfiguration $apiConfiguration;

    public function __construct(TokenBasedConfiguration $apiConfiguration)
    {
        $this->apiConfiguration = $apiConfiguration;
    }

    public function decorateRequest(RestRequest $restRequest, RestClient $restClient)
    {
        $bodyObject = $restRequest->getBodyObject();

        if(!$bodyObject instanceof StrikePlagiarismRequestParameters)
        {
            return;
        }

        $bodyObject->setApiKey($this->apiConfiguration->getApiToken());
    }
}