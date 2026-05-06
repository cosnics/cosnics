<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupCreateEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupDeleteEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupMoveEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUpdateEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\BeforeGroupDeleteEvent;
use Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{
    public function __construct(
        protected GroupRepository $groupRepository, protected GroupMembershipService $groupMembershipService,
        protected PropertyMapper $propertyMapper, protected EventDispatcherInterface $eventDispatcher,
        protected GroupsTreeTraverser $groupsTreeTraverser
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countGroups(?ConditionInterface $condition = null): int
    {
        return $this->groupRepository->countGroups($condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function createGroup(Group $group, ?User $executingUser = null): void
    {
        $this->groupRepository->createGroup($group);
        $this->eventDispatcher->dispatch(new AfterGroupCreateEvent($group, $executingUser));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function createGroupFromParameters(
        string $name, string $parentIdentifier, ?string $description = null, ?string $code = null,
        ?User $executingUser = null
    ): Group
    {
        $group = new Group();
        $group->setName($name);
        $group->setDescription($description);
        $group->setCode($code);
        $group->setParentId($parentIdentifier);

        $this->createGroup($group, $executingUser);

        return $group;
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\GroupMembership>
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupMembershipException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createGroupMembershipForGroupIdentifierAndUserIdentifiers(
        string $groupIdentifier, array $userIdentifiers, ?User $executingUser = null
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
    public function deleteGroup(Group $group, ?User $executingUser = null): void
    {
        $this->eventDispatcher->dispatch(new BeforeGroupDeleteEvent($group, $executingUser));
        $descendants = $this->groupsTreeTraverser->retrieveDescendantsByGroup($group);

        foreach ($descendants as $descendant) {
            $this->deleteGroup($descendant, $executingUser);
        }

        $this->groupRepository->deleteGroup($group);
        $this->eventDispatcher->dispatch(new AfterGroupDeleteEvent($group, $executingUser));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Throwable
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function deleteGroupByIdentifier(string $identifier, ?User $executingUser = null): void
    {
        $this->deleteGroup($this->retrieveGroupByIdentifier($identifier), $executingUser);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByGroupIdentifier(string $groupIdentifier, ?User $executingUser = null): void
    {
        try {
            $group = $this->retrieveGroupByIdentifier($groupIdentifier);
            $this->groupMembershipService->deleteGroupMembershipsByGroup($group, $executingUser);
        }
        catch (NoSuchGroupException) {
        }
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteGroupMembershipsByGroupIdentifiers(array $groupIdentifiers, ?User $executingUser = null): void
    {
        foreach ($groupIdentifiers as $groupIdentifier) {
            $this->deleteGroupMembershipsByGroupIdentifier($groupIdentifier, $executingUser);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function moveGroup(Group $group, string $parentGroupIdentifier, ?User $executingUser = null): void
    {
        $oldParentGroupIdentifier = $group->getParentId();

        $this->groupRepository->moveGroup($group, $parentGroupIdentifier);

        $this->eventDispatcher->dispatch(
            new AfterGroupMoveEvent($group, $oldParentGroupIdentifier, $parentGroupIdentifier, $executingUser)
        );
    }

    /**
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveDescendantsByParentIdentifier(string $parentIdentifier = DataClass::EMPTY_UUID
    ): ArrayCollection
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByCode(string $groupCode): Group
    {
        if (empty($groupCode)) {
            throw new InvalidArgumentException('The given groupcode can not be empty');
        }

        return $this->groupRepository->retrieveGroupByCode($groupCode);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByCodeAndParentIdentifier(string $groupCode, string $parentIdentifier): Group
    {
        if (empty($groupCode)) {
            throw new InvalidArgumentException('The given $groupCode can not be empty for group code ' . $groupCode);
        }

        if (empty($parentIdentifier)) {
            throw new InvalidArgumentException(
                'The given $parentIdentifier can not be empty for group code ' . $groupCode
            );
        }

        return $this->groupRepository->retrieveGroupByCodeAndParentIdentifier($groupCode, $parentIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function retrieveGroupByIdentifier(string $groupIdentifier): Group
    {
        $group = $this->groupRepository->retrieveGroupByIdentifier($groupIdentifier);

        if (!$group instanceof Group) {
            throw new RuntimeException('Could not find the group with identifier ' . $groupIdentifier);
        }

        return $group;
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface $condition
     * @param ?int $offset
     * @param ?int $count
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroups(
        ?ConditionInterface $condition = null, ?int $offset = 0, ?int $count = - 1, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->groupRepository->retrieveGroups($condition, $count, $offset, $orderBy);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupsByIdentifiers(array $groupIdentifiers): ArrayCollection
    {
        if (empty($groupIdentifiers)) {
            return new ArrayCollection([]);
        }

        return $this->groupRepository->retrieveGroupsByIdentifiersOrderedByName($groupIdentifiers);
    }

    /**
     * @param ?string $searchQuery
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroupsBySearchQueryAndParentIdentifier(
        ?string $searchQuery = null, string $parentIdentifier = DataClass::EMPTY_UUID
    ): ArrayCollection
    {
        return $this->groupRepository->retrieveGroupsBySearchQueryAndParentIdentifier(
            $searchQuery, $parentIdentifier
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveRootGroup(): Group
    {
        try {
            return $this->groupRepository->retrieveRootGroup();
        }
        catch (NoSuchGroupException) {
            throw new RuntimeException('Could not find the root group');
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateGroup(Group $group, ?User $executingUser = null): void
    {
        $this->groupRepository->updateGroup($group);

        $this->eventDispatcher->dispatch(new AfterGroupUpdateEvent($group, $executingUser));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    public function updateGroupFromParameters(
        Group $group, string $name, string $parentIdentifier, ?string $description = null, ?string $code = null,
        ?User $executingUser = null
    ): Group
    {
        $group->setName($name);
        $group->setDescription($description);
        $group->setCode($code);
        $group->setParentId($parentIdentifier);

        $this->updateGroup($group, $executingUser);

        if ($group->getParentId() != $parentIdentifier) {
            $this->moveGroup($group, $parentIdentifier, $executingUser);
        }

        return $group;
    }
}