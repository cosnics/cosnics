<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use GuzzleHttp\Exception\ClientException;
use Microsoft\Graph\Model\Event;
use Microsoft\Graph\Model\Group;
use Microsoft\Graph\Model\PlannerPlan;
use Microsoft\Graph\Model\User;
use Symfony\Component\Uid\Uuid;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupRepository
{

    /**
     * @var string
     */
    protected $cosnicsPrefix;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    private $graphRepository;

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     * @param string $cosnicsPrefix
     */
    public function __construct(GraphRepository $graphRepository, $cosnicsPrefix = '')
    {
        $this->setGraphRepository($graphRepository);
        $this->cosnicsPrefix = $cosnicsPrefix;
    }

    /**
     * Creates a new group by a given name
     *
     * @param string $groupName
     *
     * @return \Microsoft\Graph\Model\Group | \Microsoft\Graph\Model\Entity
     */
    public function createGroup($groupName)
    {
        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName,
            'mailEnabled' => false,
            'mailNickname' => str_replace(
                '-', '_', $this->cosnicsPrefix . Uuid::v4()
            ),
            'groupTypes' => [
                'Unified',
            ],
            'securityEnabled' => false,
            'visibility' => 'private'
        ];

        return $this->getGraphRepository()->executePostWithAccessTokenExpirationRetry(
            '/groups', $groupData, Group::class
        );
    }

    /**
     * Creates a new plan for a given group
     *
     * @param string $groupIdentifier
     * @param string $planName
     *
     * @return \Microsoft\Graph\Model\Entity | \Microsoft\Graph\Model\PlannerPlan
     */
    public function createPlanForGroup($groupIdentifier, $planName)
    {
        return $this->getGraphRepository()->executePostWithDelegatedAccess(
            '/planner/plans', ['owner' => $groupIdentifier, 'title' => $planName], PlannerPlan::class
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    protected function getGraphRepository()
    {
        return $this->graphRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     */
    protected function setGraphRepository(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     * Returns a group by a given identifier
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\Group | \Microsoft\Graph\Model\Entity
     */
    public function getGroup($groupIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier, Group::class
        );
    }

    /**
     *
     * @param string $groupId
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\User | \Microsoft\Graph\Model\Entity
     */
    public function getGroupMember($groupId, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/groups/' . $groupId . '/members/' . $azureUserIdentifier, User::class
            );
        }
        catch (ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                return null;
            }

            throw $exception;
        }
    }

    /**
     *
     * @param string $groupId
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\User | \Microsoft\Graph\Model\Entity
     */
    public function getGroupOwner($groupId, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/groups/' . $groupId . '/owners/' . $azureUserIdentifier, User::class
            );
        }
        catch (ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
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
     * @return \Microsoft\Graph\Model\User[] | \Microsoft\Graph\Model\Entity
     */
    public function listGroupMembers($groupIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier . '/members', User::class, true
        );
    }

    /**
     * Lists the owners of a given group
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\User[] | \Microsoft\Graph\Model\Entity
     */
    public function listGroupOwners($groupIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier . '/owners', User::class, true
        );
    }

    /**
     * Lists the plans for a given group
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\PlannerPlan[] | \Microsoft\Graph\Model\Entity
     */
    public function listGroupPlans($groupIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithDelegatedAccess(
            '/groups/' . $groupIdentifier . '/planner/plans', PlannerPlan::class, true
        );
    }

    /**
     * Removes an owner from a given group
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     */
    public function removeMemberFromGroup($groupIdentifier, $azureUserIdentifier)
    {
        return $this->getGraphRepository()->executeDeleteWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier . '/members/' . $azureUserIdentifier . '/$ref', Event::class
        );
    }

    /**
     * Removes an owner from a given group
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     */
    public function removeOwnerFromGroup($groupIdentifier, $azureUserIdentifier)
    {
        return $this->getGraphRepository()->executeDeleteWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier . '/owners/' . $azureUserIdentifier . '/$ref', Event::class
        );
    }

    /**
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     */
    public function subscribeMemberInGroup($groupIdentifier, $azureUserIdentifier)
    {
        return $this->getGraphRepository()->executePostWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier . '/members/$ref',
            ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $azureUserIdentifier], Event::class
        );
    }

    /**
     * Subscribes an owner to a group
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     */
    public function subscribeOwnerInGroup($groupIdentifier, $azureUserIdentifier)
    {
        return $this->getGraphRepository()->executePostWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier . '/owners/$ref',
            ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $azureUserIdentifier], Event::class
        );
    }

    /**
     * Updates a group name by a given identifier
     *
     * @param string $groupIdentifier
     * @param string $groupName
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     */
    public function updateGroup($groupIdentifier, $groupName)
    {
        $groupData = ['description' => $groupName, 'displayName' => $groupName];

        return $this->getGraphRepository()->executePatchWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier, $groupData, Event::class
        );
    }
}