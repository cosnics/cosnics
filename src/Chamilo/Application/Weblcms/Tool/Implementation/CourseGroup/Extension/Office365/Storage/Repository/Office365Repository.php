<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     * @var AccessToken
     */
    protected $delegatedAccessToken;

    /**
     * @var string
     */
    protected $currentRequestUrl;

    /**
     * Office365Repository constructor.
     *
     * @param \League\OAuth2\Client\Provider\AbstractProvider $oauthProvider
     * @param \Microsoft\Graph\Graph $graph
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     * @param string $currentRequestUrl
     */
    public function __construct(
        AbstractProvider $oauthProvider, Graph $graph, AccessTokenRepositoryInterface $accessTokenRepository,
        $currentRequestUrl
    )
    {
        $this->oauthProvider = $oauthProvider;
        $this->graph = $graph;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->currentRequestUrl = $currentRequestUrl;

        $this->initializeApplicationAccessToken();
    }

    /**
     * Initializes the access token
     */
    protected function initializeApplicationAccessToken()
    {
        $accessToken = $this->accessTokenRepository->getApplicationAccessToken();
        if (!$accessToken instanceof AccessToken || $accessToken->hasExpired())
        {
            $accessToken = $this->requestNewApplicationAccessToken();
        }

        $this->graph->setAccessToken($accessToken);

        $this->delegatedAccessToken = $this->accessTokenRepository->getDelegatedAccessToken();
    }

    /**
     * Returns the access token
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function requestNewApplicationAccessToken()
    {
        $accessToken = $this->oauthProvider->getAccessToken(
            'client_credentials',
            ['resource' => 'https://graph.microsoft.com/']
        );

        $this->accessTokenRepository->storeApplicationAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * Requests a new access token for the user, redirecting the user to the authorization URL
     */
    protected function requestNewDelegatedAccessToken()
    {
        $authorizationUrl = $this->oauthProvider->getAuthorizationUrl(['state' => $this->oauthProvider->getState()]);

        $redirectResponse = new RedirectResponse($authorizationUrl);
        $redirectResponse->send();
    }

    /**
     * Sets the user access token as the currently to use access token
     */
    protected function activateDelegatedAccessToken()
    {
        if (empty($this->delegatedAccessToken) || !$this->delegatedAccessToken instanceof AccessToken)
        {
            $this->requestNewDelegatedAccessToken();
        }

        $this->graph->setAccessToken($this->delegatedAccessToken);
    }

    /**
     * Authorizes a user by a given authorization code
     *
     * @param string $authorizationCode
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        $this->delegatedAccessToken = $this->oauthProvider->getAccessToken(
            'authorization_code', ['code' => $authorizationCode, 'resource' => 'https://graph.microsoft.com/']
        );

        $this->accessTokenRepository->storeDelegatedAccessToken($this->delegatedAccessToken);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Microsoft\Graph\Model\User
     */
    public function getOffice365User(User $user)
    {
        try
        {
            return $this->executeRequestWithAccessTokenExpirationRetry(
                $this->graph->createRequest('GET', '/users/' . $user->get_email())
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
                $accessToken = $this->requestNewApplicationAccessToken();
                $this->graph->setAccessToken($accessToken);
                $graphRequest->addHeaders(['Authorization' => 'Bearer ' . $accessToken]);

                return $graphRequest->execute();
            }

            throw $exception;
        }
    }

    /**
     * Creates a new group by a given name
     *
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
     * Updates a group name by a given identifier
     *
     * @param string $groupIdentifier
     * @param string $groupName
     *
     * @return \Microsoft\Graph\Model\Event
     */
    public function updateGroup($groupIdentifier, $groupName)
    {
        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName
        ];

        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest('PATCH', '/groups/' . $groupIdentifier)
                ->attachBody($groupData)
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
        );
    }

    /**
     * Subscribes an owner to a group
     *
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
     * Removes an owner from a given group
     *
     * @param string $groupIdentifier
     * @param string $office365UserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event
     */
    public function removeOwnerFromGroup($groupIdentifier, $office365UserIdentifier)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest(
                'DELETE', '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier . '/$ref'
            )
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
        );
    }

    /**
     * @param string $groupId
     * @param string $office365UserIdentifier
     *
     * @return \Microsoft\Graph\Model\User
     */
    public function getGroupOwner($groupId, $office365UserIdentifier)
    {
        try
        {
            return $this->executeRequestWithAccessTokenExpirationRetry(
                $this->graph->createRequest('GET', '/groups/' . $groupId . '/owners/' . $office365UserIdentifier)
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
     * Lists the owners of a given group
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\User[]
     */
    public function listGroupOwners($groupIdentifier)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest('GET', '/groups/' . $groupIdentifier . '/owners')
                ->setReturnType(\Microsoft\Graph\Model\User::class)
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

    /**
     * Removes an owner from a given group
     *
     * @param string $groupIdentifier
     * @param string $office365UserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event
     */
    public function removeMemberFromGroup($groupIdentifier, $office365UserIdentifier)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest(
                'DELETE', '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier . '/$ref'
            )
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
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
     * Lists the owners of a given group
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\User[]
     */
    public function listGroupMembers($groupIdentifier)
    {
        return $this->executeRequestWithAccessTokenExpirationRetry(
            $this->graph->createRequest('GET', '/groups/' . $groupIdentifier . '/members')
                ->setReturnType(\Microsoft\Graph\Model\User::class)
        );
    }

    /**
     * Lists the plans for a given group
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\PlannerPlan[]
     */
    public function listGroupPlans($groupIdentifier)
    {
        $this->activateDelegatedAccessToken();

        $request = $this->graph->createRequest('GET', '/groups/' . $groupIdentifier . '/planner/plans')
            ->setReturnType(\Microsoft\Graph\Model\PlannerPlan::class);

        $result = $request->execute();
        $this->initializeApplicationAccessToken();

        return $result;
    }
}