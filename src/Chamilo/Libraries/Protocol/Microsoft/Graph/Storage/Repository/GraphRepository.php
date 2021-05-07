<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphCollectionRequest;
use Microsoft\Graph\Http\GraphRequest;
use Microsoft\Graph\Http\GraphResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GraphRepository
{
    const RESPONSE_CODE_RESOURCE_NOT_FOUND = '404';
    const RESPONSE_CODE_ACCESS_TOKEN_EXPIRED = '401';
    const RESPONSE_CODE_BAD_REQUEST = '400';
    const API_VERSION_V1 = 'V1.0';
    const API_VERSION_BETA = 'beta';

    /**
     *
     * @var \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $oauthProvider;

    /**
     *
     * @var \Microsoft\Graph\Graph
     */
    protected $graph;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface
     */
    protected $accessTokenRepository;

    /**
     *
     * @var \League\OAuth2\Client\Token\AccessToken
     */
    protected $delegatedAccessToken;

    /**
     * GraphRepository constructor.
     *
     * @param \League\OAuth2\Client\Provider\AbstractProvider $oauthProvider
     * @param \Microsoft\Graph\Graph $graph
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function __construct(
        AbstractProvider $oauthProvider, Graph $graph,
        AccessTokenRepositoryInterface $accessTokenRepository
    )
    {
        $this->setOauthProvider($oauthProvider);
        $this->setGraph($graph);
        $this->setAccessTokenRepository($accessTokenRepository);

        $this->initializeApplicationAccessToken();
    }

    /**
     *
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected function getOauthProvider()
    {
        return $this->oauthProvider;
    }

    /**
     *
     * @param \League\OAuth2\Client\Provider\AbstractProvider $oauthProvider
     */
    protected function setOauthProvider(AbstractProvider $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
    }

    /**
     *
     * @return \Microsoft\Graph\Graph
     */
    protected function getGraph()
    {
        return $this->graph;
    }

    /**
     *
     * @param \Microsoft\Graph\Graph $graph
     */
    protected function setGraph(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface
     */
    protected function getAccessTokenRepository()
    {
        return $this->accessTokenRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     */
    protected function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository)
    {
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function getDelegatedAccessToken()
    {
        return $this->delegatedAccessToken;
    }

    /**
     *
     * @param \League\OAuth2\Client\Token\AccessTokenInterface $delegatedAccessToken
     */
    protected function setDelegatedAccessToken(AccessTokenInterface $delegatedAccessToken = null)
    {
        $this->delegatedAccessToken = $delegatedAccessToken;
    }

    /**
     * @throws IdentityProviderException
     */
    protected function initializeApplicationAccessToken()
    {
        $accessToken = $this->getAccessTokenRepository()->getApplicationAccessToken();

        if (!$accessToken instanceof AccessToken || $accessToken->hasExpired())
        {
            $accessToken = $this->requestNewApplicationAccessToken();
        }

        $this->getGraph()->setAccessToken($accessToken);
        $this->setDelegatedAccessToken($this->getAccessTokenRepository()->getDelegatedAccessToken());
    }

    /**
     * @return AccessTokenInterface
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    protected function requestNewApplicationAccessToken()
    {
        $accessToken = $this->getOauthProvider()->getAccessToken(
            'client_credentials',
            ['resource' => 'https://graph.microsoft.com/']
        );

        $this->getAccessTokenRepository()->storeApplicationAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * Requests a new access token for the user, redirecting the user to the authorization URL
     */
    protected function requestNewDelegatedAccessToken()
    {
        $authorizationUrl = $this->getOauthProvider()->getAuthorizationUrl(
            ['state' => $this->oauthProvider->getState()]
        );

        $redirectResponse = new RedirectResponse($authorizationUrl);
        $redirectResponse->send();

        exit;
    }

    /**
     * Sets the user access token as the currently to use access token
     * @throws IdentityProviderException
     */
    protected function activateDelegatedAccessToken()
    {
        $delegatedAccessToken = $this->getDelegatedAccessToken();

        if (empty($delegatedAccessToken) || !$delegatedAccessToken instanceof AccessToken)
        {
            $this->requestNewDelegatedAccessToken();
        }
        elseif ($delegatedAccessToken->hasExpired())
        {
            $this->setDelegatedAccessToken(
                $this->getOauthProvider()->getAccessToken(
                    'refresh_token',
                    ['refresh_token' => $delegatedAccessToken->getRefreshToken()]
                )
            );

            $this->accessTokenRepository->storeDelegatedAccessToken($this->getDelegatedAccessToken());
        }

        $this->getGraph()->setAccessToken($this->getDelegatedAccessToken());
    }

    /**
     * Authorizes a user by a given authorization code
     *
     * @param string $authorizationCode
     *
     * @throws IdentityProviderException
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        $this->setDelegatedAccessToken(
            $this->getOauthProvider()->getAccessToken(
                'authorization_code',
                ['code' => $authorizationCode, 'resource' => 'https://graph.microsoft.com/']
            )
        );

        $this->getAccessTokenRepository()->storeDelegatedAccessToken($this->getDelegatedAccessToken());
    }

    /**
     * Executes a request in the graph API with an additional try if the access token has expired by refreshing
     * the access token and executing the request again.
     *
     * @param \Microsoft\Graph\Http\GraphRequest $graphRequest
     *
     * @return mixed
     * @throws GraphException
     * @throws IdentityProviderException
     */
    protected function executeRequestWithAccessTokenExpirationRetry(GraphRequest $graphRequest)
    {
        try
        {
            return $graphRequest->execute();
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == self::RESPONSE_CODE_ACCESS_TOKEN_EXPIRED)
            {
                $accessToken = $this->requestNewApplicationAccessToken();
                $this->getGraph()->setAccessToken($accessToken);
                $graphRequest->addHeaders(['Authorization' => 'Bearer ' . $accessToken]);

                try
                {
                    return $graphRequest->execute();
                }
                catch (\Microsoft\Graph\Exception\GraphException $ex)
                {
                }
            }

            throw $exception;
        }
        catch (\Microsoft\Graph\Exception\GraphException $exception)
        {
            $outputArray = [];
            preg_match('/Received ([^\s]*) for call/', $exception->getMessage(), $outputArray);
            $parsedCode = empty($outputArray[1]) ? 0 : $outputArray[1];

            throw new GraphException($exception->getMessage(), $parsedCode, $exception);
        }
    }

    /**
     *
     * @param \Microsoft\Graph\Http\GraphRequest $graphRequest
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     *
     * @throws \Exception
     */
    protected function executeRequestWithDelegatedAccess(GraphRequest $graphRequest)
    {
        $this->activateDelegatedAccessToken();

        if (!$this->delegatedAccessToken instanceof AccessToken)
        {
            throw new \Exception('The delegated access token could not be activated');
        }

        /**
         * Change the authorization header since graph doesn't do this automatically when the new token is set
         */
        $graphRequest->addHeaders(['Authorization' => 'Bearer ' . $this->delegatedAccessToken->getToken()]);

        $result = $graphRequest->execute();
        $this->initializeApplicationAccessToken();

        return $result;
    }

    /**
     * @param $requestType
     * @param $endpoint
     * @param array $requestBody
     * @param null $returnClass
     * @param bool $isCollectionRequest
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Http\GraphCollectionRequest|GraphRequest
     *
     * @throws GraphException
     */
    protected function createRequest(
        $requestType, $endpoint, $requestBody = [], $returnClass = null, bool $isCollectionRequest = false,
        string $apiVersion = self::API_VERSION_V1
    ): GraphRequest
    {
        $this->getGraph()->setApiVersion($apiVersion);

        try
        {
            if (!$isCollectionRequest)
            {
                $request = $this->getGraph()->createRequest($requestType, $endpoint)->setReturnType($returnClass);
            }
            else
            {
                $request = $this->getGraph()->createCollectionRequest($requestType, $endpoint);
            }
        }
        catch (\Microsoft\Graph\Exception\GraphException $exception)
        {
            throw new GraphException("Error creating request", 0, $exception);
        }

        if (!empty($requestBody))
        {
            $request->attachBody($requestBody);
        }

        return $request;
    }

    /**
     * Parses a collection response.
     * Bugfix for the microsoft graph library parsing everything to a single
     * object when an empty collection is returned from the graph API
     *
     * @param \Microsoft\Graph\Http\GraphResponse $graphResponse
     * @param string $returnType
     *
     * @return array
     */
    protected function parseCollectionResponse(GraphResponse $graphResponse, $returnType, bool $delegated = false)
    {
        $body = $graphResponse->getBody();

        $count = 0;

        if (array_key_exists('@odata.count', $body))
        {
            $count = $body['@odata.count'];
        }
        elseif (array_key_exists('value', $body))
        {
            $count = count($body['value']);
        }

        $data = ($count > 0) ? $graphResponse->getResponseAsObject($returnType) : [];
        $nextLink = $body['@odata.nextLink'];
        if (!empty($nextLink))
        {
            if ($delegated)
            {
                $data =
                    array_merge($data, $this->executeGetWithDelegatedAccess($nextLink, $returnType, true));
            }
            else
            {
                $data =
                    array_merge($data, $this->executeGetWithAccessTokenExpirationRetry($nextLink, $returnType, true));
            }
        }

        return $data;
    }

    /**
     * @param $requestType
     * @param $endpoint
     * @param array $requestBody
     * @param null $returnClass
     * @param bool $isCollectionRequest
     * @param string $apiVersion
     *
     * @return mixed
     * @throws GraphException
     */
    protected function createAndExecuteRequestWithAccessTokenExpirationRetry(
        $requestType, $endpoint, $requestBody = [],
        $returnClass = null, bool $isCollectionRequest = false,
        $apiVersion = self::API_VERSION_V1
    )
    {
        try
        {
            return $this->executeRequestWithAccessTokenExpirationRetry(
                $this->createRequest(
                    $requestType, $endpoint, $requestBody, $returnClass, $isCollectionRequest, $apiVersion
                )
            );
        }
        catch (IdentityProviderException $exception)
        {
            throw new GraphException("Authentication or authorization failed with Microsoft Graph", 0, $exception);
        }
    }

    /**
     *
     * @param string $requestType
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @param bool $isCollectionRequest
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity | \Microsoft\Graph\Http\GraphResponse -
     *      A Microsoft Graph Entity-instance of type $returnClass or a dry collection response
     * @throws \Exception
     */
    protected function createAndExecuteRequestWithDelegatedAccessToken(
        $requestType, $endpoint, $requestBody = [],
        $returnClass = null, $isCollectionRequest = false,
        $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->executeRequestWithDelegatedAccess(
            $this->createRequest($requestType, $endpoint, $requestBody, $returnClass, $isCollectionRequest, $apiVersion)
        );
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     * @param bool $isCollectionRequest
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity | \Microsoft\Graph\Model\Entity[]
     *  A Microsoft Graph Entity-instance of type $returnClass
     * @throws GraphException
     */
    public function executeGetWithAccessTokenExpirationRetry(
        $endpoint, $returnClass = null, $isCollectionRequest = false, $apiVersion = self::API_VERSION_V1
    )
    {
        $response = $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'GET', $endpoint, [], $returnClass, $isCollectionRequest, $apiVersion
        );

        if ($isCollectionRequest)
        {
            return $this->parseCollectionResponse($response, $returnClass, false);
        }

        return $response;
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     * @param bool $isCollectionRequest
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity | \Microsoft\Graph\Model\Entity[]
     *  A Microsoft Graph Entity-instance of type $returnClass
     * @throws \Exception
     */
    public function executeGetWithDelegatedAccess(
        $endpoint, $returnClass = null, $isCollectionRequest = false,
        $apiVersion = self::API_VERSION_V1
    )
    {
        $response = $this->createAndExecuteRequestWithDelegatedAccessToken(
            'GET', $endpoint, [], $returnClass, $isCollectionRequest, $apiVersion
        );

        if ($isCollectionRequest)
        {
            return $this->parseCollectionResponse($response, $returnClass, true);
        }

        return $response;
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity | GraphResponse
     * @throws GraphException
     */
    public function executePostWithAccessTokenExpirationRetry(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'POST',
            $endpoint,
            $requestBody,
            $returnClass,
            false,
            $apiVersion
        );
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     * @throws GraphException
     */
    public function executePutWithAccessTokenExpirationRetry(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'PUT',
            $endpoint,
            $requestBody,
            $returnClass,
            false,
            $apiVersion
        );
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     * @throws \Exception
     */
    public function executePostWithDelegatedAccess(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithDelegatedAccessToken(
            'POST', $endpoint, $requestBody, $returnClass, false, $apiVersion
        );
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     * @throws GraphException
     */
    public function executePatchWithAccessTokenExpirationRetry(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'PATCH',
            $endpoint,
            $requestBody,
            $returnClass,
            false,
            $apiVersion
        );
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     * @throws \Exception
     */
    public function executePatchWithDelegatedAccess(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithDelegatedAccessToken(
            'PATCH', $endpoint, $requestBody, $returnClass, false, $apiVersion
        );
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     * @throws GraphException
     */
    public function executeDeleteWithAccessTokenExpirationRetry(
        $endpoint, $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'DELETE', $endpoint, [], $returnClass, false, $apiVersion
        );
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     * @throws \Exception
     */
    public function executeDeleteWithDelegatedAccess($endpoint, $returnClass = null, $apiVersion = self::API_VERSION_V1)
    {
        return $this->createAndExecuteRequestWithDelegatedAccessToken(
            'DELETE', $endpoint, [], $returnClass, false, $apiVersion
        );
    }
}
