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
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
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
    public function countSubscribedUsersByGroupIdentifier(
        string $groupIdentifier, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupMembershipRepository->countSubscribedUsersByGroupIdentifier(
            $groupIdentifier, $condition
        );
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupMembershipRepository->countSubscribedUsersByGroupIdentifiers(
            $groupIdentifiers, $condition
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersByGroup(Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
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

            $this->groupUsersCount[$cacheKey] = $this->countSubscribedUsersByGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUsersCount[$cacheKey];
    }

    public function deleteGroupMembership(GroupRelUser $groupRelUser, ?User $executingUser = null): bool
    {
        if (!$this->groupMembershipRepository->deleteGroupMembership($groupRelUser)) {
            throw new RuntimeException(
                sprintf(
                    'Could not unsubscribe user %s from group %s', $groupRelUser->getUserId(),
                    $groupRelUser->getGroupId()
                )
            );
        }

        $this->eventDispatcher->dispatch(
            new AfterGroupUnsubscribeEvent($groupRelUser->getGroupId(), $groupRelUser->getUserId(), $executingUser)
        );

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function deleteGroupMembershipByGroupAndUser(Group $group, User $user, ?User $executingUser = null): bool
    {
        $groupRelation = $this->groupMembershipRepository->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
            $group->getId(), $user->getId()
        );

        if (!$groupRelation instanceof GroupRelUser) {
            throw new RuntimeException(
                sprintf(
                    'Could not unsubscribe user %s from group %s because there is no active subscription',
                    $user->getId(), $group->getId()
                )
            );
        }

        return $this->deleteGroupMembership($groupRelation, $executingUser);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByGroup(Group $group, ?User $executingUser = null): bool
    {
        $groupUserRelations =
            $this->groupMembershipRepository->retrieveGroupMembershipsByGroupIdentifier($group->getId());

        foreach ($groupUserRelations as $groupUserRelation) {
            if (!$this->groupMembershipRepository->deleteGroupMembership($groupUserRelation)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not unsubscribe user %s from group %s', $groupUserRelation->getUserId(),
                        $groupUserRelation->getGroupId()
                    )
                );
            }

            $this->eventDispatcher->dispatch(
                new AfterGroupUnsubscribeEvent(
                    $group->getId(), $groupUserRelation->getUserId(), $executingUser
                )
            );
        }

        $this->eventDispatcher->dispatch(new AfterGroupEmptyEvent($group, $executingUser));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function deleteGroupMembershipsByUser(User $user, ?User $executingUser = null): bool
    {
        $groupMemberships = $this->retrieveGroupMembershipsByUserIdentifier($user->getId());

        foreach ($groupMemberships as $groupMembership) {
            $this->deleteGroupMembership($groupMembership, $executingUser);
        }

        return true;
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
    public function removeGroupMembershipsByGroupIdentifiers(array $groupIdentifiers): bool
    {
        return $this->groupMembershipRepository->deleteGroupMembershipsByGroupIdentifiers($groupIdentifiers);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveGroupMembershipByGroupAndUser(Group $group, User $user): ?GroupRelUser
    {
        return $this->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier($group->getId(), $user->getId());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveGroupMembershipByGroupCodeAndUser(string $groupCode, User $user): ?GroupRelUser
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipByGroupCodeAndUserIdentifier(
            $groupCode, $user->getId()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
        string $groupIdentifier, string $userIdentifier
    ): ?GroupRelUser
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
            $groupIdentifier, $userIdentifier
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveGroupMembershipByIdentifier(string $groupMembershipIdentifier): ?GroupRelUser
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipByIdentifier($groupMembershipIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupRelUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupMembershipsByGroupIdentifier(string $groupIdentifier): ArrayCollection
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipsByGroupIdentifier($groupIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupRelUser>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupMembershipsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipsByUserIdentifier($userIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        return $this->groupMembershipRepository->retrieveGroupsByUserIdentifier($userIdentifier);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveSubscribedUserIdentifiersByGroupIdentifier(string $groupIdentifier): array
    {
        return $this->retrieveSubscribedUserIdentifiersByGroupIdentifiers([$groupIdentifier]);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveSubscribedUserIdentifiersByGroupIdentifiers(array $groupIdentifiers): array
    {
        return $this->groupMembershipRepository->retrieveSubscribedUserIdentifiersByGroupIdentifiers(
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
    public function retrieveSubscribedUsersByGroupIdentifier(
        string $groupIdentifier, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->retrieveSubscribedUsersByGroupIdentifiers([$groupIdentifier], $condition, $offset, $count,
            $orderBy);
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
    public function retrieveSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->groupMembershipRepository->retrieveSubscribedUsersByGroupIdentifiers(
            $groupIdentifiers, $condition, $offset, $count, $orderBy
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveUserIdentifiersByGroup(
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
                $this->retrieveSubscribedUserIdentifiersByGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUserIdentifiers[$cacheKey];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function subscribeUserToGroup(Group $group, User $user, ?User $executingUser = null): GroupRelUser
    {
        try {
            $groupRelation =
                $this->groupMembershipRepository->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
                    $group->getId(), $user->getId()
                );
        }
        catch (StorageNoResultException) {
            $groupRelation = new GroupRelUser();

            $groupRelation->setUserId($user->getId());
            $groupRelation->setGroupId($group->getId());

            $this->groupMembershipRepository->createGroupMembership($groupRelation);

            $this->eventDispatcher->dispatch(
                new AfterGroupSubscribeEvent($group->getId(), $user->getId(), $executingUser)
            );
        }

        return $groupRelation;
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function synchronizeGroup(Group $group, array $userIdentifiers, ?User $executingUser = null): bool
    {
        $currentUserIdentifiers = $this->retrieveSubscribedUserIdentifiersByGroupIdentifier($group->getId());

        $newUserIdentifiers = array_diff($userIdentifiers, $currentUserIdentifiers);
        $oldUserIdentifiers = array_diff($currentUserIdentifiers, $userIdentifiers);

        $newUsers = $this->userService->findUsersByIdentifiers($newUserIdentifiers);

        foreach ($newUsers as $newUser) {
            $this->subscribeUserToGroup($group, $newUser, $executingUser);
        }

        $oldUsers = $this->userService->findUsersByIdentifiers($oldUserIdentifiers);

        foreach ($oldUsers as $oldUser) {
            $this->deleteGroupMembershipByGroupAndUser($group, $oldUser, $executingUser);
        }

        return true;
    }
}