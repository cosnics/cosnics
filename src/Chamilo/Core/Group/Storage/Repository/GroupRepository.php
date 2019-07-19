<?php

namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupClosureTable;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
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
     * Finds a GroupRelUser object by a given group code and user id
     *
     * @param string $groupCode
     * @param int $userId
     *
     * @return GroupRelUser | DataClass
     */
    public function findGroupRelUserByGroupCodeAndUserId($groupCode, $userId)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE),
            new StaticConditionVariable($groupCode)
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                Group::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)
                )
            )
        );

        return $this->dataClassRepository->retrieve(
            GroupRelUser::class,
            new DataClassRetrieveParameters($condition, array(), $joins)
        );
    }

    /**
     *
     * @param int $groupId
     * @param int $userId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|GroupRelUser
     */
    public function findGroupRelUserByGroupAndUserId($groupId, $userId)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($groupId)
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            GroupRelUser::class,
            new DataClassRetrieveParameters($condition, array())
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function findDirectChildrenFromGroup(Group $group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($group->getId())
        );

        return $this->dataClassRepository->retrieves(Group::class, new DataClassRetrievesParameters($condition));
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

    public function testClosureTable()
    {
//        $condition = new EqualityCondition(
//            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
//            new StaticConditionVariable(0)
//        );
//
//        $root = $this->dataClassRepository->retrieve(Group::class, new DataClassRetrieveParameters($condition));
//        $this->addChildToParent(GroupClosureTable::class, $root, 0);
//        $this->addChildren($root);
var_dump($this->getAllParentsByChildId(Group::class, GroupClosureTable::class, 33));

$group = new Group();
$group->setId(31);
//
//$this->deleteChildFromTree(GroupClosureTable::class, $group);

//        $this->moveChildToNewParent(GroupClosureTable::class, $group, 2);
    }

    public function addChildren(Group $group)
    {
        $children = $group->get_children(false);
        foreach($children as $child)
        {
            $this->addChildToParent(GroupClosureTable::class, $child, $group->getId());
            $this->addChildren($child);
        }
    }
}