<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365Repository
{
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
    }

    public function isAccessTokenValid($accessToken)
    {
        return '';
    }

    public function refreshAccessToken($accessToken)
    {
        return '';
    }

    public function getAccessToken()
    {
        return '';
    }

    public function getOffice365UserIdentifier(User $user)
    {
        return '';
    }

    /**
     * @param string $accessToken
     * @param string $groupName
     *
     * @return \Microsoft\Graph\Model\Group
     */
    public function createGroup($accessToken, $groupName)
    {
        $this->graph->setAccessToken($accessToken);

        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName,
            'mailEnabled' => false,
            'groupTypes'=> [
                'Unified',
            ],
            'securityEnabled' => false
        ];

        return $this->graph->createRequest("POST", "/groups")
            ->attachBody($groupData)
            ->setReturnType(\Microsoft\Graph\Model\Group::class)
            ->execute();
    }

    /**
     * @param string $accessToken
     * @param \Microsoft\Graph\Model\Group $group
     * @param string $office365UserIdentifier
     */
    public function subscribeOwnerInGroup($accessToken, Group $group, $office365UserIdentifier)
    {
        $this->graph->setAccessToken($accessToken);

        $this->graph->createRequest("POST", "/groups/" . $group->getId() . 'owners/$ref')
            ->attachBody(["@odata.id" => "https://graph.microsoft.com/v1.0/users/" . $office365UserIdentifier])
            ->setReturnType(\Microsoft\Graph\Model\Event::class)
            ->execute();
    }

}