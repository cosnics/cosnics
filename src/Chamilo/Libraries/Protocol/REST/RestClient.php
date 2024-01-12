<?php

namespace Chamilo\Libraries\Protocol\REST;

use Chamilo\Libraries\Protocol\REST\Configuration\RestConfiguration;
use Chamilo\Libraries\Protocol\REST\Decorator\RestRequestDecoratorManager;
use Chamilo\Libraries\Protocol\REST\Exception\RestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * @package Hogent\Integration\Panopto\Repository\API\REST
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RestClient implements RestClientInterface
{
    protected RestConfiguration $apiConfiguration;
    protected Serializer $serializer;
    protected RestRequestDecoratorManager $restRequestDecoratorManager;

    protected ?Client $restClient = null;


    public function __construct(
        RestConfiguration $apiConfiguration, Serializer $serializer,
        RestRequestDecoratorManager $restRequestDecoratorManager
    )
    {
        $this->apiConfiguration = $apiConfiguration;
        $this->serializer = $serializer;
        $this->restRequestDecoratorManager = $restRequestDecoratorManager;
    }

    /**
     * @param RestRequest $restRequest
     *
     * @return object|array
     *
     * @throws RestException
     */
    public function executeRequest(RestRequest $restRequest)
    {
        $response = $this->executeRequestRaw($restRequest);
        return $this->getResultFromResponseRaw($response, $restRequest);
    }

    /**
     * Only call this directly when creating a decorator, use executeRequest otherwise
     *
     * @param RestRequest $restRequest
     * @return mixed|ResponseInterface
     *
     * @throws RestException
     */
    public function executeRequestRaw(RestRequest $restRequest)
    {
        try
        {
            $this->initializeRestClient();

            $this->restRequestDecoratorManager->decorateRequest($restRequest, $this);

            $request = $this->toGuzzleRequest($restRequest);
            return $this->restClient->send($request); //, ['debug' => true]);
        }
        catch(\Exception | \GuzzleHttp\Exception\GuzzleException $ex)
        {
            throw new RestException($ex->getMessage(), $ex->getCode(), $ex);
        }

    }

    /**
     * Formats this request to an actual Guzzle Request object
     *
     * @return Request
     * @throws RestException
     */
    protected function toGuzzleRequest(RestRequest $request)
    {
        $headers = $request->getHeaders();
        $headers['Content-Type'] = 'application/json';

        $body = is_object($request->getBodyObject()) ?
            $this->serializer->serialize($request->getBodyObject(), 'json') : null;

        $body = !empty($request->getBodyParameters()) ?
            $this->serializer->serialize($request->getBodyParameters(), 'json') : null;

        return new Request($this->getMethod(), $this->getFullPath(), $headers, $body);
    }

    /**
     * Only call this directly when creating a decorator, use executeRequest otherwise
     *
     * @param ResponseInterface $response
     * @param RestRequest $restRequest
     *
     * @return object|array
     * @throws RestException
     */
    public function getResultFromResponseRaw(ResponseInterface $response, RestRequest $restRequest)
    {
        try
        {
            $contents = $response->getBody()->getContents();

            if(!$restRequest->getModelClassName())
            {
                $result = $this->serializer->decode($contents, 'json');
            }
            else
            {
                $type = $restRequest->getModelClassName() . $restRequest->getReturnsMultipleRecords() ? '[]' : '';
                $result = $this->serializer->deserialize($response->getBody()->getContents(), $type, 'json');
            }

            if (empty($result))
            {
                return $restRequest->getReturnsMultipleRecords() ? [] : null;
            }

            return $result;
        }
        catch(\Exception | \ReflectionException $ex)
        {
            throw new RestException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Initializes the rest client if it hasn't already been initialized
     */
    protected function initializeRestClient()
    {
        if ($this->restClient instanceof Client)
        {
            return;
        }

        if (!$this->apiConfiguration->isValidConfiguration())
        {
            throw new \RuntimeException('Could not initialize the rest client due to invalid configuration');
        }

        $this->restClient = new Client(['base_uri' => $this->apiConfiguration->getApiURL()]);
    }

}
