<?php

namespace Chamilo\Libraries\Protocol\REST\Decorator;

use Chamilo\Libraries\Protocol\REST\RestClientInterface;

/**
 * Class RestClientDecorator
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
abstract class RestClientDecorator implements RestClientInterface
{
    /**
     * @var RestClientInterface
     */
    protected $restClient;

    /**
     * RestClientDecorator constructor.
     *
     * @param RestClientInterface $restClient
     */
    public function __construct(RestClientInterface $restClient)
    {
        $this->restClient = $restClient;
    }
}
