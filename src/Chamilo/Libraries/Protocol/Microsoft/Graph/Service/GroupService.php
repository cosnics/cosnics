<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use Microsoft\Graph\Model\Group;
use RuntimeException;

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
     * @param \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
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
     * Adds a member to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function addMemberToGroup($groupId, User $user)
    {
        if (!$this->isMemberOfGroup($groupId, $user))
        {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            if (empty($azureUserIdentifier))
            {
                throw new AzureUserNotExistsException($user);
            }

            $this->getGroupRepository()->subscribeMemberInGroup($groupId, $azureUserIdentifier);
        }
    }

    /**
     * Adds a owner to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
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

            $this->getGroupRepository()->subscribeOwnerInGroup($groupId, $azureUserIdentifier);
        }
    }

    /**
     * Creates a group by a given name
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $groupName
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createGroupByName(User $owner, $groupName)
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($owner);

        if (empty($azureUserIdentifier))
        {
            throw new AzureUserNotExistsException($owner);
        }

        $group = $this->getGroupRepository()->createGroup($groupName);
        $this->getGroupRepository()->subscribeOwnerInGroup($group->getId(), $azureUserIdentifier);

        return $group->getId();
    }

    /**
     * Creates a new plan for a given group
     *
     * @param string $groupId
     * @param string $planName
     *
     * @return string
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
     * Returns the identifier in azure active directory for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getAzureUserIdentifier(User $user)
    {
        return $this->getUserService()->getAzureUserIdentifier($user);
    }

    /**
     * Returns the first plan identifier of a given group
     *
     * @param string $groupId
     *
     * @return string
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
     * @param string $groupId
     *
     * @return Group
     * @throws GroupNotExistsException
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

    /**
     * Returns a list of external user identifiers that are subscribed as member in an Azure AD group
     *
     * @param string $groupId
     *
     * @return string[]
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
     * Returns a list of external user identifiers that are subscribed as owner in an Azure AD group
     *
     * @param string $groupId
     *
     * @return string[]
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
     * Returns a list of all the plan identifiers of a given group
     *
     * @param string $groupId
     *
     * @return string[]
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
     * Returns the URI for the given group
     *
     * @param string $groupId
     *
     * @return string
     */
    public function getGroupUrl($groupId)
    {
        $groupUrl = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'group_base_uri']
        );

        $group = $this->groupRepository->getGroup($groupId);
        if (!$group instanceof Group)
        {
            throw new RuntimeException(
                'The group with identifier ' . $groupId . ' could not be found'
            );
        }

        return str_replace('{GROUP_ID}', $group->getMailNickname(), $groupUrl);
    }

    /**
     * Returns or creates a new plan based on a given group
     *
     * @param string $groupId
     *
     * @return string
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
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param integer $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
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
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param integer $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function isOwnerOfGroup($groupId, User $user)
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
     * Removes all the members from a given group
     *
     * @param string $groupId
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
     * Removes all the owners from a given group
     *
     * @param string $groupId
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
     * Removes a member from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function removeMemberFromGroup($groupId, User $user)
    {
        if ($this->isMemberOfGroup($groupId, $user))
        {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);
            $this->getGroupRepository()->removeMemberFromGroup($groupId, $azureUserIdentifier);
        }
    }

    /**
     * Removes a owner from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
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
     * @param ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Syncs the given users to the given groups. Optionally excluding some users from being removed
     *
     * @param integer $groupId
     * @param User[] $users
     * @param User[] | null $excludedUsersForRemoval
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function syncUsersToGroup($groupId, $users = [], $excludedUsersForRemoval = [])
    {
        $group = $this->groupRepository->getGroup($groupId);
        if (!$group instanceof Group)
        {
            throw new RuntimeException(
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
     * Updates the name of a group
     *
     * @param string $groupId
     * @param string $groupName
     */
    public function updateGroupName($groupId, $groupName)
    {
        $this->getGroupRepository()->updateGroup($groupId, $groupName);
    }
}