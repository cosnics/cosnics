<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupCreateEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupDeleteEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupMoveEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupUpdateEvent;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Exception\NoSuchGroupException;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service to manage the groups of Chamilo
 *
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{
    protected EventDispatcherInterface $eventDispatcher;

    protected GroupMembershipService $groupMembershipService;

    protected GroupRepository $groupRepository;

    /**
     * @var string[]
     */
    protected array $groupUserIdentifiers = [];

    /**
     * @var int[]
     */
    protected array $groupUsersCount = [];

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected PropertyMapper $propertyMapper;

    protected array $subGroupIdentifiers = [];

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected array $subGroups = [];

    /**
     * @var int[]
     */
    protected array $subGroupsCount = [];

    /**
     * @var string[]
     */
    protected array $userSubscribedGroupIdentifiers = [];

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected array $userSubscribedGroups = [];

    public function __construct(
        GroupRepository $groupRepository, GroupMembershipService $groupMembershipService,
        PropertyMapper $propertyMapper, EventDispatcherInterface $eventDispatcher,
        GroupsTreeTraverser $groupsTreeTraverser
    )
    {
        $this->groupRepository = $groupRepository;
        $this->groupMembershipService = $groupMembershipService;
        $this->propertyMapper = $propertyMapper;
        $this->eventDispatcher = $eventDispatcher;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function countGroups(?Condition $condition = null): int
    {
        return $this->getGroupRepository()->countGroups($condition);
    }

    /**
     * @throws \Throwable
     */
    public function createGroup(Group $group): bool
    {
        if (!$this->getGroupRepository()->createGroup($group))
        {
            return false;
        }

        $this->getEventDispatcher()->dispatch(new AfterGroupCreateEvent($group));

        return true;
    }

    /**
     * @throws \Throwable
     */
    public function deleteGroup(Group $group): bool
    {
        $subGroupIds = [];
        $impactedUserIds = $this->groupsTreeTraverser->findUserIdentifiersForGroup($group, true, true);

        $deletedGroups = $this->getGroupRepository()->deleteGroup($group);

        foreach ($deletedGroups as $deletedGroup)
        {
            $subGroupIds[] = $deletedGroup->getId();
        }

        if (!$this->getGroupMembershipService()->removeUsersFromGroupsByIdsAfterRemoval($subGroupIds))
        {
            return false;
        }

        $this->getEventDispatcher()->dispatch(new AfterGroupDeleteEvent($group, $subGroupIds, $impactedUserIds));

        return true;
    }

    /**
     * @return string[]
     * @deprecated Use GroupsTreeTraverser::findAllSubscribedGroupIdentifiersForUserIdentifier(string $userIdentifier)
     */
    public function findAllSubscribedGroupIdentifiersForUserIdentifier(string $userIdentifier): array
    {
        return $this->groupsTreeTraverser->findAllSubscribedGroupIdentifiersForUserIdentifier($userIdentifier);
    }

    /**
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @deprecated Use GroupsTreeTraverser::findAllSubscribedGroupsForUserIdentifier(string $userIdentifier)
     */
    public function findAllSubscribedGroupsForUserIdentifier(string $userIdentifier): ArrayCollection
    {
        return $this->groupsTreeTraverser->findAllSubscribedGroupsForUserIdentifier($userIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupByCode(string $groupCode): Group
    {
        if (empty($groupCode))
        {
            throw new InvalidArgumentException('The given groupcode can not be empty');
        }

        $group = $this->groupRepository->findGroupByCode($groupCode);

        if (!$group instanceof Group)
        {
            throw new RuntimeException('Could not find the group with groupcode ' . $groupCode);
        }

        return $group;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupByCodeAndParentIdentifier(string $groupCode, string $parentIdentifier): Group
    {
        if (empty($groupCode))
        {
            throw new InvalidArgumentException('The given $groupCode can not be empty for group code ' . $groupCode);
        }

        if (empty($parentIdentifier))
        {
            throw new InvalidArgumentException(
                'The given $parentIdentifier can not be empty for group code ' . $groupCode
            );
        }

        try
        {
            return $this->groupRepository->findGroupByCodeAndParentIdentifier($groupCode, $parentIdentifier);
        }
        catch (StorageNoResultException)
        {
            throw new NoSuchGroupException(code: $groupCode, parentIdentifier: $parentIdentifier);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupByIdentifier(string $groupIdentifier): Group
    {
        $group = $this->groupRepository->findGroupByIdentifier($groupIdentifier);

        if (!$group instanceof Group)
        {
            throw new RuntimeException('Could not find the group with identifier ' . $groupIdentifier);
        }

        return $group;
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroups(
        ?Condition $condition = null, ?int $offset = 0, ?int $count = - 1, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->getGroupRepository()->findGroups($condition, $count, $offset, $orderBy);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     */
    public function findGroupsAndSubgroupsForGroupIdentifiers(array $groupIdentifiers = []): ArrayCollection
    {
        $groups = new ArrayCollection();

        foreach ($groupIdentifiers as $groupIdentifier)
        {
            $group = $this->findGroupByIdentifier($groupIdentifier);

            $groups->add($group);

            $subgroups = $this->groupsTreeTraverser->findSubGroupsForGroup($group);

            foreach ($subgroups as $subgroup)
            {
                $groups->add($subgroup);
            }
        }

        return $groups;
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupsByIdentifiers(array $groupIdentifiers): ArrayCollection
    {
        if (empty($groupIdentifiers))
        {
            return new ArrayCollection([]);
        }

        return $this->groupRepository->findGroupsByIdentifiersOrderedByName($groupIdentifiers);
    }

    /**
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupsForParentIdentifier(string $parentIdentifier = '0'): ArrayCollection
    {
        return $this->getGroupRepository()->findGroupsForParentIdentifier($parentIdentifier);
    }

    /**
     * @param ?string $searchQuery
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findGroupsForSearchQueryAndParentIdentifier(
        ?string $searchQuery = null, string $parentIdentifier = '0'
    ): ArrayCollection
    {
        return $this->getGroupRepository()->findGroupsForSearchQueryAndParentIdentifier(
            $searchQuery, $parentIdentifier
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function findRootGroup(): Group
    {
        $group = $this->groupRepository->findRootGroup();

        if (!$group instanceof Group)
        {
            throw new RuntimeException('Could not find the root group');
        }

        return $group;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @deprecated Use GroupService::findGroupByIdentifier() now
     */
    public function getGroupByIdentifier(string $groupIdentifier): ?Group
    {
        return $this->findGroupByIdentifier($groupIdentifier);
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return string
     * @deprecated (use tree traverser)
     */
    public function getGroupPath(Group $group): string
    {
        return $this->groupsTreeTraverser->getFullyQualifiedNameForGroup($group);
    }

    public function getGroupRepository(): GroupRepository
    {
        return $this->groupRepository;
    }

    /**
     * @deprecated (use tree traverser)
     */
    public function getHighestGroupQuotumForUser(User $user): int
    {
        return $this->groupsTreeTraverser->getHighestGroupQuotumForUser($user);
    }

    /**
     * @deprecated (use tree traverser)
     */
    public function getLowestGroupQuotumForUser(User $user): int
    {
        return $this->groupsTreeTraverser->getLowestGroupQuotumForUser($user);
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    /**
     * @throws \Throwable
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function moveGroup(Group $group, string $parentGroupIdentifier): bool
    {
        $oldParentGroup = $this->findGroupByIdentifier($group->getParentId());
        $newParentGroup = $this->findGroupByIdentifier($parentGroupIdentifier);

        if (!$this->getGroupRepository()->moveGroup($group, $parentGroupIdentifier))
        {
            return false;
        }

        $this->getEventDispatcher()->dispatch(new AfterGroupMoveEvent($group, $oldParentGroup, $newParentGroup));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function subscribeUserToGroupByCode(string $groupCode, User $user): GroupRelUser
    {
        return $this->getGroupMembershipService()->subscribeUserToGroup($this->findGroupByCode($groupCode), $user);
    }

    public function truncateGroup(Group $group): bool
    {
        return $this->getGroupMembershipService()->unsubscribeAllUsersFromGroup($group);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function updateGroup(Group $group): bool
    {
        if (!$this->getGroupRepository()->updateGroup($group))
        {
            return false;
        }

        $this->getEventDispatcher()->dispatch(new AfterGroupUpdateEvent($group));

        return true;
    }
}