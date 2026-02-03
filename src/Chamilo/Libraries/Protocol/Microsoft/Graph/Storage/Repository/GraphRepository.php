<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface\AccessTokenRepositoryInterface;
use Exception;
use GuzzleHttp\Exception\ClientException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Microsoft\Graph\GraphServiceClient;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @TODO Fix class
 */
class GraphRepository
{
    public const API_VERSION_BETA = 'beta';
    public const API_VERSION_V1 = 'V1.0';
    public const RESPONSE_CODE_ACCESS_TOKEN_EXPIRED = '401';
    public const RESPONSE_CODE_RESOURCE_NOT_FOUND = '404';

    protected AccessTokenRepositoryInterface $accessTokenRepository;

    protected ?AccessToken $delegatedAccessToken = null;

    protected GraphServiceClient $graphServiceClient;

    /**
     *
     * @var \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected AbstractProvider $oauthProvider;

    public function __construct(
        AbstractProvider $oauthProvider, GraphServiceClient $graphServiceClient,
        AccessTokenRepositoryInterface $accessTokenRepository
    )
    {
        $this->setOauthProvider($oauthProvider);
        $this->setGraphServiceClient($graphServiceClient);
        $this->setAccessTokenRepository($accessTokenRepository);

        $this->initializeApplicationAccessToken();
    }

    /**
     * Sets the user access token as the currently to use access token
     */
    protected function activateDelegatedAccessToken()
    {
        $delegatedAccessToken = $this->getDelegatedAccessToken();

        if (empty($delegatedAccessToken) || !$delegatedAccessToken instanceof AccessToken) {
            $this->requestNewDelegatedAccessToken();
        }
        elseif ($delegatedAccessToken->hasExpired()) {
            $this->setDelegatedAccessToken(
                $this->getOauthProvider()->getAccessToken(
                    'refresh_token', ['refresh_token' => $delegatedAccessToken->getRefreshToken()]
                )
            );

            $this->accessTokenRepository->storeDelegatedAccessToken($this->getDelegatedAccessToken());
        }

        $this->getGraphServiceClient()->setAccessToken($this->getDelegatedAccessToken());
    }

    /**
     *
     * @param string $requestType
     * @param string $endpoint
     * @param string[] $requestBody
     * @param string $returnClass
     * @param string $apiVersion
     *
     * @param bool $isCollectionRequest
     *
     * @return \Microsoft\Graph\Model\Entity | \Microsoft\Graph\Http\GraphResponse -
     *      A Microsoft Graph Entity-instance of type $returnClass or a dry collection response
     */
    protected function createAndExecuteRequestWithAccessTokenExpirationRetry(
        $requestType, $endpoint, $requestBody = [], $returnClass = null, bool $isCollectionRequest = false,
        $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->createRequest($requestType, $endpoint, $requestBody, $returnClass, $isCollectionRequest, $apiVersion)
        );
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
        $requestType, $endpoint, $requestBody = [], $returnClass = null, $isCollectionRequest = false,
        $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->executeRequestWithDelegatedAccess(
            $this->createRequest($requestType, $endpoint, $requestBody, $returnClass, $isCollectionRequest, $apiVersion)
        );
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
     */
    protected function createRequest(
        $requestType, $endpoint, $requestBody = [], $returnClass = null, bool $isCollectionRequest = false,
        string $apiVersion = self::API_VERSION_V1
    )
    {
        $this->getGraphServiceClient()->setApiVersion($apiVersion);

        if (!$isCollectionRequest) {
            $request =
                $this->getGraphServiceClient()->createRequest($requestType, $endpoint)->setReturnType($returnClass);
        }
        else {
            $request = $this->getGraphServiceClient()->createCollectionRequest($requestType, $endpoint);
        }

        if (!empty($requestBody)) {
            $request->attachBody($requestBody);
        }

        return $request;
    }

    /**
     *
     * @param string $endpoint
     * @param string $returnClass
     *
     * @param string $apiVersion
     *
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
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
            'DELETE', $endpoint, [], $returnClass, $apiVersion
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
     */
    public function executeGetWithAccessTokenExpirationRetry(
        $endpoint, $returnClass = null, $isCollectionRequest = false, $apiVersion = self::API_VERSION_V1
    )
    {
        $response = $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'GET', $endpoint, [], $returnClass, $isCollectionRequest, $apiVersion
        );

