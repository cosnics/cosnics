<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException;
use Chamilo\Libraries\Utilities\UUID;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    private $graphRepository;

    /**
     * @var string
     */
    protected $cosnicsPrefix;

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
     * Creates a new group by a given name
     *
     * @param string $groupName
     *
     * @return \Microsoft\Graph\Model\Group | \Microsoft\Graph\Model\Entity
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function createGroup($groupName)
    {
        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName,
            'mailEnabled' => false,
            'mailNickname' => str_replace(
                '-', '_',
                $this->cosnicsPrefix . UUID::v4()
            ),
            'groupTypes' => [
                'Unified',
            ],
            'securityEnabled' => false,
            'visibility' => 'private'
        ];

        return $this->getGraphRepository()->executePostWithAccessTokenExpirationRetry(
            '/groups',
            $groupData,
            \Microsoft\Graph\Model\Group::class
        );
    }

    /**
     * Updates a group name by a given identifier
     *
     * @param string $groupIdentifier
     * @param string $groupName
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function updateGroup($groupIdentifier, $groupName)
    {
        try
        {
            $groupData = ['description' => $groupName, 'displayName' => $groupName];

            return $this->getGraphRepository()->executePatchWithAccessTokenExpirationRetry(
                '/groups/' . $groupIdentifier,
                $groupData,
                \Microsoft\Graph\Model\Event::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                throw new GroupNotExistsException($groupIdentifier);
            }

            throw $exception;
        }
    }

    /**
     * Returns a group by a given identifier
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\Group | \Microsoft\Graph\Model\Entity
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroup($groupIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/groups/' . $groupIdentifier, \Microsoft\Graph\Model\Group::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                throw new GroupNotExistsException($groupIdentifier);
            }

            throw $exception;
        }
    }

    /**
     * Subscribes an owner to a group
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function subscribeOwnerInGroup($groupIdentifier, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executePostWithAccessTokenExpirationRetry(
                '/groups/' . $groupIdentifier . '/owners/$ref',
                ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $azureUserIdentifier],
                \Microsoft\Graph\Model\Event::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                throw new GroupNotExistsException($groupIdentifier);
            }

            throw $exception;
        }
    }

    /**
     * Removes an owner from a given group
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeOwnerFromGroup($groupIdentifier, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeDeleteWithAccessTokenExpirationRetry(
                '/groups/' . $groupIdentifier . '/owners/' . $azureUserIdentifier . '/$ref',
                \Microsoft\Graph\Model\Event::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                throw new GroupNotExistsException($groupIdentifier);
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroupOwner($groupId, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/groups/' . $groupId . '/owners/' . $azureUserIdentifier,
                \Microsoft\Graph\Model\User::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
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
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function listGroupOwners($groupIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/groups/' . $groupIdentifier . '/owners',
                \Microsoft\Graph\Model\User::class, true
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                throw new GroupNotExistsException($groupIdentifier);
            }

            throw $exception;
        }

    }

    /**
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function subscribeMemberInGroup($groupIdentifier, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executePostWithAccessTokenExpirationRetry(
                '/groups/' . $groupIdentifier . '/members/$ref',
                ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $azureUserIdentifier],
                \Microsoft\Graph\Model\Event::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                //could also be that the user is not found.
                $userNotFoundMsgPosition = strpos(
                    $exception->getResponse()->getBody()->getContents(),
                    'Resource \'' . $azureUserIdentifier . '\' does not exist'
                );
                if($userNotFoundMsgPosition !== false) {
                    throw new UnknownAzureUserIdException($azureUserIdentifier);
                } else {
                    throw new GroupNotExistsException($groupIdentifier);
                }
            }

            throw $exception;
        }
    }

    /**
     * Removes an owner from a given group
     *
     * @param string $groupIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Event | \Microsoft\Graph\Model\Entity
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeMemberFromGroup($groupIdentifier, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeDeleteWithAccessTokenExpirationRetry(
                '/groups/' . $groupIdentifier . '/members/' . $azureUserIdentifier . '/$ref',
                \Microsoft\Graph\Model\Event::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                throw new GroupNotExistsException($groupIdentifier);
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroupMember($groupId, $azureUserIdentifier)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/groups/' . $groupId . '/members/' . $azureUserIdentifier,
                \Microsoft\Graph\Model\User::class
            );
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function listGroupMembers($groupIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/groups/' . $groupIdentifier . '/members',
            \Microsoft\Graph\Model\User::class, true
        );
    }

    /**
     * Lists the plans for a given group
     *
     * @param string $groupIdentifier
     *
     * @return \Microsoft\Graph\Model\PlannerPlan[] | \Microsoft\Graph\Model\Entity
     * @throws \Exception
     */
    public function listGroupPlans($groupIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithDelegatedAccess(
            '/groups/' . $groupIdentifier . '/planner/plans',
            \Microsoft\Graph\Model\PlannerPlan::class, true
        );
    }

    /**
     * Creates a new plan for a given group
     *
     * @param string $groupIdentifier
     * @param string $planName
     *
     * @return \Microsoft\Graph\Model\Entity | \Microsoft\Graph\Model\PlannerPlan
     * @throws \Exception
     */
    public function createPlanForGroup($groupIdentifier, $planName)
    {
        return $this->getGraphRepository()->executePostWithDelegatedAccess(
            '/planner/plans',
            ['owner' => $groupIdentifier, 'title' => $planName],
            \Microsoft\Graph\Model\PlannerPlan::class
        );
    }
}
