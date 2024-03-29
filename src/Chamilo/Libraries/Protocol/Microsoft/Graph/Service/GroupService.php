<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use GuzzleHttp\Exception\ClientException;
use Microsoft\Graph\Model\Group;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected $userService;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository
     */
    protected $groupRepository;

    /**
     * @var ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * GroupService constructor
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository $groupRepository
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        UserService $userService, GroupRepository $groupRepository, ConfigurationConsulter $configurationConsulter
    )
    {
        $this->setUserService($userService);
        $this->setGroupRepository($groupRepository);
        $this->setConfigurationConsulter($configurationConsulter);
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     */
    protected function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository
     */
    protected function getGroupRepository()
    {
        return $this->groupRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository $groupRepository
     */
    protected function setGroupRepository(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Returns the identifier in azure active directory for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    protected function getAzureUserIdentifier(User $user, bool $forceNewRetrieve = false)
    {
        return $this->getUserService()->getAzureUserIdentifier($user, $forceNewRetrieve);
    }

    /**
     * Creates a group by a given name
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $groupName
     *
     * @return string
     * @throws AzureUserNotExistsException
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws UnknownAzureUserIdException
     */
    public function createGroupByName(User $owner, $groupName)
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($owner);

        if (empty($azureUserIdentifier))
        {
            throw new AzureUserNotExistsException($owner);
        }

        $group = $this->getGroupRepository()->createGroup($groupName);

        /** IMPORTANT: SUBSCRIBE USER AS MEMBER AND AS OWNER TO MAKE CORRECT FUNCTIONALITY */
        $this->getGroupRepository()->subscribeMemberInGroup($group->getId(), $azureUserIdentifier);
        $this->getGroupRepository()->subscribeOwnerInGroup($group->getId(), $azureUserIdentifier);

        return $group->getId();
    }

    /**
     * Updates the name of a group
     *
     * @param string $groupId
     * @param string $groupName
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function updateGroupName($groupId, $groupName)
    {
        $this->getGroupRepository()->updateGroup($groupId, $groupName);
    }

    /**
     * Adds a member to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws AzureUserNotExistsException
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws UnknownAzureUserIdException
     */
    public function addMemberToGroup(string $groupId, User $user)
    {
        if (!$this->isMemberOfGroup($groupId, $user))
        {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            // Retry once more by forcing the retrieval of the azure user identifier directly from graph
            if (empty($azureUserIdentifier))
            {
                $azureUserIdentifier = $this->getAzureUserIdentifier($user, true);
            }

            if (empty($azureUserIdentifier))
            {
                throw new AzureUserNotExistsException($user);
            }

            /** BUG IN MICROSOFT: SUBSCRIBE MEMBER REMOVES AN OWNER BUT THE OWNER SHOULD BE BOTH MEMBER AND OWNER  */
            if ($this->isOwnerOfGroup($groupId, $user))
            {
                $this->getGroupRepository()->subscribeMemberInGroup($groupId, $azureUserIdentifier);

                try
                {
                    $this->getGroupRepository()->subscribeOwnerInGroup($groupId, $azureUserIdentifier);
                }
                catch (\Exception $exception)
                {
                    //TODO: check if subscribe member bug is fixed. It seems so because
                    // GRAPH api is returning already subscribed errors
                }
            }
            else
            {
                $this->getGroupRepository()->subscribeMemberInGroup($groupId, $azureUserIdentifier);
            }
        }
    }

    /**
     * Removes a member from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws AzureUserNotExistsException
     */
    public function removeMemberFromGroup($groupId, User $user)
    {
        if ($this->isMemberOfGroup($groupId, $user))
        {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            if (empty($azureUserIdentifier))
            {
                throw new AzureUserNotExistsException($user);
            }

            $this->getGroupRepository()->removeMemberFromGroup($groupId, $azureUserIdentifier);
        }
    }

    /**
     * @param string $groupId
     * @param string $azureMemberId
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeMemberFromGroupByAzureId(string $groupId, string $azureMemberId)
    {
        $this->getGroupRepository()->removeMemberFromGroup($groupId, $azureMemberId);
    }

    /**
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param integer $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function isMemberOfGroup($groupId, User $user)
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($user);
        if (empty($azureUserIdentifier))
        {
            return false;
        }

        $groupMember = $this->getGroupRepository()->getGroupMember($groupId, $azureUserIdentifier);

        return $groupMember instanceof \Microsoft\Graph\Model\User;
    }

    /**
     * Returns a list of external user identifiers that are subscribed as member in an Azure AD group
     *
     * @param string $groupId
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroupMembers($groupId)
    {
        $userIdentifiers = [];

        $groupMembers = $this->getGroupRepository()->listGroupMembers($groupId);
        foreach ($groupMembers as $groupMember)
        {
            $userIdentifiers[] = $groupMember->getId();
        }

        return $userIdentifiers;
    }

    /**
     * Removes all the members from a given group
     *
     * @param string $groupId
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeAllMembersFromGroup($groupId)
    {
        $groupMembers = $this->getGroupMembers($groupId);
        foreach ($groupMembers as $groupMember)
        {
            $this->getGroupRepository()->removeMemberFromGroup($groupId, $groupMember);
        }
    }

    /**
     * Adds a owner to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws AzureUserNotExistsException
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function addOwnerToGroup($groupId, User $user)
    {
        if (!$this->isOwnerOfGroup($groupId, $user))
        {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            if (empty($azureUserIdentifier))
            {
                throw new AzureUserNotExistsException($user);
            }

            try
            {
                /** BUG IN MICROSOFT: THE OWNER SHOULD BE BOTH MEMBER AND OWNER  */
                $this->getGroupRepository()->subscribeMemberInGroup($groupId, $azureUserIdentifier);
            }
            catch (\Exception $ex)
            {
            }

            $this->getGroupRepository()->subscribeOwnerInGroup($groupId, $azureUserIdentifier);
        }
    }

    /**
     * Removes a owner from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeOwnerFromGroup($groupId, User $user)
    {
        if ($this->isOwnerOfGroup($groupId, $user))
        {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);
            $this->getGroupRepository()->removeOwnerFromGroup($groupId, $azureUserIdentifier);
        }
    }

    /**
     * @param string $groupId
     * @param string $ownerAzureId
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeOwnerByAzureId(string $groupId, string $ownerAzureId)
    {
        $this->getGroupRepository()->removeOwnerFromGroup($groupId, $ownerAzureId);
    }

    /**
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function isOwnerOfGroup(string $groupId, User $user)
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($user);

        if (empty($azureUserIdentifier))
        {
            return false;
        }

        $groupOwner = $this->getGroupRepository()->getGroupOwner($groupId, $azureUserIdentifier);

        return $groupOwner instanceof \Microsoft\Graph\Model\User;
    }

    /**
     * Returns a list of external user identifiers that are subscribed as owner in an Azure AD group
     *
     * @param string $groupId
     *
     * @return string[]
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroupOwners($groupId)
    {
        $userIdentifiers = [];

        $groupOwners = $this->getGroupRepository()->listGroupOwners($groupId);
        foreach ($groupOwners as $groupOwner)
        {
            $userIdentifiers[] = $groupOwner->getId();
        }

        return $userIdentifiers;
    }

    /**
     * Removes all the owners from a given group
     *
     * @param string $groupId
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeAllOwnersFromGroup($groupId)
    {
        $groupOwners = $this->getGroupOwners($groupId);

        foreach ($groupOwners as $groupOwner)
        {
            $this->getGroupRepository()->removeOwnerFromGroup($groupId, $groupOwner);
        }
    }

    /**
     * Returns a list of all the plan identifiers of a given group
     *
     * @param string $groupId
     *
     * @return string[]
     * @throws \Exception
     */
    public function getGroupPlanIds($groupId)
    {
        $groupPlanIds = [];

        foreach ($this->getGroupRepository()->listGroupPlans($groupId) as $groupPlan)
        {
            $groupPlanIds[] = $groupPlan->getId();
        }

        return $groupPlanIds;
    }

    /**
     * Returns the first plan identifier of a given group
     *
     * @param string $groupId
     *
     * @return string
     * @throws \Exception
     */
    public function getDefaultGroupPlanId($groupId)
    {
        $groupPlans = $this->getGroupRepository()->listGroupPlans($groupId);

        if (empty($groupPlans))
        {
            return null;
        }

        return $groupPlans[0]->getId();
    }

    /**
     * Creates a new plan for a given group
     *
     * @param string $groupId
     * @param string $planName
     *
     * @return string
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function createPlanForGroup($groupId, $planName = null)
    {
        if (empty($planName))
        {
            $group = $this->groupRepository->getGroup($groupId);
            $planName = $group->getDisplayName();
        }

        $plan = $this->groupRepository->createPlanForGroup($groupId, $planName);

        return $plan->getId();
    }

    /**
     * Returns or creates a new plan based on a given group
     *
     * @param string $groupId
     *
     * @return string
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getOrCreatePlanIdForGroup($groupId)
    {
        $planId = $this->getDefaultGroupPlanId($groupId);

        if (empty($planId))
        {
            $planId = $this->createPlanForGroup($groupId);
        }

        return $planId;
    }

    /**
     * Returns the URI for the given group
     *
     * @param string $groupId
     *
     * @return string
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroupUrl($groupId)
    {
        $groupUrl = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'group_base_uri']
        );

        $group = $this->groupRepository->getGroup($groupId);
        if (!$group instanceof Group)
        {
            throw new \RuntimeException(
                'The group with identifier ' . $groupId . ' could not be found'
            );
        }

        return str_replace('{GROUP_ID}', $group->getMailNickname(), $groupUrl);
    }

    /**
     * Syncs the given users to the given groups. Optionally excluding some users from being removed
     *
     * @param int $groupId
     * @param User[] $users
     * @param User[] | null $excludedUsersForRemoval
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function syncUsersToGroup($groupId, $users = array(), $excludedUsersForRemoval = [])
    {
        $group = $this->groupRepository->getGroup($groupId);
        if (!$group instanceof Group)
        {
            throw new \RuntimeException(
                'The group with identifier ' . $groupId . ' could not be found'
            );
        }

        $currentAzureUserIdentifiers = [];

        foreach ($users as $user)
        {
            $azureUserIdentifier = $this->userService->getAzureUserIdentifier($user);
            if (!empty($azureUserIdentifier))
            {
                $currentAzureUserIdentifiers[] = $azureUserIdentifier;
            }
        }

        $excludedUsersForRemovalIdentifiers = [];
        foreach ($excludedUsersForRemoval as $user)
        {
            $azureUserIdentifier = $this->userService->getAzureUserIdentifier($user);
            if (!empty($azureUserIdentifier))
            {
                $excludedUsersForRemovalIdentifiers[] = $azureUserIdentifier;
            }
        }

        // NEVER REMOVE OWNERS AS MEMBER WHILE SYNCHING
        $owners = $this->getGroupRepository()->listGroupOwners($groupId);
        foreach ($owners as $owner)
        {
            $excludedUsersForRemovalIdentifiers[] = $owner->getId();
        }

        $office365GroupMemberIdentifiers = $this->getGroupMembers($groupId);

        $usersToAdd = array_diff($currentAzureUserIdentifiers, $office365GroupMemberIdentifiers);
        foreach ($usersToAdd as $userToAdd)
        {
            $this->groupRepository->subscribeMemberInGroup($groupId, $userToAdd);
        }

        $usersToRemove = array_diff($office365GroupMemberIdentifiers, $currentAzureUserIdentifiers);
        if (!empty($excludedUsersForRemovalIdentifiers))
        {
            $usersToRemove = array_diff($usersToRemove, $excludedUsersForRemovalIdentifiers);
        }

        foreach ($usersToRemove as $userToRemove)
        {
            $this->groupRepository->removeMemberFromGroup($groupId, $userToRemove);
        }
    }

    /**
     * @param string $groupId
     * @param User[] $members
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroupMemberAzureIdsNotInArray(string $groupId, array $members): array
    {
        return array_diff($this->getGroupMembers($groupId), $this->userService->getAzureUserIdentifiers($members));
    }

    /**
     * @param string $groupId
     * @param User[] $owners
     *
     * @return string[]
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroupOwnerAzureIdsNotInArray(string $groupId, array $owners): array
    {
        return array_diff($this->getGroupOwners($groupId), $this->userService->getAzureUserIdentifiers($owners));
    }

    /**
     * @param string $groupId
     * @param User[] $members
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeGroupMembersNotInArray(string $groupId, array $members)
    {
        foreach ($this->getGroupMemberAzureIdsNotInArray($groupId, $members) as $memberAzureId)
        {
            $this->removeMemberFromGroupByAzureId($groupId, $memberAzureId);
        }
    }

    /**
     * @param string $groupId
     * @param User[] $owners
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeGroupOwnersNotInArray(string $groupId, array $owners)
    {
        foreach ($this->getGroupOwnerAzureIdsNotInArray($groupId, $owners) as $memberAzureId)
        {
            $this->removeOwnerByAzureId($groupId, $memberAzureId);
        }
    }

    /**
     * @param string $groupId
     *
     * @return Group
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getGroup(string $groupId): Group
    {
        $group = $this->groupRepository->getGroup($groupId);

        if (!$group instanceof Group)
        {
            throw new GroupNotExistsException($groupId);
        }

        return $group;
    }
}
