<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchGroupException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException;
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
    public function __construct(
        protected UserService $userService, protected GroupRepository $groupRepository, protected string $groupBaseUri
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function addMemberToGroup(string $groupId, User $user): bool
    {
        if (!$this->isMemberOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getEntraUserIdentifier($user);

            if (empty($azureUserIdentifier)) {
                throw new NoSuchUserException($user);
            }

            return $this->groupRepository->subscribeMemberInGroup($groupId, $azureUserIdentifier);
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function addOwnerToGroup(string $groupId, User $user): bool
    {
        if (!$this->isOwnerOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getEntraUserIdentifier($user);

            if (empty($azureUserIdentifier)) {
                throw new NoSuchUserException($user);
            }

            return $this->groupRepository->subscribeOwnerInGroup($groupId, $azureUserIdentifier);
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Exception
     */
    public function createGroupByName(User $owner, string $groupName): ?string
    {
        $azureUserIdentifier = $this->getEntraUserIdentifier($owner);

        if (empty($azureUserIdentifier)) {
            throw new NoSuchUserException($owner);
        }

        $group = $this->groupRepository->createGroup($groupName);
        $this->groupRepository->subscribeOwnerInGroup($group->getId(), $azureUserIdentifier);

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
     * @throws \Exception
     */
    public function getDefaultGroupPlanId(string $groupId): ?string
    {
        $groupPlans = $this->groupRepository->listGroupPlans($groupId);

        if (empty($groupPlans)) {
            return null;
        }

        return $groupPlans[0]->getId();
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getEntraUserIdentifier(User $user): ?string
    {
        return $this->userService->getAndSaveUserIdentifier($user);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchGroupException
     * @throws \Exception
     */
    public function getGroup(string $groupId): Group
    {
        return $this->groupRepository->getGroup($groupId);
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getGroupMembers(string $groupId): array
    {
        $userIdentifiers = [];

        $groupMembers = $this->groupRepository->listGroupMembers($groupId);
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

        $groupOwners = $this->groupRepository->listGroupOwners($groupId);
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

        foreach ($this->groupRepository->listGroupPlans($groupId) as $groupPlan) {
            $groupPlanIds[] = $groupPlan->getId();
        }

        return $groupPlanIds;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchGroupException
     */
    public function getGroupUrl(string $groupId): string
    {
        $group = $this->groupRepository->getGroup($groupId);

        return str_replace('%GroupId', $group->getMailNickname(), $this->groupBaseUri);
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

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function isMemberOfGroup(string $groupId, User $user): bool
    {
        $azureUserIdentifier = $this->getEntraUserIdentifier($user);
        if (empty($azureUserIdentifier)) {
            return false;
        }

        try {
            $this->groupRepository->getGroupMember($groupId, $azureUserIdentifier);

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    public function isOwnerOfGroup(string $groupId, User $user): bool
    {
        try {
            $azureUserIdentifier = $this->getEntraUserIdentifier($user);

            if (empty($azureUserIdentifier)) {
                return false;
            }

            $this->groupRepository->getGroupOwner($groupId, $azureUserIdentifier);

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
            if (!$this->groupRepository->removeMemberFromGroup($groupId, $groupMember)) {
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
            if (!$this->groupRepository->removeOwnerFromGroup($groupId, $groupOwner)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function removeMemberFromGroup(string $groupId, User $user): bool
    {
        if ($this->isMemberOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getEntraUserIdentifier($user);

            return $this->groupRepository->removeMemberFromGroup($groupId, $azureUserIdentifier);
        }

        return false;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function removeOwnerFromGroup(string $groupId, User $user): bool
    {
        if ($this->isOwnerOfGroup($groupId, $user)) {
            $azureUserIdentifier = $this->getEntraUserIdentifier($user);

            return $this->groupRepository->removeOwnerFromGroup($groupId, $azureUserIdentifier);
        }

        return false;
    }

    /**
     * @param string $groupId
     * @param array<\Chamilo\Core\User\Storage\DataClass\User> $users
     * @param ?array<\Chamilo\Core\User\Storage\DataClass\User> $excludedUsersForRemoval
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Exception
     */
    public function syncUsersToGroup(string $groupId, array $users = [], ?array $excludedUsersForRemoval = []): void
    {
        try {
            $this->groupRepository->getGroup($groupId);

            $currentAzureUserIdentifiers = [];

            foreach ($users as $user) {
                $azureUserIdentifier = $this->userService->getUserIdentifier($user);
                if (!empty($azureUserIdentifier)) {
                    $currentAzureUserIdentifiers[] = $azureUserIdentifier;
                }
            }

            $excludedUsersForRemovalIdentifiers = [];
            foreach ($excludedUsersForRemoval as $user) {
                $azureUserIdentifier = $this->userService->getUserIdentifier($user);
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
        catch (NoSuchGroupException) {
            throw new RuntimeException(
                'The group with identifier ' . $groupId . ' could not be found'
            );
        }
    }

    public function updateGroupName(string $groupId, string $groupName): bool
    {
        return $this->groupRepository->updateGroup($groupId, $groupName);
    }
}