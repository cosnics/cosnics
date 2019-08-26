<?php

namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupClosureTable;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Dataclass repository for the group application
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepository extends ClosureTableRepository
{
    /**
     * @var \Chamilo\Core\Group\Storage\Repository\GroupSubscriptionRepository
     */
    protected $groupSubscriptionRepository;

    /**
     * GroupRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Core\Group\Storage\Repository\GroupSubscriptionRepository $groupSubscriptionRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository,
        \Chamilo\Core\Group\Storage\Repository\GroupSubscriptionRepository $groupSubscriptionRepository
    )
    {
        parent::__construct($dataClassRepository);
        $this->groupSubscriptionRepository = $groupSubscriptionRepository;
    }

    /**
     * Finds a GroupRelUser object by a given group code and user id
     *
     * @param string $groupCode
     * @param int $userId
     *
     * @return GroupRelUser | DataClass
     */
    public function findGroupRelUserByGroupCodeAndUserId($groupCode, $userId)
    {
        $user = new User();
        $user->setId($userId);

        return $this->groupSubscriptionRepository->findGroupRelUserByGroupCodeAndUserId($groupCode, $user);
    }

    /**
     * Finds a group object by a given group code
     *
     * @param string $groupCode
     *
     * @return DataClass | Group
     */
    public function findGroupByCode($groupCode)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE),
            new StaticConditionVariable($groupCode)
        );

        return $this->dataClassRepository->retrieve(Group::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param array $groupCodes
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function findGroupsByCodes(array $groupCodes)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE),
            $groupCodes
        );

        return $this->dataClassRepository->retrieves(Group::class, new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param int $groupId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | Group
     */
    public function findGroupByIdentifier($groupId)
    {
        return $this->dataClassRepository->retrieveById(Group::class, $groupId);
    }

    /*****************************************************************************************************************
     * Fallback functionality for dataclass methods                                                                  *
     *****************************************************************************************************************/

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function create(DataClass $dataClass)
    {
        return $dataClass->create();
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     * @throws \Exception
     */
    public function update(DataClass $dataClass)
    {
        return $dataClass->update();
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function delete(DataClass $dataClass)
    {
        return $dataClass->delete();
    }

    /*****************************************************************************************************************
     * Closure Table Functionality                                                                                   *
     *****************************************************************************************************************/

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|Group
     */
    public function getRootGroup()
    {
        return $this->dataClassRepository->retrieve(
            Group::class,
            new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            )
        );
    }

    /**
     * Creates a new group and adds the group to the closure table
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     */
    public function createGroup(Group $group)
    {
        // LEGACY CODE: make sure that the group is not already created using the create dataclass. We use
        // double code to make sure that the nested sets are used as backup until the refactoring is done.
        if(!$group->is_identified())
        {
            $success = $this->dataClassRepository->create($group);
            if (!$success)
            {
                return false;
            }
        }

        return $this->addGroupToClosureTable($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     */
    public function updateGroup(Group $group)
    {
        return $this->dataClassRepository->update($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     */
    public function deleteGroup(Group $group)
    {
        $success = $this->dataClassRepository->delete($group);
        if (!$success)
        {
            return false;
        }

        return $this->deleteChildFromTree(GroupClosureTable::class, $group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int $newParentId
     *
     * @return bool|void
     */
    public function moveGroup(Group $group, int $newParentId)
    {
        return $this->moveChildToNewParent(GroupClosureTable::class, $group, $newParentId);
    }

    /**
     * Adds an existing group to the closure table
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return bool
     */
    public function addGroupToClosureTable(Group $group)
    {
        return $this->addChildToParent(GroupClosureTable::class, $group, $group->get_parent_id());
    }

    /**
     * Returns all the child groups for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function getAllChildrenForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->getAllChildrenByParentId(Group::class, GroupClosureTable::class, $group->getId(), $includeSelf);
    }

    /**
     * Returns the identifiers of all the children for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return int[]|string[]
     */
    public function getAllChildIdsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->getAllChildIdsByParentId(GroupClosureTable::class, $group->getId(), $includeSelf);
    }

    /**
     * @param Group $group
     * @param bool $includeSelf
     *
     * @return int
     */
    public function countAllChildrenForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->countAllChildrenByParentId(GroupClosureTable::class, $group->getId(), $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function getDirectChildrenOfGroup(Group $group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($group->getId())
        );

        return $this->dataClassRepository->retrieves(Group::class, new DataClassRetrievesParameters($condition));
    }

    /**
     * @param Group $group
     *
     * @return int
     */
    public function countDirectChildrenForGroup(Group $group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($group->getId())
        );

        return $this->dataClassRepository->count(Group::class, new DataClassCountParameters($condition));
    }

    /**
     * Returns all the parent groups for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function getAllParentsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->getAllParentsByChildId(Group::class, GroupClosureTable::class, $group->getId(), $includeSelf);
    }

    /**
     * Returns all the parent ids for a given group. Has the possibility to include the given group.
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return int[]|string[]
     */
    public function getAllParentIdsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->getAllParentIdsByChildId(GroupClosureTable::class, $group->getId(), $includeSelf);
    }

    /**
     * @param Group $group
     * @param bool $includeSelf
     *
     * @return int
     */
    public function countAllParentsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->countAllParentsByChildId(GroupClosureTable::class, $group->getId(), $includeSelf);
    }

    /**
     * Returns the direct parent group of a given group
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|Group
     */
    public function getDirectParentOfGroup(Group $group)
    {
        return $this->dataClassRepository->retrieveById(Group::class, $group->get_parent_id());
    }

}