<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;

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
     * GroupService constructor
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository $groupRepository
     */
    public function __construct(UserService $userService, GroupRepository $groupRepository)
    {
        $this->userService = $userService;
        $this->groupRepository = $groupRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository
     */
    public function getGroupRepository()
    {
        return $this->groupRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository $groupRepository
     */
    public function setGroupRepository(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Returns the identifier in azure active directory for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return string
     */
    protected function getAzureUserIdentifier(User $user)
    {
        return $this->getUserService()->getAzureUserIdentifier($user);
    }

    /**
     * Creates a group by a given name
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $groupName
     * @return string
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createGroupByName(User $owner, $groupName)
    {
        // TODO: Temporarily hardcode this to avoid new groups being created
        return 'e5dcbd72-8938-4ed2-9b31-fbac1b04ea3b';
        $azureUserIdentifier = $this->getAzureUserIdentifier($owner);

        if (empty($azureUserIdentifier))
        {
            throw new AzureUserNotExistsException($owner);
        }

        $group = $this->getGroupRepository()->createGroup($groupName);
        $this->getGroupRepository()->subscribeMemberInGroup($group, $azureUserIdentifier);

        return $group->getId();
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

    /**
     * Adds a member to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function addMemberToGroup($groupId, User $user)
    {
        if (! $this->isMemberOfGroup($groupId, $user))
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
     * Removes a member from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
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
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param integer $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
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
     * Adds a owner to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function addOwnerToGroup($groupId, User $user)
    {
        if (! $this->isOwnerOfGroup($groupId, $user))
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
     * Removes a owner from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function removeOwnerFromGroup($groupId, User $user)
    {
        if ($this->isOwnerOfGroup($groupId, $user))
        {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            if (empty($azureUserIdentifier))
            {
                throw new AzureUserNotExistsException($user);
            }

            $this->getGroupRepository()->removeOwnerFromGroup($groupId, $azureUserIdentifier);
        }
    }

    /**
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param integer $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
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
     * Returns a list of external user identifiers that are subscribed as owner in an Azure AD group
     *
     * @param string $groupId
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
     * Returns a list of all the plan identifiers of a given group
     *
     * @param string $groupId
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
     * Returns the first plan identifier of a given group
     *
     * @param string $groupId
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
}