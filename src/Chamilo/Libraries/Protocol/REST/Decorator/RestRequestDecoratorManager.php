<?php
namespace Chamilo\Libraries\Protocol\REST\Decorator;

use Chamilo\Libraries\Protocol\REST\RestClient;
use Chamilo\Libraries\Protocol\REST\RestRequest;

class RestRequestDecoratorManager implements RestRequestDecoratorInterface
{
    /**
     * @var array|\Chamilo\Libraries\Protocol\REST\Decorator\RestRequestDecoratorInterface[]
     */
    protected array $restRequestDecorators = [];

    public function addRestRequestDecorator(RestRequestDecoratorInterface $restRequestDecorator)
    {
        $this->restRequestDecorators[] = $restRequestDecorator;
    }

    public function decorateRequest(RestRequest $restRequest, RestClient $restClient)
    {
        foreach($this->restRequestDecorators as $requestDecorator)
        {
            $requestDecorator->decorateRequest($restRequest, $this);
        }
    }
}