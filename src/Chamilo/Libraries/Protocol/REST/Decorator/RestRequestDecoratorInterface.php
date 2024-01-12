<?php

namespace Chamilo\Libraries\Protocol\REST\Decorator;

use Chamilo\Libraries\Protocol\REST\RestClient;
use Chamilo\Libraries\Protocol\REST\RestRequest;

/**
 * Class RestClientInterface
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface RestRequestDecoratorInterface
{
    /**
     * @param RestRequest $restRequest
     */
    public function decorateRequest(RestRequest $restRequest, RestClient $restClient);
}
