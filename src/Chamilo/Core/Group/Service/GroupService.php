<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupCreateEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupDeleteEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupMoveEvent;
use Chamilo\Core\Group\Architecture\EventDispatcher\Event\AfterGroupUpdateEvent;
use Chamilo\Core\Group\Architecture\Exception\GroupNotFoundException;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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
     * @throws \Throwable
     */
    public function deleteGroup(Group $group, ?User $executingUser = null): bool
    {
        $subGroupIds = [];
        $impactedUserIds = $this->groupMembershipService->findUserIdentifiersForGroup($group, true, true);

        $deletedGroups = $this->groupRepository->deleteGroup($group);

        foreach ($deletedGroups as $deletedGroup) {
            $subGroupIds[] = $deletedGroup->getId();
        }

        if (!$this->groupMembershipService->removeUsersFromGroupsByIdsAfterRemoval($subGroupIds)) {
            return false;
        }

        $this->eventDispatcher->dispatch(
            new AfterGroupDeleteEvent($group, $subGroupIds, $impactedUserIds, $executingUser)
        );

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findGroupByCode(string $groupCode): Group
    {
        if (empty($groupCode)) {
            throw new InvalidArgumentException('The given groupcode can not be empty');
        }

        $group = $this->groupRepository->findGroupByCode($groupCode);

        if (!$group instanceof Group) {
            throw new RuntimeException('Could not find the group with groupcode ' . $groupCode);
        }

        return $group;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findGroupByCodeAndParentIdentifier(string $groupCode, string $parentIdentifier): Group
    {
        if (empty($groupCode)) {
            throw new InvalidArgumentException('The given $groupCode can not be empty for group code ' . $groupCode);
        }

        if (empty($parentIdentifier)) {
            throw new InvalidArgumentException(
                'The given $parentIdentifier can not be empty for group code ' . $groupCode
            );
        }

        try {
            return $this->groupRepository->findGroupByCodeAndParentIdentifier($groupCode, $parentIdentifier);
        }
        catch (StorageNoResultException) {
            throw new GroupNotFoundException(code: $groupCode, parentIdentifier: $parentIdentifier);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findGroupByIdentifier(string $groupIdentifier): Group
    {
        $group = $this->groupRepository->findGroupByIdentifier($groupIdentifier);

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
    public function findGroups(
        ?ConditionInterface $condition = null, ?int $offset = 0, ?int $count = - 1, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->groupRepository->findGroups($condition, $count, $offset, $orderBy);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findGroupsAndSubgroupsForGroupIdentifiers(array $groupIdentifiers = []): ArrayCollection
    {
        $groups = new ArrayCollection();

        foreach ($groupIdentifiers as $groupIdentifier) {
            $group = $this->findGroupByIdentifier($groupIdentifier);

            $groups->add($group);

            $subgroups = $this->groupsTreeTraverser->findSubGroupsForGroup($group);

            foreach ($subgroups as $subgroup) {
                $groups->add($subgroup);
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
    public function findGroupsByIdentifiers(array $groupIdentifiers): ArrayCollection
    {
        if (empty($groupIdentifiers)) {
            return new ArrayCollection([]);
        }

        return $this->groupRepository->findGroupsByIdentifiersOrderedByName($groupIdentifiers);
    }

    /**
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findGroupsForParentIdentifier(string $parentIdentifier = DataClass::EMPTY_UUID): ArrayCollection
    {
        return $this->groupRepository->findGroupsForParentIdentifier($parentIdentifier);
    }

    /**
     * @param ?string $searchQuery
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findGroupsForSearchQueryAndParentIdentifier(
        ?string $searchQuery = null, string $parentIdentifier = DataClass::EMPTY_UUID
    ): ArrayCollection
    {
        return $this->groupRepository->findGroupsForSearchQueryAndParentIdentifier(
            $searchQuery, $parentIdentifier
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findRootGroup(): Group
    {
        $group = $this->groupRepository->findRootGroup();

        if (!$group instanceof Group) {
            throw new RuntimeException('Could not find the root group');
        }

        return $group;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function moveGroup(Group $group, string $parentGroupIdentifier, ?User $executingUser = null): bool
    {
        $oldParentGroup = $this->findGroupByIdentifier($group->getParentId());
        $newParentGroup = $this->findGroupByIdentifier($parentGroupIdentifier);

        if (!$this->groupRepository->moveGroup($group, $parentGroupIdentifier)) {
            return false;
        }

        $this->eventDispatcher->dispatch(
            new AfterGroupMoveEvent($group, $oldParentGroup, $newParentGroup, $executingUser)
        );

        return true;
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ?\Chamilo\Core\User\Storage\DataClass\User $executingUser
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function subscribeUserToGroupByCode(string $groupCode, User $user, ?User $executingUser = null): GroupRelUser
    {
        return $this->groupMembershipService->subscribeUserToGroup(
            $this->findGroupByCode($groupCode), $user, $executingUser
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function truncateGroup(Group $group, ?User $executingUser = null): bool
    {
        return $this->groupMembershipService->unsubscribeAllUsersFromGroup($group, $executingUser);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateGroup(Group $group, ?User $executingUser = null): bool
    {
        if (!$this->groupRepository->updateGroup($group)) {
            return false;
        }

        $this->eventDispatcher->dispatch(new AfterGroupUpdateEvent($group, $executingUser));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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