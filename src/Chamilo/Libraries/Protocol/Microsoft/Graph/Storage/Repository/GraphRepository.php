<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GraphRepository
{
    const RESPONSE_CODE_RESOURCE_NOT_FOUND = '404';
    const RESPONSE_CODE_ACCESS_TOKEN_EXPIRED = '401';

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
     *
     * @var string
     */
    protected $currentRequestUrl;

    /**
     * GraphRepository constructor.
     *
     * @param \League\OAuth2\Client\Provider\AbstractProvider $oauthProvider
     * @param \Microsoft\Graph\Graph $graph
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     * @param string $currentRequestUrl
     */
    public function __construct(AbstractProvider $oauthProvider, Graph $graph,
        AccessTokenRepositoryInterface $accessTokenRepository, $currentRequestUrl)
    {
        $this->oauthProvider = $oauthProvider;
        $this->graph = $graph;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->currentRequestUrl = $currentRequestUrl;

        $this->initializeApplicationAccessToken();
    }

    /**
     *
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function getOauthProvider()
    {
        return $this->oauthProvider;
    }

    /**
     *
     * @param \League\OAuth2\Client\Provider\AbstractProvider $oauthProvider
     */
    public function setOauthProvider(AbstractProvider $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
    }

    /**
     *
     * @return \Microsoft\Graph\Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     *
     * @param \Microsoft\Graph\Graph $graph
     */
    public function setGraph(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository()
    {
        return $this->accessTokenRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository)
    {
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getDelegatedAccessToken()
    {
        return $this->delegatedAccessToken;
    }

    /**
     *
     * @param \League\OAuth2\Client\Token\AccessToken $delegatedAccessToken
     */
    public function setDelegatedAccessToken(AccessToken $delegatedAccessToken)
    {
        $this->delegatedAccessToken = $delegatedAccessToken;
    }

    /**
     *
     * @return string
     */
    public function getCurrentRequestUrl()
    {
        return $this->currentRequestUrl;
    }

    /**
     *
     * @param string $currentRequestUrl
     */
    public function setCurrentRequestUrl($currentRequestUrl)
    {
        $this->currentRequestUrl = $currentRequestUrl;
    }

    /**
     * Initializes the access token
     */
    protected function initializeApplicationAccessToken()
    {
        $accessToken = $this->getAccessTokenRepository()->getApplicationAccessToken();

        if (! $accessToken instanceof AccessToken || $accessToken->hasExpired())
        {
            $accessToken = $this->requestNewApplicationAccessToken();
        }

        $this->getGraph()->setAccessToken($accessToken);
        $this->setDelegatedAccessToken($this->getAccessTokenRepository()->getDelegatedAccessToken());
    }

    /**
     * Returns the access token
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function requestNewApplicationAccessToken()
    {
        $accessToken = $this->getOauthProvider()->getAccessToken(
            'client_credentials',
            ['resource' => 'https://graph.microsoft.com/']);

        $this->getAccessTokenRepository()->storeApplicationAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * Requests a new access token for the user, redirecting the user to the authorization URL
     */
    protected function requestNewDelegatedAccessToken()
    {
        $authorizationUrl = $this->getOauthProvider()->getAuthorizationUrl(
            ['state' => base64_encode($this->getCurrentRequestUrl())]);

        $redirectResponse = new RedirectResponse($authorizationUrl);
        $redirectResponse->send();
    }

    /**
     * Sets the user access token as the currently to use access token
     */
    protected function activateDelegatedAccessToken()
    {
        $delegatedAccessToken = $this->getDelegatedAccessToken();

        if (empty($delegatedAccessToken) || ! $delegatedAccessToken instanceof AccessToken)
        {
            $this->requestNewDelegatedAccessToken();
        }

        $this->getGraph()->setAccessToken($delegatedAccessToken);
    }

    /**
     * Authorizes a user by a given authorization code
     *
     * @param string $authorizationCode
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        $this->setDelegatedAccessToken(
            $this->getOauthProvider()->getAccessToken(
                'authorization_code',
                ['code' => $authorizationCode, 'resource' => 'https://graph.microsoft.com/']));

        $this->getAccessTokenRepository()->storeDelegatedAccessToken($this->getDelegatedAccessToken());
    }

    /**
     * Executes a request in the graph API with an additional try if the access token has expired by refreshing
     * the access token and executing the request again.
     *
     * @param \Microsoft\Graph\Http\GraphRequest $graphRequest
     * @throws \GuzzleHttp\Exception\ClientException $exception
     * @return mixed
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

                return $graphRequest->execute();
            }

            throw $exception;
        }
    }

    /**
     *
     * @param \Microsoft\Graph\Http\GraphRequest $graphRequest
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    protected function executeRequestWithDelegatedAccess(GraphRequest $graphRequest)
    {
        $this->activateDelegatedAccessToken();
        $result = $graphRequest->execute();
        $this->initializeApplicationAccessToken();

        return $result;
    }

    /**
     *
     * @param string $requestType
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @return \Microsoft\Graph\Http\GraphRequest
     */
    protected function createRequest($requestType, $endpoint, $requestBody = [], $returnClass = null)
    {
        $request = $this->getGraph()->createRequest($requestType, $endpoint)->setReturnType($returnClass);

        if (! empty($requestBody))
        {
            $request->attachBody($requestBody);
        }

        return $request;
    }

    /**
     *
     * @param string $requestType
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    protected function createAndExecuteRequestWithAccessTokenExpirationRetry($requestType, $endpoint, $requestBody = [],
        $returnClass = null)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->createRequest($requestType, $endpoint, $requestBody, $returnClass));
    }

    /**
     *
     * @param string $requestType
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    protected function createAndExecuteRequestWithDelegatedAccessToken($requestType, $endpoint, $requestBody = [],
        $returnClass = null)
    {
        return $this->executeRequestWithDelegatedAccessToken(
            $this->createRequest($requestType, $endpoint, $requestBody, $returnClass));
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executeGetWithAccessTokenExpirationRetry($endpoint, $returnClass = null)
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry('GET', $endpoint, [], $returnClass);
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executeGetWithDelegatedAccess($endpoint, $returnClass = null)
    {
        return $this->createAndExecuteRequestWithDelegatedAccessToken('GET', $endpoint, [], $returnClass);
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executePostWithAccessTokenExpirationRetry($endpoint, $requestBody = [], $returnClass = null)
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'POST',
            $endpoint,
            $requestBody,
            $returnClass);
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executePostWithDelegatedAccess($endpoint, $requestBody = [], $returnClass = null)
    {
        return $this->createAndExecuteRequestWithDelegatedAccess('POST', $endpoint, $requestBody, $returnClass);
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executePatchWithAccessTokenExpirationRetry($endpoint, $requestBody = [], $returnClass = null)
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'PATCH',
            $endpoint,
            $requestBody,
            $returnClass);
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executePatchWithDelegatedAccess($endpoint, $requestBody = [], $returnClass = null)
    {
        return $this->createAndExecuteRequestWithDelegatedAccess('PATCH', $endpoint, $requestBody, $returnClass);
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executeDeleteWithAccessTokenExpirationRetry($endpoint, $returnClass = null)
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry('DELETE', $endpoint, [], $returnClass);
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executeDeleteWithDelegatedAccess($endpoint, $returnClass = null)
    {
        return $this->createAndExecuteRequestWithDelegatedAccess('DELETE', $endpoint, [], $returnClass);
    }
}