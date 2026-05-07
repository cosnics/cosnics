<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupEmptyEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupSubscribeEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUnsubscribeEvent;
use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupMembership;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
                    $this->groupsTreeTraverser->retrieveDescendantIdentifiersByGroup($group, $recursiveSubgroups);
            }
            else {
                $groupIdentifiers = [];
            }

            $groupIdentifiers[] = $group->getId();

            $this->groupUsersCount[$cacheKey] = $this->countSubscribedUsersByGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUsersCount[$cacheKey];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function createGroupMembershipForGroupAndUser(Group $group, User $user, ?User $executingUser = null
    ): GroupMembership
    {
        try {
            $groupMembership = new GroupMembership();

            $groupMembership->setUserId($user->getId());
            $groupMembership->setGroupId($group->getId());

            $this->groupMembershipRepository->createGroupMembership($groupMembership);

            $this->eventDispatcher->dispatch(
                new AfterGroupSubscribeEvent($group->getId(), $user->getId(), $executingUser)
            );
        }
        catch (ObjectAlreadyExistsException) {
            $groupMembership =
                $this->groupMembershipRepository->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
                    $group->getId(), $user->getId()
                );
        }

        return $groupMembership;
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function createGroupMembershipForGroupAndUserIdentifiers(
        Group $group, array $userIdentifiers, ?User $executingUser = null
    ): ArrayCollection
    {
        $groupMemberships = new ArrayCollection();

        foreach ($userIdentifiers as $userIdentifier) {
            try {
                $user = $this->userService->retrieveUserByIdentifier($userIdentifier);
                $groupMemberships->add($this->createGroupMembershipForGroupAndUser($group, $user, $executingUser));
            }
            catch (NoSuchUserException) {
            }
        }

        return $groupMemberships;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembership(GroupMembership $groupMembership, ?User $executingUser = null): void
    {
        $this->groupMembershipRepository->deleteGroupMembership($groupMembership);

        $this->eventDispatcher->dispatch(
            new AfterGroupUnsubscribeEvent(
                $groupMembership->getGroupId(), $groupMembership->getUserId(), $executingUser
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipByGroupAndUser(Group $group, User $user, ?User $executingUser = null): void
    {
        try {
            $groupMembership =
                $this->groupMembershipRepository->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
                    $group->getId(), $user->getId()
                );

            $this->deleteGroupMembership($groupMembership, $executingUser);
        }
        catch (NoSuchGroupMembershipException) {
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipByIdentifier(string $groupMembershipIdentifier, ?User $executingUser = null
    ): void
    {
        try {
            $this->deleteGroupMembership(
                $this->retrieveGroupMembershipByIdentifier($groupMembershipIdentifier), $executingUser
            );
        }
        catch (NoSuchGroupMembershipException) {
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByGroup(Group $group, ?User $executingUser = null): void
    {
        $groupMemberships =
            $this->groupMembershipRepository->retrieveGroupMembershipsByGroupIdentifier($group->getId());

        foreach ($groupMemberships as $groupMembership) {
            $this->deleteGroupMembership($groupMembership);
        }

        $this->eventDispatcher->dispatch(new AfterGroupEmptyEvent($group, $executingUser));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByIdentifiers(array $groupMembershipIdentifiers, ?User $executingUser = null
    ): void
    {
        foreach ($groupMembershipIdentifiers as $groupMembershipIdentifier) {
            $this->deleteGroupMembershipByIdentifier($groupMembershipIdentifier, $executingUser);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByUser(User $user, ?User $executingUser = null): void
    {
        $groupMemberships = $this->retrieveGroupMembershipsByUserIdentifier($user->getId());

        foreach ($groupMemberships as $groupMembership) {
            $this->deleteGroupMembership($groupMembership, $executingUser);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByGroupAndUser(Group $group, User $user): ?GroupMembership
    {
        return $this->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier($group->getId(), $user->getId());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByGroupCodeAndUser(string $groupCode, User $user): ?GroupMembership
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipByGroupCodeAndUserIdentifier(
            $groupCode, $user->getId()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
        string $groupIdentifier, string $userIdentifier
    ): ?GroupMembership
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
            $groupIdentifier, $userIdentifier
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByIdentifier(string $groupMembershipIdentifier): ?GroupMembership
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipByIdentifier($groupMembershipIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupMembershipsByGroupIdentifier(string $groupIdentifier): ArrayCollection
    {
        return $this->groupMembershipRepository->retrieveGroupMembershipsByGroupIdentifier($groupIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
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
                    $this->groupsTreeTraverser->retrieveDescendantIdentifiersByGroup($group, $recursiveSubgroups);
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
     * @param string[] $userIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function synchronizeGroup(Group $group, array $userIdentifiers, ?User $executingUser = null): void
    {
        $currentUserIdentifiers = $this->retrieveSubscribedUserIdentifiersByGroupIdentifier($group->getId());

        $newUserIdentifiers = array_diff($userIdentifiers, $currentUserIdentifiers);
        $oldUserIdentifiers = array_diff($currentUserIdentifiers, $userIdentifiers);

        $newUsers = $this->userService->retrieveUsersByIdentifiers($newUserIdentifiers);

        foreach ($newUsers as $newUser) {
            $this->createGroupMembershipForGroupAndUser($group, $newUser, $executingUser);
        }

        $oldUsers = $this->userService->retrieveUsersByIdentifiers($oldUserIdentifiers);

        foreach ($oldUsers as $oldUser) {
            $this->deleteGroupMembershipByGroupAndUser($group, $oldUser, $executingUser);
        }
    }
}