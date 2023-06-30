<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service to manage the groups of Chamilo
 *
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{
    protected GroupEventNotifier $groupEventNotifier;

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
        PropertyMapper $propertyMapper, GroupEventNotifier $groupEventNotifier, GroupsTreeTraverser $groupsTreeTraverser
    )
    {
        $this->groupRepository = $groupRepository;
        $this->groupMembershipService = $groupMembershipService;
        $this->propertyMapper = $propertyMapper;
        $this->groupEventNotifier = $groupEventNotifier;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    public function countGroups(?Condition $condition = null): int
    {
        return $this->getGroupRepository()->countGroups($condition);
    }

    public function createGroup(Group $group): bool
    {
        if (!$this->getGroupRepository()->createGroup($group))
        {
            return false;
        }

        return $this->groupEventNotifier->afterCreate($group);
    }

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

        $this->groupEventNotifier->afterDelete($group, $subGroupIds, $impactedUserIds);

        return true;
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated (use tree traverser)
     */
    public function findAllSubscribedGroupIdentifiersForUserIdentifier(string $userIdentifier): array
    {
        return $this->groupsTreeTraverser->findAllSubscribedGroupIdentifiersForUserIdentifier($userIdentifier);
    }

    /**
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findAllSubscribedGroupsForUserIdentifier(string $userIdentifier): ArrayCollection
    {
        return $this->groupsTreeTraverser->findAllSubscribedGroupsForUserIdentifier($userIdentifier);
    }

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

        $group = $this->groupRepository->findGroupByCodeAndParentIdentifier($groupCode, $parentIdentifier);

        if (!$group instanceof Group)
        {
            throw new RuntimeException(
                'Could not find the group with groupcode ' . $groupCode . ' and parent identifier ' . $parentIdentifier
            );
        }

        return $group;
    }

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
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findGroups(
        ?Condition $condition = null, ?int $offset = 0, ?int $count = - 1, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getGroupRepository()->findGroups($condition, $count, $offset, $orderBy);
    }

    /**
     * @param string[] $groupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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
     * @param ?string $searchQuery
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findGroupsForSearchQueryAndParentIdentifier(
        ?string $searchQuery = null, string $parentIdentifier = '0'
    ): ArrayCollection
    {
        return $this->getGroupRepository()->findGroupsForSearchQueryAndParentIdentifier(
            $searchQuery, $parentIdentifier
        );
    }

    public function findRootGroup(): Group
    {
        $group = $this->groupRepository->findRootGroup();

        if (!$group instanceof Group)
        {
            throw new RuntimeException('Could not find the root group');
        }

        return $group;
    }

    /**
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated (use tree traverser)
     */
    public function getHighestGroupQuotumForUser(User $user): int
    {
        return $this->groupsTreeTraverser->getHighestGroupQuotumForUser($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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

    public function moveGroup(Group $group, string $parentGroupIdentifier): bool
    {
        $oldParentGroup = $this->findGroupByIdentifier($group->getParentId());
        $newParentGroup = $this->findGroupByIdentifier($parentGroupIdentifier);

        if (!$this->getGroupRepository()->moveGroup($group, $parentGroupIdentifier))
        {
            return false;
        }

        return $this->groupEventNotifier->afterMove($group, $oldParentGroup, $newParentGroup);
    }

    public function subscribeUserToGroupByCode(string $groupCode, User $user): GroupRelUser
    {
        $group = $this->findGroupByCode($groupCode);

        return $this->getGroupMembershipService()->subscribeUserToGroup($group, $user);
    }

    public function updateGroup(Group $group): bool
    {
        if (!$this->getGroupRepository()->updateGroup($group))
        {
            return false;
        }

        return $this->groupEventNotifier->afterUpdate($group);
    }
}