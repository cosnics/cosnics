<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphRequest;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365Repository
{
    const RESPONSE_CODE_RESOURCE_NOT_FOUND = '404';
    const RESPONSE_CODE_ACCESS_TOKEN_EXPIRED = '401';

    /**
     * @var \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $oauthProvider;

    /**
     * @var \Microsoft\Graph\Graph
     */
    protected $graph;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface
     */
    protected $accessTokenRepository;

    /**
     * Office365Repository constructor.
     *
     * @param \League\OAuth2\Client\Provider\AbstractProvider $oauthProvider
     * @param \Microsoft\Graph\Graph $graph
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function __construct(
        AbstractProvider $oauthProvider, Graph $graph, AccessTokenRepositoryInterface $accessTokenRepository
    )
    {
        $this->oauthProvider = $oauthProvider;
        $this->graph = $graph;
        $this->accessTokenRepository = $accessTokenRepository;

        $this->initializeAccessToken();
    }

    /**
     * Initializes the access token
     */
    protected function initializeAccessToken()
    {
        $accessToken = $this->accessTokenRepository->getAccessToken();
        if (!$accessToken instanceof AccessToken || $accessToken->hasExpired())
        {
            $accessToken = $this->requestNewAccessToken();
        }

        $this->graph->setAccessToken($accessToken);
    }

    /**
     * Returns the access token
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function requestNewAccessToken()
    {
        $accessToken = $this->oauthProvider->getAccessToken(
            'client_credentials',
            ['resource' => 'https://graph.microsoft.com/']
        );

        $this->accessTokenRepository->storeAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Microsoft\Graph\Model\User
     */
    public function getOffice365User(User $user)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest('GET', '/users/' . $user->get_email())
                ->setReturnType(\Microsoft\Graph\Model\User::class)
        );
    }

    /**
     * @param string $groupId
     * @param string $office365UserIdentifier
     *
     * @return \Microsoft\Graph\Model\User
     */
    public function getGroupMember($groupId, $office365UserIdentifier)
    {
        try
        {
            return $this->executeRequestWithAccessTokenExpirationRetry(
                $this->graph->createRequest('GET', '/groups/' . $groupId . '/members/' . $office365UserIdentifier)
                    ->setReturnType(\Microsoft\Graph\Model\User::class)
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == self::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * Executes a request in the graph API with an additional try if the access token has expired by refreshing
     * the access token and executing the request again.
     *
     * @param \Microsoft\Graph\Http\GraphRequest $graphRequest
     *
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
                $accessToken = $this->requestNewAccessToken();
                $this->graph->setAccessToken($accessToken);

                return $graphRequest->execute();
            }

            throw $exception;
        }
    }

    /**
     * @param string $groupName
     *
     * @return \Microsoft\Graph\Model\Group
     */
    public function createGroup($groupName)
    {
        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName,
            'mailEnabled' => false,
            'groupTypes' => [
                'Unified',
            ],
            'securityEnabled' => false
        ];

        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest('POST', '/groups')
                ->attachBody($groupData)
                ->setReturnType(\Microsoft\Graph\Model\Group::class)
        );
    }

    /**
     * @param string $groupIdentifier
     * @param string $office365UserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event
     */
    public function subscribeOwnerInGroup($groupIdentifier, $office365UserIdentifier)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest('POST', '/groups/' . $groupIdentifier . '/owners/$ref')
                ->attachBody(['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $office365UserIdentifier])
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
        );
    }

    /**
     * @param string $groupIdentifier
     * @param string $office365UserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event
     */
    public function subscribeMemberInGroup($groupIdentifier, $office365UserIdentifier)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest('POST', '/groups/' . $groupIdentifier . '/members/$ref')
                ->attachBody(['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $office365UserIdentifier])
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
        );
    }
}