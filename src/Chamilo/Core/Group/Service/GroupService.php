<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Iterator\DataClassCollection;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service to manage the groups of Chamilo
 *
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{
    /**
     * @var \Chamilo\Core\Group\Storage\Repository\GroupRepository
     */
    protected $groupRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupMembershipService
     */
    protected $groupMembershipService;

    /**
     * @var \Chamilo\Core\Group\Service\GroupsTreeTraverser
     */
    protected $groupsTreeTraverser;

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected $userSubscribedGroups = [];

    /**
     * @var integer[][]
     */
    protected $userSubscribedGroupIdentifiers = [];

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    protected $propertyMapper;

    /**
     * @var integer[][]
     */
    protected $groupUserIdentifiers = [];

    /**
     * @var integer[][]
     */
    protected $subGroupIdentifiers = [];

    /**
     * @var integer[]
     */
    protected $subGroupsCount = [];

    /**
     * @var integer[]
     */
    protected $groupUsersCount = [];

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected $subGroups = [];

    /**
     * @var \Chamilo\Core\Group\Service\GroupEventNotifier
     */
    protected $groupEventNotifier;

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository
     * @param \Chamilo\Core\Group\Service\GroupMembershipService $groupMembershipService
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     * @param \Chamilo\Core\Group\Service\GroupEventNotifier $groupEventNotifier
     */
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

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countGroups(Condition $condition = null)
    {
        return $this->getGroupRepository()->countGroups($condition);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     * @throws \Exception
     */
    public function createGroup(Group $group)
    {
        $success = $this->getGroupRepository()->createGroup($group);
        if ($success)
        {
            $this->groupEventNotifier->afterCreate($group);
        }

        return $success;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     * @throws \Exception
     */
    public function deleteGroup(Group $group)
    {
        $subGroupIds = [];
        $impactedUserIds = $this->groupsTreeTraverser->findUserIdentifiersForGroup($group, true, true);

        $deletedGroups = $this->getGroupRepository()->deleteGroup($group);

        if (!$deletedGroups instanceof DataClassCollection)
        {
            return false;
        }

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
     * @param integer $userIdentifier
     *
     * @return integer[]
     * @throws \Exception
     *
     * @deprecated (use tree traverser)
     */
    public function findAllSubscribedGroupIdentifiersForUserIdentifier(int $userIdentifier)
    {
        return $this->groupsTreeTraverser->findAllSubscribedGroupIdentifiersForUserIdentifier($userIdentifier);
    }

    /**
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|DataClassCollection
     *
     * @throws \Exception
     */
    public function findAllSubscribedGroupsForUserIdentifier(int $userIdentifier)
    {
        return $this->groupsTreeTraverser->findAllSubscribedGroupsForUserIdentifier($userIdentifier);
    }

    /**
     * @param string $groupCode
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function findGroupByCode($groupCode)
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
     * @param string $groupCode
     * @param integer $parentIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function findGroupByCodeAndParentIdentifier($groupCode, $parentIdentifier)
    {
        if (empty($groupCode))
        {
            throw new InvalidArgumentException('The given $groupCode can not be empty for group code ' . $groupCode);
        }

        if (empty($parentIdentifier))
        {
            throw new InvalidArgumentException('The given $parentIdentifier can not be empty for group code ' . $groupCode);
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

    /**
     * @param integer $groupIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function findGroupByIdentifier($groupIdentifier)
    {
        $group = $this->groupRepository->findGroupByIdentifier($groupIdentifier);

        if (!$group instanceof Group)
        {
            throw new RuntimeException('Could not find the group with identifier ' . $groupIdentifier);
        }

        return $group;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|DataClassCollection
     */
    public function findGroups($condition, $offset = 0, $count = - 1, ?OrderBy $orderBy = null)
    {
        return $this->getGroupRepository()->findGroups($condition, $count, $offset, $orderBy);
    }

    /**
     *
     * @param integer[] $groupIdentifiers
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|DataClassCollection
     */
    public function findGroupsByIdentifiers($groupIdentifiers)
    {
        if (empty($groupIdentifiers))
        {
            return new DataClassCollection(Group::class, []);
        }

        return $this->groupRepository->findGroupsByIdentifiersOrderedByName($groupIdentifiers);
    }

    /**
     * @param string $searchQuery
     * @param integer $parentIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|DataClassCollection
     */
    public function findGroupsForSearchQueryAndParentIdentifier(string $searchQuery = null, int $parentIdentifier = 0)
    {
        return $this->getGroupRepository()->findGroupsForSearchQueryAndParentIdentifier(
            $searchQuery, $parentIdentifier
        );
    }

    /**
     * @param integer $groupIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     * @deprecated Use GroupService::findGroupByIdentifier() now
     */
    public function getGroupByIdentifier($groupIdentifier)
    {
        return $this->findGroupByIdentifier($groupIdentifier);
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupMembershipService
     */
    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupMembershipService $groupMembershipService
     */
    public function setGroupMembershipService(GroupMembershipService $groupMembershipService): void
    {
        $this->groupMembershipService = $groupMembershipService;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return string
     * @todo This should be rewritten when implementing the new NestedSetDataClassRepository for Group objects
     *
     * @deprecated (use tree traverser)
     */
    public function getGroupPath(Group $group)
    {
        return $this->groupsTreeTraverser->getGroupPath($group);
    }

    /**
     * @return \Chamilo\Core\Group\Storage\Repository\GroupRepository
     */
    public function getGroupRepository(): GroupRepository
    {
        return $this->groupRepository;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository
     */
    public function setGroupRepository(GroupRepository $groupRepository): void
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     *
     * @deprecated (use tree traverser)
     */
    public function getHighestGroupQuotumForUser(User $user)
    {
        return $this->groupsTreeTraverser->getHighestGroupQuotumForUser($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     *
     * @deprecated (use tree traverser)
     */
    public function getLowestGroupQuotumForUser(User $user)
    {
        return $this->groupsTreeTraverser->getLowestGroupQuotumForUser($user);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     */
    public function setPropertyMapper(PropertyMapper $propertyMapper): void
    {
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param integer $parentGroupIdentifier
     *
     * @return boolean
     * @throws \Exception
     */
    public function moveGroup(Group $group, int $parentGroupIdentifier)
    {
        $oldParentGroup = $this->findGroupByIdentifier($group->getParentId());
        $newParentGroup = $this->findGroupByIdentifier($parentGroupIdentifier);

        if (!$oldParentGroup instanceof Group || !$newParentGroup instanceof Group)
        {
            throw new InvalidArgumentException(
                sprintf(
                    'The old parent (%s) or the new parent (%s) of the group (%s) are not referencing a valid group so the group can not be moved',
                    $group->getParentId(), $parentGroupIdentifier, $group->getId()
                )
            );
        }

        $success = $this->getGroupRepository()->moveGroup($group, $parentGroupIdentifier);
        if (!$success)
        {
            return false;
        }

        $this->groupEventNotifier->afterMove($group, $oldParentGroup, $newParentGroup);

        return true;
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Exception
     */
    public function subscribeUserToGroupByCode($groupCode, User $user)
    {
        $group = $this->findGroupByCode($groupCode);
        $this->getGroupMembershipService()->subscribeUserToGroup($group, $user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     */
    public function updateGroup(Group $group)
    {
        return $this->getGroupRepository()->updateGroup($group);
    }
}