<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupCreateEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupDeleteEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupMoveEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUpdateEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\BeforeGroupDeleteEvent;
use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{
    public function __construct(
        protected GroupRepository $groupEntityRepository, protected GroupMembershipService $groupMembershipService,
        protected PropertyMapper $propertyMapper, protected EventDispatcherInterface $eventDispatcher,
        protected GroupsTreeTraverser $groupsTreeTraverser
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function countGroups(?ConditionInterface $condition = null): int
    {
        return $this->groupEntityRepository->countGroups($condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createGroup(Group $group, ?User $executingUser = null, bool $flush = true): void
    {
        $this->groupEntityRepository->saveGroup($group, $flush);
        $this->eventDispatcher->dispatch(new AfterGroupCreateEvent($group, $executingUser, $flush));
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createGroupFromParameters(
        string $name, Uuid $parentIdentifier, ?string $description = null, ?string $code = null,
        ?User $executingUser = null, bool $flush = true
    ): Group
    {
        $group = new Group();
        $group->setIdentifier(new UuidV7());
        $group->setName($name);
        $group->setDescription($description);
        $group->setCode($code);
        $group->setParent($this->groupEntityRepository->getGroupReference($parentIdentifier));

        $this->createGroup($group, $executingUser, $flush);

        return $group;
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\GroupMembership>
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createGroupMembershipForGroupIdentifierAndUserIdentifiers(
        Uuid $groupIdentifier, array $userIdentifiers, ?User $executingUser = null
    ): ArrayCollection
    {
        try {
            $group = $this->retrieveGroupByIdentifier($groupIdentifier);

            return $this->groupMembershipService->createGroupMembershipForGroupAndUserIdentifiers(
                $group, $userIdentifiers, $executingUser
            );
        }
        catch (NoSuchGroupException) {
            return new ArrayCollection();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroup(Group $group, ?User $executingUser = null, bool $flush = true): void
    {
        $this->eventDispatcher->dispatch(new BeforeGroupDeleteEvent($group, $executingUser));
        $descendants = $this->groupsTreeTraverser->retrieveDescendantsByGroup($group);

        foreach ($descendants as $descendant) {
            $this->deleteGroup($descendant, $executingUser, $flush);
        }

        $this->groupEntityRepository->removeGroup($group, $flush);
        $this->eventDispatcher->dispatch(new AfterGroupDeleteEvent($group, $executingUser, $flush));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function deleteGroupByIdentifier(Uuid $identifier, ?User $executingUser = null, bool $flush = true): void
    {
        $this->deleteGroup($this->retrieveGroupByIdentifier($identifier), $executingUser, $flush);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function deleteGroupMembershipsByGroupIdentifier(Uuid $groupIdentifier, ?User $executingUser = null): void
    {
        try {
            $group = $this->retrieveGroupByIdentifier($groupIdentifier);
            $this->groupMembershipService->deleteGroupMembershipsByGroup($group, $executingUser);
        }
        catch (NoSuchGroupException) {
        }
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     *
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function deleteGroupMembershipsByGroupIdentifiers(array $groupIdentifiers, ?User $executingUser = null): void
    {
        foreach ($groupIdentifiers as $groupIdentifier) {
            $this->deleteGroupMembershipsByGroupIdentifier($groupIdentifier, $executingUser);
        }
    }

    public function flushEntities(): void
    {
        $this->groupEntityRepository->flush();
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function getGroupReference(Uuid $groupIdentifier): Group
    {
        return $this->groupEntityRepository->getGroupReference($groupIdentifier);
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function moveGroup(
        Group $group, Uuid $parentGroupIdentifier, ?User $executingUser = null, bool $flush = true
    ): void
    {
        $oldParentGroupIdentifier = clone $group->getParent()->getIdentifier();

        $group->setParent($this->groupEntityRepository->getGroupReference($parentGroupIdentifier));
        $this->groupEntityRepository->saveGroup($group, $flush);

        $this->eventDispatcher->dispatch(
            new AfterGroupMoveEvent($group, $oldParentGroupIdentifier, $parentGroupIdentifier, $executingUser, $flush)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveDescendantsByParentIdentifier(?Uuid $parentIdentifier = null): ArrayCollection
    {
        try {
            if ($parentIdentifier !== DataClass::EMPTY_UUID) {
                $parentGroup = $this->retrieveGroupByIdentifier($parentIdentifier);
            }
            else {
                $parentGroup = $this->retrieveRootGroup();
            }

            return $this->groupsTreeTraverser->retrieveDescendantsByGroup($parentGroup);
        }
        catch (NoSuchGroupException) {
            return new ArrayCollection();
        }
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByCode(string $groupCode): Group
    {
        if (empty($groupCode)) {
            throw new InvalidArgumentException('The given groupcode can not be empty');
        }

        return $this->groupEntityRepository->findGroupByCode($groupCode);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByCodeAndParentIdentifier(string $groupCode, ?Uuid $parentIdentifier = null): Group
    {
        return $this->groupEntityRepository->findGroupByCodeAndParentIdentifier($groupCode, $parentIdentifier);
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByIdentifier(Uuid $groupIdentifier): Group
    {
        return $this->groupEntityRepository->findGroupByIdentifier($groupIdentifier);
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface $condition
     * @param ?int $offset
     * @param ?int $count
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveGroups(
        ?ConditionInterface $condition = null, ?int $offset = 0, ?int $count = - 1, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->groupEntityRepository->findGroups($condition, $count, $offset, $orderBy);
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupsAndDescendantsByGroupIdentifiers(array $groupIdentifiers = []): ArrayCollection
    {
        $groups = new ArrayCollection();

        foreach ($groupIdentifiers as $groupIdentifier) {
            try {
                $group = $this->retrieveGroupByIdentifier($groupIdentifier);

                $groups->add($group);

                $descendants = $this->groupsTreeTraverser->retrieveDescendantsByGroup($group);

                foreach ($descendants as $descendant) {
                    $groups->add($descendant);
                }
            }
            catch (NoSuchGroupException) {
            }
        }

        return $groups;
    }

    /**
     * @param \Symfony\Component\Uid\Uuid[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveGroupsByIdentifiers(array $groupIdentifiers): ArrayCollection
    {
        if (empty($groupIdentifiers)) {
            return new ArrayCollection([]);
        }

        return $this->groupEntityRepository->findGroupsByIdentifiers(
            $groupIdentifiers, new OrderBy(
                [new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))]
            )
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\Entity\Group>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieveGroupsBySearchQueryAndParentIdentifier(
        ?string $searchQuery = null, ?Uuid $parentIdentifier = null
    ): ArrayCollection
    {
        return $this->groupEntityRepository->findGroupsBySearchQueryAndParentIdentifier(
            $searchQuery, $parentIdentifier
        );
    }

    public function retrieveRootGroup(): Group
    {
        try {
            return $this->groupEntityRepository->findRootGroup();
        }
        catch (NoSuchGroupException) {
            throw new RuntimeException('Could not find the root group');
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function updateGroup(Group $group, ?User $executingUser = null, bool $flush = true): void
    {
        $this->groupEntityRepository->saveGroup($group, $flush);

        $this->eventDispatcher->dispatch(new AfterGroupUpdateEvent($group, $executingUser, $flush));
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function updateGroupFromParameters(
        Group $group, string $name, ?Uuid $parentIdentifier = null, ?string $description = null, ?string $code = null,
        ?User $executingUser = null, bool $flush = true
    ): Group
    {
        $group->setName($name);
        $group->setDescription($description);
        $group->setCode($code);
        $group->setParent($this->groupEntityRepository->getGroupReference($parentIdentifier));

        $this->updateGroup($group, $executingUser, $flush);

        if (!$group->getParent()->getIdentifier()->equals($parentIdentifier)) {
            $this->moveGroup($group, $parentIdentifier, $executingUser, $flush);
        }

        return $group;
    }
}