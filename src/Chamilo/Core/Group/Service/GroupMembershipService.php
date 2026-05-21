<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupEmptyEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupSubscribeEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUnsubscribeEvent;
use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\Group\Storage\Repository\GroupEntityRepository;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipEntityRepository;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

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
        protected readonly GroupsTreeTraverser $groupsTreeTraverser,
        protected readonly GroupMembershipEntityRepository $groupMembershipEntityRepository,
        protected readonly GroupEntityRepository $groupEntityRepository,
        protected readonly UserRepository $userRepository
    )
    {
    }

    public function countSubscribedUsersByGroupIdentifier(
        Uuid $groupIdentifier, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupMembershipEntityRepository->countSubscribedUsersByGroupIdentifier(
            $groupIdentifier, $condition
        );
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     */
    public function countSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupMembershipEntityRepository->countSubscribedUsersByGroupIdentifiers(
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createGroupMembershipForGroupAndUser(Group $group, User $user, ?User $executingUser = null
    ): GroupMembership
    {
        try {
            $groupMembership = new GroupMembership();

            $groupMembership->setIdentifier(new UuidV7());
            $groupMembership->setUser($user);
            $groupMembership->setGroup($group);

            $this->groupMembershipEntityRepository->saveGroupMembership($groupMembership);

            $this->eventDispatcher->dispatch(
                new AfterGroupSubscribeEvent($group->getIdentifier(), $user->getIdentifier(), $executingUser)
            );
        }
        catch (EntityAlreadyExistsException) {
            $groupMembership = $this->retrieveGroupMembershipByGroupAndUser($group, $user);
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
                $user = $this->userService->findUserByIdentifier(Uuid::fromString($userIdentifier));
                $groupMemberships->add($this->createGroupMembershipForGroupAndUser($group, $user, $executingUser));
            }
            catch (NoSuchUserException) {
            }
        }

        return $groupMemberships;
    }

    public function deleteGroupMembership(GroupMembership $groupMembership, ?User $executingUser = null): void
    {
        $this->groupMembershipEntityRepository->removeGroupMembership($groupMembership);

        $this->eventDispatcher->dispatch(
            new AfterGroupUnsubscribeEvent(
                $groupMembership->getGroup()->getIdentifier(), $groupMembership->getUser()->getIdentifier(),
                $executingUser
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function deleteGroupMembershipByGroupAndUser(Group $group, User $user, ?User $executingUser = null): void
    {
        try {
            $groupMembership =
                $this->groupMembershipEntityRepository->findGroupMembershipByGroupIdentifierAndUserIdentifier(
                    $group->getIdentifier(), $user->getIdentifier()
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function deleteGroupMembershipsByGroup(Group $group, ?User $executingUser = null): void
    {
        $groupMemberships =
            $this->groupMembershipEntityRepository->findGroupMembershipsByGroupIdentifier($group->getIdentifier());

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
        $groupMemberships = $this->retrieveGroupMembershipsByUserIdentifier($user->getIdentifier()->toString());

        foreach ($groupMemberships as $groupMembership) {
            $this->deleteGroupMembership($groupMembership, $executingUser);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipsByGroupIdentifier(Uuid $groupIdentifier): ArrayCollection
    {
        return $this->groupMembershipEntityRepository->findGroupMembershipsByGroupIdentifier($groupIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveGroupMembershipByGroupAndUser(Group $group, User $user): ?GroupMembership
    {
        return $this->retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
            $group->getIdentifier(), $user->getIdentifier()
        );
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveGroupMembershipByGroupIdentifierAndUserIdentifier(
        Uuid $groupIdentifier, Uuid $userIdentifier
    ): ?GroupMembership
    {
        return $this->groupMembershipEntityRepository->findGroupMembershipByGroupIdentifierAndUserIdentifier(
            $groupIdentifier, $userIdentifier
        );
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByIdentifier(Uuid $groupMembershipIdentifier): ?GroupMembership
    {
        return $this->groupMembershipEntityRepository->findGroupMembershipByIdentifier($groupMembershipIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveGroupMembershipsByUserIdentifier(Uuid $userIdentifier): ArrayCollection
    {
        return $this->groupMembershipEntityRepository->findGroupMembershipsByUserIdentifier($userIdentifier);
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
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     *
     * @return \Symfony\Component\Uid\Uuid[]
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveSubscribedUserIdentifiersByGroupIdentifiers(array $groupIdentifiers): array
    {
        return $this->groupMembershipEntityRepository->findGroupMembershipUserIdentifiersByGroupIdentifiers(
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function synchronizeGroup(Group $group, array $userIdentifiers, ?User $executingUser = null): void
    {
        $currentUserIdentifiers = $this->retrieveSubscribedUserIdentifiersByGroupIdentifier($group->getId());

        $newUserIdentifiers = array_diff($userIdentifiers, $currentUserIdentifiers);
        $oldUserIdentifiers = array_diff($currentUserIdentifiers, $userIdentifiers);

        $newUsers = $this->userService->findUsersByIdentifiers($newUserIdentifiers);

        foreach ($newUsers as $newUser) {
            $this->createGroupMembershipForGroupAndUser($group, $newUser, $executingUser);
        }

        $oldUsers = $this->userService->findUsersByIdentifiers($oldUserIdentifiers);

        foreach ($oldUsers as $oldUser) {
            $this->deleteGroupMembershipByGroupAndUser($group, $oldUser, $executingUser);
        }
    }
}