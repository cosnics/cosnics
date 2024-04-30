<?php

namespace Chamilo\Libraries\Protocol\REST;

use Chamilo\Libraries\Protocol\REST\Exception\RestException;

/**
 * Class RestClientInterface
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface RestClientInterface
{
    /**
     * @param RestRequest $restRequest
     *
     * @return object|array
     *
     * @throws RestException
     */
    public function executeRequest(RestRequest $restRequest);
}
