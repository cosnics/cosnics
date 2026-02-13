<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use Exception;
use Microsoft\Graph\Generated\Models\Group;
use RuntimeException;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{
    protected string $groupBaseUri;

    protected GroupRepository $groupRepository;

    protected UserService $userService;

    public function __construct(
        UserService $userService, GroupRepository $groupRepository, string $groupBaseUri
    )
    {
        $this->userService = $userService;
        $this->groupRepository = $groupRepository;
        $this->groupBaseUri = $groupBaseUri;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     */
    public function addMemberToGroup(string $groupId, User $user): bool
    {
        if (!$this->isMemberOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            if (empty($azureUserIdentifier)) {
                throw new UserNotFoundException($user);
            }

            return $this->getGroupRepository()->subscribeMemberInGroup($groupId, $azureUserIdentifier);
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     */
    public function addOwnerToGroup(string $groupId, User $user): bool
    {
        if (!$this->isOwnerOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            if (empty($azureUserIdentifier)) {
                throw new UserNotFoundException($user);
            }

            return $this->getGroupRepository()->subscribeOwnerInGroup($groupId, $azureUserIdentifier);
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     * @throws \Exception
     */
    public function createGroupByName(User $owner, string $groupName): ?string
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($owner);

        if (empty($azureUserIdentifier)) {
            throw new UserNotFoundException($owner);
        }

        $group = $this->getGroupRepository()->createGroup($groupName);
        $this->getGroupRepository()->subscribeOwnerInGroup($group->getId(), $azureUserIdentifier);

        return $group->getId();
    }

    /**
     * @throws \Exception
     */
    public function createPlanForGroup(string $groupId, ?string $planName = null): string
    {
        if (empty($planName)) {
            $group = $this->groupRepository->getGroup($groupId);
            $planName = $group->getDisplayName();
        }

        $plan = $this->groupRepository->createPlanForGroup($groupId, $planName);

        return $plan->getId();
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     */
    protected function getAzureUserIdentifier(User $user): ?string
    {
        return $this->getUserService()->getAndSaveUserIdentifier($user);
    }

    /**
     * @throws \Exception
     */
    public function getDefaultGroupPlanId(string $groupId): ?string
    {
        $groupPlans = $this->getGroupRepository()->listGroupPlans($groupId);

        if (empty($groupPlans)) {
            return null;
        }

        return $groupPlans[0]->getId();
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\GroupNotExistsException
     * @throws \Exception
     */
    public function getGroup(string $groupId): Group
    {
        return $this->groupRepository->getGroup($groupId);
    }

    public function getGroupBaseUri(): string
    {
        return $this->groupBaseUri;
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getGroupMembers(string $groupId): array
    {
        $userIdentifiers = [];

        $groupMembers = $this->getGroupRepository()->listGroupMembers($groupId);
        foreach ($groupMembers as $groupMember) {
            $userIdentifiers[] = $groupMember->getId();
        }

        return $userIdentifiers;
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getGroupOwners(string $groupId): array
    {
        $userIdentifiers = [];

        $groupOwners = $this->getGroupRepository()->listGroupOwners($groupId);
        foreach ($groupOwners as $groupOwner) {
            $userIdentifiers[] = $groupOwner->getId();
        }

        return $userIdentifiers;
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getGroupPlanIds(string $groupId): array
    {
        $groupPlanIds = [];

        foreach ($this->getGroupRepository()->listGroupPlans($groupId) as $groupPlan) {
            $groupPlanIds[] = $groupPlan->getId();
        }

        return $groupPlanIds;
    }

    protected function getGroupRepository(): GroupRepository
    {
        return $this->groupRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\GroupNotExistsException
     */
    public function getGroupUrl(string $groupId): string
    {
        $group = $this->groupRepository->getGroup($groupId);

        return str_replace('%GroupId', $group->getMailNickname(), $this->getGroupBaseUri());
    }

    /**
     * @throws \Exception
     */
    public function getOrCreatePlanIdForGroup(string $groupId): string
    {
        $planId = $this->getDefaultGroupPlanId($groupId);

        if (empty($planId)) {
            $planId = $this->createPlanForGroup($groupId);
        }

        return $planId;
    }

    protected function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     * @throws \Exception
     */
    public function isMemberOfGroup(string $groupId, User $user): bool
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($user);
        if (empty($azureUserIdentifier)) {
            return false;
        }

        try {
            $this->getGroupRepository()->getGroupMember($groupId, $azureUserIdentifier);

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    public function isOwnerOfGroup(string $groupId, User $user): bool
    {
        try {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            if (empty($azureUserIdentifier)) {
                return false;
            }

            $this->getGroupRepository()->getGroupOwner($groupId, $azureUserIdentifier);

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    /**
     * @throws \Exception
     */
    public function removeAllMembersFromGroup($groupId): bool
    {
        $groupMembers = $this->getGroupMembers($groupId);
        foreach ($groupMembers as $groupMember) {
            if (!$this->getGroupRepository()->removeMemberFromGroup($groupId, $groupMember)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function removeAllOwnersFromGroup(string $groupId): bool
    {
        $groupOwners = $this->getGroupOwners($groupId);

        foreach ($groupOwners as $groupOwner) {
            if (!$this->getGroupRepository()->removeOwnerFromGroup($groupId, $groupOwner)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    public function removeMemberFromGroup(string $groupId, User $user): bool
    {
        if ($this->isMemberOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            return $this->getGroupRepository()->removeMemberFromGroup($groupId, $azureUserIdentifier);
        }

        return false;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    public function removeOwnerFromGroup(string $groupId, User $user): bool
    {
        if ($this->isOwnerOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getAzureUserIdentifier($user);

            return $this->getGroupRepository()->removeOwnerFromGroup($groupId, $azureUserIdentifier);
        }

        return false;
    }

    /**
     * @param string $groupId
     * @param array<\Chamilo\Core\User\Storage\DataClass\User> $users
     * @param ?array<\Chamilo\Core\User\Storage\DataClass\User> $excludedUsersForRemoval
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     * @throws \Exception
     */
    public function syncUsersToGroup(string $groupId, array $users = [], ?array $excludedUsersForRemoval = []): void
    {
        try {
            $this->getGroupRepository()->getGroup($groupId);

            $currentAzureUserIdentifiers = [];

            foreach ($users as $user) {
                $azureUserIdentifier = $this->getUserService()->getUserIdentifier($user);
                if (!empty($azureUserIdentifier)) {
                    $currentAzureUserIdentifiers[] = $azureUserIdentifier;
                }
            }

            $excludedUsersForRemovalIdentifiers = [];
            foreach ($excludedUsersForRemoval as $user) {
                $azureUserIdentifier = $this->getUserService()->getUserIdentifier($user);
                if (!empty($azureUserIdentifier)) {
                    $excludedUsersForRemovalIdentifiers[] = $azureUserIdentifier;
                }
            }

            $office365GroupMemberIdentifiers = $this->getGroupMembers($groupId);

            $usersToAdd = array_diff($currentAzureUserIdentifiers, $office365GroupMemberIdentifiers);
            foreach ($usersToAdd as $userToAdd) {
                $this->groupRepository->subscribeMemberInGroup($groupId, $userToAdd);
            }

            $usersToRemove = array_diff($office365GroupMemberIdentifiers, $currentAzureUserIdentifiers);
            if (!empty($excludedUsersForRemovalIdentifiers)) {
                $usersToRemove = array_diff($usersToRemove, $excludedUsersForRemovalIdentifiers);
            }

            foreach ($usersToRemove as $userToRemove) {
                $this->groupRepository->removeMemberFromGroup($groupId, $userToRemove);
            }
        }
        catch (GroupNotExistsException) {
            throw new RuntimeException(
                'The group with identifier ' . $groupId . ' could not be found'
            );
        }
    }

    public function updateGroupName(string $groupId, string $groupName): bool
    {
        return $this->getGroupRepository()->updateGroup($groupId, $groupName);
    }
}