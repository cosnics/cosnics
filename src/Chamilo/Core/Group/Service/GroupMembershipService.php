<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupEmptyEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupSubscribeEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUnsubscribeEvent;
use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\Entity\User;
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
        protected readonly EventDispatcherInterface $eventDispatcher, protected readonly UserService $userService,
        protected readonly GroupsTreeTraverser $groupsTreeTraverser,
        protected readonly GroupMembershipRepository $groupMembershipRepository
    )
    {
    }

    public function countSubscribedUsersByGroupIdentifier(
        Uuid $groupIdentifier, ?ConditionInterface $condition = null
    ): int
    {
        return $this->groupMembershipRepository->countGroupMembershipsByGroupIdentifier(
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
        return $this->groupMembershipRepository->countGroupMembershipsByGroupIdentifiers(
            $groupIdentifiers, $condition
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersByGroup(Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
    ): int
    {
        $cacheKey = md5(serialize([$group->getIdentifier(), $includeSubGroups, $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->groupUsersCount)) {
            if ($includeSubGroups) {
                $groupIdentifiers =
                    $this->groupsTreeTraverser->retrieveDescendantIdentifiersByGroup($group, $recursiveSubgroups);
            }
            else {
                $groupIdentifiers = [];
            }

            $groupIdentifiers[] = $group->getIdentifier();

            $this->groupUsersCount[$cacheKey] = $this->countSubscribedUsersByGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUsersCount[$cacheKey];
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function createGroupMembershipForGroupAndUser(
        Group $group, User $user, ?User $executingUser = null, bool $flush = true
    ): GroupMembership
    {
        try {
            $groupMembership = new GroupMembership();

            $groupMembership->setIdentifier(new UuidV7());
            $groupMembership->setUser($user);
            $groupMembership->setGroup($group);

            $this->groupMembershipRepository->saveGroupMembership($groupMembership, $flush);

            $this->eventDispatcher->dispatch(
                new AfterGroupSubscribeEvent($group->getIdentifier(), $user->getIdentifier(), $executingUser, $flush)
            );
        }
        catch (EntityAlreadyExistsException) {
            $groupMembership = $this->retrieveGroupMembershipByGroupAndUser($group, $user);
        }

        return $groupMembership;
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createGroupMembershipForGroupAndUserIdentifiers(
        Group $group, array $userIdentifiers, ?User $executingUser = null
    ): ArrayCollection
    {
        $groupMemberships = new ArrayCollection();

        foreach ($userIdentifiers as $userIdentifier) {
            try {
                $user = $this->userService->findUserByIdentifier($userIdentifier);
                $groupMemberships->add($this->createGroupMembershipForGroupAndUser($group, $user, $executingUser));
            }
            catch (NoSuchUserException) {
            }
        }

        return $groupMemberships;
    }

    public function deleteGroupMembership(
        GroupMembership $groupMembership, ?User $executingUser = null, bool $flush = true
    ): void
    {
        $this->groupMembershipRepository->removeGroupMembership($groupMembership, $flush);

        $this->eventDispatcher->dispatch(
            new AfterGroupUnsubscribeEvent(
                $groupMembership->getGroup()->getIdentifier(), $groupMembership->getUser()->getIdentifier(),
                $executingUser, $flush
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function deleteGroupMembershipByGroupAndUser(Group $group, User $user, ?User $executingUser = null): void
    {
        try {
            $groupMembership = $this->groupMembershipRepository->findGroupMembershipByGroupIdentifierAndUserIdentifier(
                $group->getIdentifier(), $user->getIdentifier()
            );

            $this->deleteGroupMembership($groupMembership, $executingUser);
        }
        catch (NoSuchGroupMembershipException) {
        }
    }

    public function deleteGroupMembershipByIdentifier(Uuid $groupMembershipIdentifier, ?User $executingUser = null
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
    public function deleteGroupMembershipsByGroup(Group $group, ?User $executingUser = null, bool $flush = true): void
    {
        $groupMemberships =
            $this->groupMembershipRepository->findGroupMembershipsByGroupIdentifier($group->getIdentifier());

        foreach ($groupMemberships as $groupMembership) {
            $this->deleteGroupMembership($groupMembership, $executingUser, $flush);
        }

        $this->eventDispatcher->dispatch(new AfterGroupEmptyEvent($group, $executingUser, $flush));
    }

    public function deleteGroupMembershipsByIdentifiers(array $groupMembershipIdentifiers, ?User $executingUser = null
    ): void
    {
        foreach ($groupMembershipIdentifiers as $groupMembershipIdentifier) {
            $this->deleteGroupMembershipByIdentifier($groupMembershipIdentifier, $executingUser);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function deleteGroupMembershipsByUser(User $user, ?User $executingUser = null): void
    {
        $groupMemberships = $this->retrieveGroupMembershipsByUserIdentifier($user->getIdentifier());

        foreach ($groupMemberships as $groupMembership) {
            $this->deleteGroupMembership($groupMembership, $executingUser);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findGroupMembershipsByGroupIdentifier(Uuid $groupIdentifier): ArrayCollection
    {
        return $this->groupMembershipRepository->findGroupMembershipsByGroupIdentifier($groupIdentifier);
    }

    public function flushEntities(): void
    {
        $this->groupMembershipRepository->flush();
    }

    /**
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
        return $this->groupMembershipRepository->findGroupMembershipByGroupIdentifierAndUserIdentifier(
            $groupIdentifier, $userIdentifier
        );
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     */
    public function retrieveGroupMembershipByIdentifier(Uuid $groupMembershipIdentifier): ?GroupMembership
    {
        return $this->groupMembershipRepository->findGroupMembershipByIdentifier($groupMembershipIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveGroupMembershipsByUserIdentifier(Uuid $userIdentifier): ArrayCollection
    {
        return $this->groupMembershipRepository->findGroupMembershipsByUserIdentifier($userIdentifier);
    }

    /**
     * @return \Symfony\Component\Uid\Uuid[]
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveSubscribedUserIdentifiersByGroupIdentifier(Uuid $groupIdentifier): array
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
        return $this->groupMembershipRepository->findGroupMembershipUserIdentifiersByGroupIdentifiers(
            $groupIdentifiers
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveSubscribedUsersByGroupIdentifier(
        Uuid $groupIdentifier, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->retrieveSubscribedUsersByGroupIdentifiers([$groupIdentifier], $condition, $offset, $count,
            $orderBy);
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveSubscribedUsersByGroupIdentifiers(
        array $groupIdentifiers, ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->groupMembershipRepository->findGroupMembershipsByGroupIdentifiers(
            $groupIdentifiers, $condition, $offset, $count, $orderBy
        );
    }

    /**
     * @return \Symfony\Component\Uid\Uuid[]
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveUserIdentifiersByGroup(
        Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
    ): array
    {
        $cacheKey = md5(serialize([$group->getIdentifier(), $includeSubGroups, $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->groupUserIdentifiers)) {
            if ($includeSubGroups) {
                $groupIdentifiers =
                    $this->groupsTreeTraverser->retrieveDescendantIdentifiersByGroup($group, $recursiveSubgroups);
            }
            else {
                $groupIdentifiers = [];
            }

            $groupIdentifiers[] = $group->getIdentifier();

            $this->groupUserIdentifiers[$cacheKey] =
                $this->retrieveSubscribedUserIdentifiersByGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUserIdentifiers[$cacheKey];
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function synchronizeGroup(Group $group, array $userIdentifiers, ?User $executingUser = null): void
    {
        $currentUserIdentifiers = $this->retrieveSubscribedUserIdentifiersByGroupIdentifier($group->getIdentifier());

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