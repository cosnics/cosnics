<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use RuntimeException;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipService
{
    protected GroupEventNotifier $groupEventNotifier;

    protected GroupMembershipRepository $groupMembershipRepository;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected UserService $userService;

    public function __construct(
        GroupMembershipRepository $groupMembershipRepository, GroupEventNotifier $groupEventNotifier,
        UserService $userService
    )
    {
        $this->groupMembershipRepository = $groupMembershipRepository;
        $this->groupEventNotifier = $groupEventNotifier;
        $this->userService = $userService;
    }

    /**
     * @param int $groupIdentifier
     *
     * @return int
     * @throws \Exception
     */
    public function countSubscribedUsersForGroupIdentifier(int $groupIdentifier)
    {
        return $this->getGroupMembershipRepository()->countSubscribedUsersForGroupIdentifier($groupIdentifier);
    }

    /**
     * @param int $groupIdentifiers
     *
     * @return int
     * @throws \Exception
     */
    public function countSubscribedUsersForGroupIdentifiers(array $groupIdentifiers)
    {
        return $this->getGroupMembershipRepository()->countSubscribedUsersForGroupIdentifiers(
            $groupIdentifiers
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @throws \Exception
     */
    public function emptyGroup(Group $group)
    {
        $impactedUserIds = $this->groupsTreeTraverser->findUserIdentifiersForGroup($group);

        $success = $this->getGroupMembershipRepository()->emptyGroup($group);
        if (!$success)
        {
            throw new RuntimeException('Could not empty the group with id ' . $group->getId());
        }

        $this->groupEventNotifier->afterEmptyGroup($group, $impactedUserIds);
    }

    /**
     * @param int $groupIdentifier
     *
     * @return int[]
     * @throws \Exception
     */
    public function findSubscribedUserIdentifiersForGroupIdentifier(int $groupIdentifier): array
    {
        return $this->findSubscribedUserIdentifiersForGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param int[] $groupIdentifiers
     *
     * @return int[]
     * @throws \Exception
     */
    public function findSubscribedUserIdentifiersForGroupIdentifiers(array $groupIdentifiers): array
    {
        return $this->getGroupMembershipRepository()->findSubscribedUserIdentifiersForGroupIdentifiers(
            $groupIdentifiers
        );
    }

    /**
     * @param int[] $groupIdentifiers
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\SubscribedUser>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSubscribedUsersForGroupIdentifier(
        int $groupIdentifier, ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->findSubscribedUsersForGroupIdentifiers([$groupIdentifier], $condition, $offset, $count, $orderBy);
    }

    /**
     * @param int[] $groupIdentifiers
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\SubscribedUser>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSubscribedUsersForGroupIdentifiers(
        array $groupIdentifiers, ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getGroupMembershipRepository()->findSubscribedUsersForGroupIdentifiers(
            $groupIdentifiers, $condition, $offset, $count, $orderBy
        );
    }

    /**
     * @return \Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository
     */
    public function getGroupMembershipRepository(): GroupMembershipRepository
    {
        return $this->groupMembershipRepository;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getGroupUserRelationByGroupAndUser(Group $group, User $user)
    {
        return $this->getGroupMembershipRepository()->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getGroupUserRelationByGroupCodeAndUser(string $groupCode, User $user)
    {
        return $this->getGroupMembershipRepository()->findGroupRelUserByGroupCodeAndUserId($groupCode, $user->getId());
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * Shortcut method to remove the users from a group by the group identifiers, only directly after removal of the
     * groups because no notifiers are called. This is due to the fact that a group removal already triggers an event
     * and therefore this cleanup action of the users after a delete should not trigger a new event.
     *
     * @param int $groupIdentifiers
     *
     * @return bool
     * @throws \Exception
     */
    public function removeUsersFromGroupsByIdsAfterRemoval(array $groupIdentifiers)
    {
        return $this->groupMembershipRepository->unsubscribeUsersFromGroupIdentifiers($groupIdentifiers);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository $groupMembershipRepository
     */
    public function setGroupMembershipRepository(GroupMembershipRepository $groupMembershipRepository): void
    {
        $this->groupMembershipRepository = $groupMembershipRepository;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     *
     * @return GroupMembershipService
     */
    public function setUserService(UserService $userService): GroupMembershipService
    {
        $this->userService = $userService;

        return $this;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Exception
     */
    public function subscribeUserToGroup(Group $group, User $user)
    {
        $groupRelation =
            $this->getGroupMembershipRepository()->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser)
        {
            $groupRelation = new GroupRelUser();
            $groupRelation->set_user_id($user->getId());
            $groupRelation->set_group_id($group->getId());

            if (!$this->getGroupMembershipRepository()->createGroupUserRelation($groupRelation))
            {
                throw new RuntimeException(
                    sprintf('Could not subscribe user %s to group %s', $user->getId(), $group->getId())
                );
            }

            $this->groupEventNotifier->afterSubscribe($group, $user);
        }

        return $groupRelation;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int $userIdentifiers
     *
     * @throws \Exception
     */
    public function synchronizeGroup(Group $group, array $userIdentifiers)
    {
        $currentUserIdentifiers = $this->findSubscribedUserIdentifiersForGroupIdentifier($group->getId());

        $newUserIdentifiers = array_diff($userIdentifiers, $currentUserIdentifiers);
        $oldUserIdentifiers = array_diff($currentUserIdentifiers, $userIdentifiers);

        $newUsers = $this->getUserService()->findUsersByIdentifiers($newUserIdentifiers);

        foreach ($newUsers as $newUser)
        {
            $this->subscribeUserToGroup($group, $newUser);
        }

        $oldUsers = $this->getUserService()->findUsersByIdentifiers($oldUserIdentifiers);

        foreach ($oldUsers as $oldUser)
        {
            $this->unsubscribeUserFromGroup($group, $oldUser);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUserFromGroup(Group $group, User $user)
    {
        $groupRelation =
            $this->getGroupMembershipRepository()->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser)
        {
            throw new RuntimeException(
                sprintf(
                    'Could not unsubscribe user %s from group %s because there is no active subscription',
                    $user->getId(), $group->getId()
                )
            );
        }

        if (!$this->getGroupMembershipRepository()->deleteGroupUserRelation($groupRelation))
        {
            throw new RuntimeException(
                sprintf('Could not unsubscribe user %s from group %s', $user->getId(), $group->getId())
            );
        }

        $this->groupEventNotifier->afterUnsubscribe($group, $user);
    }
}