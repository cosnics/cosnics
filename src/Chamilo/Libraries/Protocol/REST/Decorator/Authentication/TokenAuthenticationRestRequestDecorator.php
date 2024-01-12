<?php

namespace Chamilo\Libraries\Protocol\REST\Decorator\Authentication;

use Chamilo\Libraries\Protocol\REST\Decorator\RestRequestDecoratorInterface;
use Chamilo\Libraries\Protocol\REST\RestClient;
use Chamilo\Libraries\Protocol\REST\RestRequest;

class TokenAuthenticationRestRequestDecorator implements RestRequestDecoratorInterface
{
    protected string $authToken;

    /**
     * @param string $authToken
     */
    public function __construct(string $authToken)
    {
        $this->authToken = $authToken;
    }

    public function decorateRequest(RestRequest $restRequest, RestClient $restClient)
    {
        $restRequest->addHeader('Authorization', 'Bearer ' . $this->authToken);
    }
}