        if ($isCollectionRequest) {
            return $this->parseCollectionResponse($response, $returnClass);
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
        $endpoint, $returnClass = null, $isCollectionRequest = false, $apiVersion = self::API_VERSION_V1
    )
    {
        $response = $this->createAndExecuteRequestWithDelegatedAccessToken(
            'GET', $endpoint, [], $returnClass, $isCollectionRequest, $apiVersion
        );

        if ($isCollectionRequest) {
            return $this->parseCollectionResponse($response, $returnClass);
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
     * @return \Microsoft\Graph\Model\Entity A Microsoft Graph Entity-instance of type $returnClass
     */
    public function executePatchWithAccessTokenExpirationRetry(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'PATCH', $endpoint, $requestBody, $returnClass, false, $apiVersion
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
            'PATCH', $endpoint, $requestBody, $returnClass, $apiVersion
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
     */
    public function executePostWithAccessTokenExpirationRetry(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
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
     * @throws \Exception
     */
    public function executePostWithDelegatedAccess(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithDelegatedAccessToken(
            'POST', $endpoint, $requestBody, $returnClass, $apiVersion
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
     */
    public function executePutWithAccessTokenExpirationRetry(
        $endpoint, $requestBody = [], $returnClass = null, $apiVersion = self::API_VERSION_V1
    )
    {
        return $this->createAndExecuteRequestWithAccessTokenExpirationRetry(
            'PUT', $endpoint, $requestBody, $returnClass, false, $apiVersion
        );
    }

    /**
     * Executes a request in the graph API with an additional try if the access token has expired by refreshing
     * the access token and executing the request again.
     *
     * @param \Microsoft\Graph\Http\GraphRequest $graphRequest
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\ClientException $exception
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    protected function executeRequestWithAccessTokenExpirationRetry(GraphRequest $graphRequest)
    {
        try {
            return $graphRequest->execute();
        }
        catch (ClientException $exception) {
            if ($exception->getCode() == self::RESPONSE_CODE_ACCESS_TOKEN_EXPIRED) {
                $accessToken = $this->requestNewApplicationAccessToken();
                $this->getGraphServiceClient()->setAccessToken($accessToken);
                $graphRequest->addHeaders(['Authorization' => 'Bearer ' . $accessToken]);

                return $graphRequest->execute();
            }

            throw $exception;
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

        if (!$this->delegatedAccessToken instanceof AccessToken) {
            throw new Exception('The delegated access token could not be activated');
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
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface\AccessTokenRepositoryInterface
     */
    protected function getAccessTokenRepository()
    {
        return $this->accessTokenRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface\AccessTokenRepositoryInterface $accessTokenRepository
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
     * @param \League\OAuth2\Client\Token\AccessToken $delegatedAccessToken
     */
    protected function setDelegatedAccessToken(AccessToken $delegatedAccessToken = null)
    {
        $this->delegatedAccessToken = $delegatedAccessToken;
    }

    protected function getGraphServiceClient(): GraphServiceClient
    {
        return $this->graphServiceClient;
    }

    protected function setGraphServiceClient(GraphServiceClient $graphServiceClient): static
    {
        $this->graphServiceClient = $graphServiceClient;

        return $this;
    }

    protected function getOauthProvider(): AbstractProvider
    {
        return $this->oauthProvider;
    }

    protected function setOauthProvider(AbstractProvider $oauthProvider): static
    {
        $this->oauthProvider = $oauthProvider;

        return $this;
    }

    /**
     * Initializes the access token
     */
    protected function initializeApplicationAccessToken()
    {
        $accessToken = $this->getAccessTokenRepository()->getApplicationAccessToken();

        if (!$accessToken instanceof AccessToken || $accessToken->hasExpired()) {
            $accessToken = $this->requestNewApplicationAccessToken();
        }

        $this->getGraphServiceClient()->setAccessToken($accessToken);
        $this->setDelegatedAccessToken($this->getAccessTokenRepository()->getDelegatedAccessToken());
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
    protected function parseCollectionResponse(GraphResponse $graphResponse, $returnType)
    {
        $body = $graphResponse->getBody();

        $count = 0;

        if (array_key_exists('@odata.count', $body)) {
            $count = $body['@odata.count'];
        }
        elseif (array_key_exists('value', $body)) {
            $count = count($body['value']);
        }

        return ($count > 0) ? $graphResponse->getResponseAsObject($returnType) : [];
    }

    /**
     * Returns the access token
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function requestNewApplicationAccessToken()
    {
        $accessToken = $this->getOauthProvider()->getAccessToken(
            'client_credentials', ['resource' => 'https://graph.microsoft.com/']
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
}