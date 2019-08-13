<?php

namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupClosureTable;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Class GroupSubscriptionRepository
 * @package Chamilo\Core\Group\Storage\Repository
 */
class GroupSubscriptionRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * GroupSubscriptionRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Finds a GroupRelUser object by a given group code and user id
     *
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return GroupRelUser | DataClass
     */
    public function findGroupRelUserByGroupCodeAndUserId($groupCode, User $user)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
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
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|GroupRelUser
     */
    public function findGroupUserRelation(Group $group, User $user)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            GroupRelUser::class,
            new DataClassRetrieveParameters($condition, array())
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\GroupRelUser $groupRelUser
     *
     * @return bool
     */
    public function createGroupUserRelation(GroupRelUser $groupRelUser)
    {
        return $this->dataClassRepository->create($groupRelUser);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\GroupRelUser $groupRelUser
     *
     * @return bool
     */
    public function deleteGroupUserRelation(GroupRelUser $groupRelUser)
    {
        return $this->dataClassRepository->delete($groupRelUser);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|User[]
     */
    public function findUsersDirectlySubscribedToGroup(Group $group)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                GroupRelUser::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID)
                )
            )
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->getId())
        );

        return $this->dataClassRepository->retrieves(
            User::class, new DataClassRetrievesParameters($condition, null, null, array(), $joins)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return int[]|string[]
     */
    public function findUserIdsDirectlySubscribedToGroup(Group $group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->getId())
        );

        $distinctProperties = new DataClassProperties();
        $distinctProperties->add(new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID));

        return $this->dataClassRepository->distinct(
            GroupRelUser::class, new DataClassDistinctParameters($condition, $distinctProperties)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|User[]
     */
    public function findUsersInGroupAndSubgroups(Group $group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_PARENT_ID),
            new StaticConditionVariable($group->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                GroupRelUser::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                GroupClosureTable::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_CHILD_ID)
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            User::class, new DataClassRetrievesParameters($condition, null, null, [], $joins, true)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return int[]|string[]
     */
    public function findUserIdsInGroupAndSubgroups(Group $group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_PARENT_ID),
            new StaticConditionVariable($group->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                GroupClosureTable::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_CHILD_ID)
                )
            )
        );

        $distinctProperties = new DataClassProperties();
        $distinctProperties->add(new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID));

        return $this->dataClassRepository->distinct(
            GroupRelUser::class, new DataClassDistinctParameters($condition, $distinctProperties, $joins)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function findGroupsWhereUserIsDirectlySubscribed(User $user)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                GroupRelUser::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            Group::class, new DataClassRetrievesParameters($condition, null, null, [], $joins)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int[]|string[]
     */
    public function findGroupIdsWhereUserIsDirectlySubscribed(User $user)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $distinctProperties = new DataClassProperties();
        $distinctProperties->add(new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID));

        return $this->dataClassRepository->distinct(
            GroupRelUser::class, new DataClassDistinctParameters($condition, $distinctProperties)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function findAllGroupsForUser(User $user)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                GroupClosureTable::class,
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                    new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_PARENT_ID)
                )
            )
        );

        $joins->add(
            new Join(
                GroupRelUser::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_CHILD_ID)
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            Group::class, new DataClassRetrievesParameters($condition, null, null, [], $joins, true)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int[]|string[]
     */
    public function findAllGroupIdsForUser(User $user)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                GroupRelUser::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_CHILD_ID)
                )
            )
        );

        $distinctProperties = new DataClassProperties();

        $distinctProperties->add(
            new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_PARENT_ID)
        );

        return $this->dataClassRepository->distinct(
            GroupClosureTable::class, new DataClassDistinctParameters($condition, $distinctProperties, $joins)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function countUserSubscriptionsToGroupAndSubgroups(Group $group, User $user)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_PARENT_ID),
            new StaticConditionVariable($group->getId())
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                GroupClosureTable::class,
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new PropertyConditionVariable(GroupClosureTable::class, GroupClosureTable::PROPERTY_CHILD_ID)
                )
            )
        );

        return $this->dataClassRepository->count(GroupRelUser::class, new DataClassCountParameters($condition, $joins));
    }

}