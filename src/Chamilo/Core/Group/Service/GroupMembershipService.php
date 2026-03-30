<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupEmptyEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupSubscribeEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUnsubscribeEvent;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipService
{
    /**
     * @var int[][]
     */
    protected array $groupUserIdentifiers = [];

    /**
     * @var int[]
     */
    protected array $groupUsersCount = [];

    public function __construct(
        protected readonly GroupMembershipRepository $groupMembershipRepository,
        protected readonly EventDispatcherInterface $eventDispatcher, protected readonly UserService $userService,
        protected readonly GroupsTreeTraverser $groupsTreeTraverser
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countSubscribedUsersForGroupIdentifier(
        string $groupIdentifier, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupMembershipRepository->countSubscribedUsersForGroupIdentifier(
            $groupIdentifier, $condition
        );
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countSubscribedUsersForGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupMembershipRepository->countSubscribedUsersForGroupIdentifiers(
            $groupIdentifiers, $condition
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersForGroup(Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
    ): int
    {
        $cacheKey = md5(serialize([$group->getId(), $includeSubGroups, $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->groupUsersCount)) {
            if ($includeSubGroups) {
                $groupIdentifiers =
                    $this->groupsTreeTraverser->findSubGroupIdentifiersForGroup($group, $recursiveSubgroups);
            }
            else {
                $groupIdentifiers = [];
            }

            $groupIdentifiers[] = $group->getId();

            $this->groupUsersCount[$cacheKey] = $this->countSubscribedUsersForGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUsersCount[$cacheKey];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function emptyGroup(Group $group, ?User $executingUser = null): bool
    {
        $impactedUserIds = $this->findUserIdentifiersForGroup($group);

        $success = $this->groupMembershipRepository->emptyGroup($group);

        if (!$success) {
            throw new RuntimeException('Could not empty the group with id ' . $group->getId());
        }

        $this->eventDispatcher->dispatch(new AfterGroupEmptyEvent($group, $impactedUserIds, $executingUser));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findGroupRelUserByIdentifier(string $groupRelUserIdentifier): ?GroupRelUser
    {
        return $this->groupMembershipRepository->findGroupRelUserByIdentifier($groupRelUserIdentifier);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findSubscribedUserIdentifiersForGroupIdentifier(string $groupIdentifier): array
    {
        return $this->findSubscribedUserIdentifiersForGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findSubscribedUserIdentifiersForGroupIdentifiers(array $groupIdentifiers): array
    {
        return $this->groupMembershipRepository->findSubscribedUserIdentifiersForGroupIdentifiers(
            $groupIdentifiers
        );
    }

    /**
     * @param string $groupIdentifier
     * @param ?\Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface $condition
     * @param ?int $offset
     * @param ?int $count
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\SubscribedUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findSubscribedUsersForGroupIdentifier(
        string $groupIdentifier, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->findSubscribedUsersForGroupIdentifiers([$groupIdentifier], $condition, $offset, $count, $orderBy);
    }

    /**
     * @param string[] $groupIdentifiers
     * @param ?\Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface $condition
     * @param ?int $offset
     * @param ?int $count
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\SubscribedUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findSubscribedUsersForGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->groupMembershipRepository->findSubscribedUsersForGroupIdentifiers(
            $groupIdentifiers, $condition, $offset, $count, $orderBy
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserIdentifiersForGroup(
        Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
    ): array
    {
        $cacheKey = md5(serialize([$group->getId(), $includeSubGroups, $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->groupUserIdentifiers)) {
            if ($includeSubGroups) {
                $groupIdentifiers =
                    $this->groupsTreeTraverser->findSubGroupIdentifiersForGroup($group, $recursiveSubgroups);
            }
            else {
                $groupIdentifiers = [];
            }

            $groupIdentifiers[] = $group->getId();

            $this->groupUserIdentifiers[$cacheKey] =
                $this->findSubscribedUserIdentifiersForGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUserIdentifiers[$cacheKey];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroupUserRelationByGroupAndUser(Group $group, User $user): ?GroupRelUser
    {
        return $this->groupMembershipRepository->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroupUserRelationByGroupCodeAndUser(string $groupCode, User $user): ?GroupRelUser
    {
        return $this->groupMembershipRepository->findGroupRelUserByGroupCodeAndUserId($groupCode, $user->getId());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroupUserRelationByGroupIdentifierAndUserIdentifier(
        string $groupIdentifier, string $userIdentifier
    ): ?GroupRelUser
    {
        return $this->groupMembershipRepository->findGroupUserRelationByGroupIdentifierAndUserIdentifier(
            $groupIdentifier, $userIdentifier
        );
    }

    /**
     * @param string $groupIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupRelUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getGroupUserRelationsByGroupIdentifier(string $groupIdentifier): ArrayCollection
    {
        return $this->groupMembershipRepository->getGroupUserRelationsByGroupIdentifier($groupIdentifier);
    }

    /**
     * Shortcut method to remove the users from a group by the group identifiers, only directly after removal of the
     * groups because no notifiers are called. This is due to the fact that a group removal already triggers an event
     * and therefore this clean-up action of the users after a delete should not trigger a new event.
     *
     * @param string[] $groupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function removeUsersFromGroupsByIdsAfterRemoval(array $groupIdentifiers): bool
    {
        return $this->groupMembershipRepository->unsubscribeUsersFromGroupIdentifiers($groupIdentifiers);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function subscribeUserToGroup(Group $group, User $user, ?User $executingUser = null): GroupRelUser
    {
        $groupRelation =
            $this->groupMembershipRepository->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser) {
            $groupRelation = new GroupRelUser();

            $groupRelation->setUserId($user->getId());
            $groupRelation->setGroupId($group->getId());

            if (!$this->groupMembershipRepository->createGroupUserRelation($groupRelation)) {
                throw new RuntimeException(
                    sprintf('Could not subscribe user %s to group %s', $user->getId(), $group->getId())
                );
            }

            $this->eventDispatcher->dispatch(new AfterGroupSubscribeEvent($group, $user, $executingUser));
        }

        return $groupRelation;
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function synchronizeGroup(Group $group, array $userIdentifiers, ?User $executingUser = null): bool
    {
        $currentUserIdentifiers = $this->findSubscribedUserIdentifiersForGroupIdentifier($group->getId());

        $newUserIdentifiers = array_diff($userIdentifiers, $currentUserIdentifiers);
        $oldUserIdentifiers = array_diff($currentUserIdentifiers, $userIdentifiers);

        $newUsers = $this->userService->findUsersByIdentifiers($newUserIdentifiers);

        foreach ($newUsers as $newUser) {
            $this->subscribeUserToGroup($group, $newUser, $executingUser);
        }

        $oldUsers = $this->userService->findUsersByIdentifiers($oldUserIdentifiers);

        foreach ($oldUsers as $oldUser) {
            $this->unsubscribeUserFromGroup($group, $oldUser, $executingUser);
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function unsubscribeAllUsersFromGroup(Group $group, ?User $executingUser = null): bool
    {
        $groupUserRelations = $this->groupMembershipRepository->getGroupUserRelationsByGroupIdentifier($group->getId());

        foreach ($groupUserRelations as $groupUserRelation) {
            if (!$this->groupMembershipRepository->deleteGroupUserRelation($groupUserRelation)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not unsubscribe user %s from group %s', $groupUserRelation->getUserId(),
                        $groupUserRelation->getGroupId()
                    )
                );
            }

            $this->eventDispatcher->dispatch(
                new AfterGroupUnsubscribeEvent(
                    $group, $this->userService->findUserByIdentifier($groupUserRelation->getUserId()), $executingUser
                )
            );
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function unsubscribeUserFromAllGroups(User $user): bool
    {
        $groups = $this->groupsTreeTraverser->findDirectlySubscribedGroupsForUserIdentifier($user->getId());

        foreach ($groups as $group) {
            $this->unsubscribeUserFromGroup($group, $user);
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function unsubscribeUserFromGroup(Group $group, User $user, ?User $executingUser = null): bool
    {
        $groupRelation =
            $this->groupMembershipRepository->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser) {
            throw new RuntimeException(
                sprintf(
                    'Could not unsubscribe user %s from group %s because there is no active subscription',
                    $user->getId(), $group->getId()
                )
            );
        }

        if (!$this->groupMembershipRepository->deleteGroupUserRelation($groupRelation)) {
            throw new RuntimeException(
                sprintf('Could not unsubscribe user %s from group %s', $user->getId(), $group->getId())
            );
        }

        $this->eventDispatcher->dispatch(new AfterGroupUnsubscribeEvent($group, $user, $executingUser));

        return true;
    }
}