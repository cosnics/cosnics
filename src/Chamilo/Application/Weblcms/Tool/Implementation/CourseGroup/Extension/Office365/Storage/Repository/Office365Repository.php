<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use JsonSchema\Exception\ResourceNotFoundException;
use Microsoft\Graph\Http\GraphRequest;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365Repository
{
    const RESPONSE_CODE_RESOURCE_NOT_FOUND = '404';
    const RESPONSE_CODE_ACCESS_TOKEN_EXPIRED = '405';

    /**
     * @var \TheNetworg\OAuth2\Client\Provider\Azure
     */
    protected $azureProvider;

    /**
     * @var \Microsoft\Graph\Graph
     */
    protected $graph;

    /**
     * Office365Repository constructor.
     *
     * @param \TheNetworg\OAuth2\Client\Provider\Azure $azureProvider
     * @param \Microsoft\Graph\Graph $graph
     */
    public function __construct(\TheNetworg\OAuth2\Client\Provider\Azure $azureProvider, \Microsoft\Graph\Graph $graph)
    {
        $this->azureProvider = $azureProvider;
        $this->graph = $graph;

        $this->graph->setAccessToken($this->getAccessToken());
    }

    /**
     * Returns the access token
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function getAccessToken()
    {
        return $this->azureProvider->getAccessToken(
            'client_credentials',
            ['resource' => 'https://graph.microsoft.com/']
        );
    }

    /**
     * Returns the identifier for the given Chamilo user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function getOffice365UserIdentifier(User $user)
    {
        $member = $this->getOffice365User($user);
        return $member->getId();
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
                //TODO: store new access token
                $accessToken = $this->getAccessToken();
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
            $this->graph->createRequest('POST', '/groups/' . $groupIdentifier . 'owners/$ref')
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
            $this->graph->createRequest('POST', '/groups/' . $groupIdentifier . 'members/$ref')
                ->attachBody(['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $office365UserIdentifier])
                ->setReturnType(\Microsoft\Graph\Model\Event::class)
        );
    }

}