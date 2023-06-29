<?php
namespace Chamilo\Core\Group\Storage;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package group.lib
 */

/**
 * This is a skeleton for a data manager for the Users table.
 * Data managers must extend this class and implement its
 * abstract methods.
 *
 * @author     Hans De Bisschop
 * @author     Dieter De Neef
 * @deprecated Use the GroupService and associated services now
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    public const PREFIX = 'group_';

    public static mixed $allSubscribedGroupsCache = [];

    /**
     * @throws \ReflectionException
     * @deprecated Use the GroupService and associated services now
     */
    public static function get_root_group()
    {
        return static::retrieve(
            Group::class, new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            )
        );
    }

    /**
     * @param $user_id
     * @param bool $only_retrieve_ids
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>|int
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     * @deprecated Replaced by GroupService::findAllSubscribedGroupIdentifiersForUserIdentifier() and
     *             GroupService::findAllSubscribedGroupsForUserIdentifier()
     */
    public static function retrieve_all_subscribed_groups_array($user_id, bool $only_retrieve_ids = false): mixed
    {
        $cacheId = md5(serialize([$user_id, $only_retrieve_ids]));

        if (!isset(self::$allSubscribedGroupsCache[$cacheId]))
        {
            // First: retrieve the left and right values of groups the user is directly subscribed to.
            $properties = new RetrieveProperties();

            $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_LEFT_VALUE));
            $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_RIGHT_VALUE));
            $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID));
            $properties->add(new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID));

            $join_conditions = [];

            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id)
            );

            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
            );

            $join = new Join(GroupRelUser::class, new AndCondition($join_conditions));

            $parameters = new RecordRetrievesParameters($properties, null, null, null, null, new Joins([$join]));

            $directly_subscribed_group_nesting_values = static::records(Group::class, $parameters);

            // Second: retrieve the (ids of) directly subscribed groups and their ancestors
            if (count($directly_subscribed_group_nesting_values) > 0)
            {
                $treeConditions = [];
                $alreadyIncludedParents = [];
                $directGroupIds = [];

                foreach ($directly_subscribed_group_nesting_values as $descendent)
                {
                    if (!in_array($descendent[NestedSet::PROPERTY_PARENT_ID], $alreadyIncludedParents))
                    {

                        $treeConditions[] = new AndCondition(
                            [
                                new ComparisonCondition(
                                    new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_LEFT_VALUE),
                                    ComparisonCondition::LESS_THAN_OR_EQUAL,
                                    new StaticConditionVariable($descendent[NestedSet::PROPERTY_LEFT_VALUE])
                                ),

                                new ComparisonCondition(
                                    new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_RIGHT_VALUE),
                                    ComparisonCondition::GREATER_THAN_OR_EQUAL,
                                    new StaticConditionVariable($descendent[NestedSet::PROPERTY_RIGHT_VALUE])
                                )
                            ]
                        );

                        $alreadyIncludedParents[] = $descendent[NestedSet::PROPERTY_PARENT_ID];
                    }

                    $directGroupIds[] = $descendent[DataClass::PROPERTY_ID];
                }

                $treeConditions[] = new InCondition(
                    new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $directGroupIds
                );

                $condition = new OrCondition($treeConditions);

                if ($only_retrieve_ids)
                {
                    $parameters = new DataClassDistinctParameters(
                        $condition,
                        new RetrieveProperties([new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)])
                    );
                    $group_ids = static::distinct(Group::class, $parameters);

                    self::$allSubscribedGroupsCache[$cacheId] = $group_ids;
                }
                else
                {
                    $parameters = new DataClassRetrievesParameters($condition);

                    self::$allSubscribedGroupsCache[$cacheId] = static::retrieves(Group::class, $parameters);
                }
            }
            elseif ($only_retrieve_ids)
            {
                self::$allSubscribedGroupsCache[$cacheId] = [];
            }
            else
            {
                // If the user is not a member of any group
                self::$allSubscribedGroupsCache[$cacheId] = new ArrayCollection([]);
            }
        }

        $groups = self::$allSubscribedGroupsCache[$cacheId];

        if (!$only_retrieve_ids)
        {
            $groups->first();
        }

        return $groups;
    }

    /**
     * Retrieves the groups a user is linked to Use the condition to limit or exclude certain groups
     *
     * @param string $user_id
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use the GroupService and associated services now
     */
    public static function retrieve_direct_subscribed_groups(string $user_id): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id)
        );

        $joins = [];
        $joins[] = new Join(
            GroupRelUser::class, new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
            )
        );
        $joins = new Joins($joins);

        $parameters = new DataClassRetrievesParameters($condition, null, null, null, $joins);

        return self::retrieves(Group::class, $parameters);
    }

    /**
     * @throws \ReflectionException
     * @deprecated Use the GroupService and associated services now
     */
    public static function retrieve_group_by_code($code)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), new StaticConditionVariable($code)
        );

        return self::retrieve(Group::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use the GroupService and associated services now
     */
    public static function retrieve_groups_and_subgroups(
        $group_ids, $additional_condition = null, $count = null, $offset = null, $order_by = null
    ): ArrayCollection
    {
        if (count($group_ids) == 0)
        {
            $group_ids[] = - 1;
        }

        // First: retrieve the left and right values of the groups provided by the caller.
        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_RIGHT_VALUE));

        $condition = new InCondition(new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $group_ids);

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $group_nesting_values = static::records(Group::class, $parameters);

        // Second: retrieve the indicated groups and their descendents
        if (count($group_nesting_values) > 0)
        {
            $conditions = [];

            if ($additional_condition instanceof Condition)
            {
                $conditions[] = $additional_condition;
            }

            $or_conditions = [];

            foreach ($group_nesting_values as $ancestor)
            {
                $or_conditions[] = new AndCondition(
                    [
                        new ComparisonCondition(
                            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_LEFT_VALUE),
                            ComparisonCondition::GREATER_THAN_OR_EQUAL,
                            new StaticConditionVariable($ancestor[NestedSet::PROPERTY_LEFT_VALUE])
                        ),

                        new ComparisonCondition(
                            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_RIGHT_VALUE),
                            ComparisonCondition::LESS_THAN_OR_EQUAL,
                            new StaticConditionVariable($ancestor[NestedSet::PROPERTY_RIGHT_VALUE])
                        )
                    ]
                );
            }

            $conditions[] = new OrCondition($or_conditions);

            $parameters = new DataClassRetrievesParameters(new AndCondition($conditions), $count, $offset, $order_by);

            return static::retrieves(Group::class, $parameters);
        }

        // If the provided group_ids do not exist or were empty
        return new ArrayCollection([]);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use the GroupService and associated services now
     */
    public static function retrieve_user_groups($user_id): ArrayCollection
    {
        $join = new Join(
            GroupRelUser::class, new AndCondition(
                [
                    new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                        new StaticConditionVariable($user_id)
                    ),

                    new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                        new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID)
                    )
                ]
            )
        );

        $joins = new Joins([$join]);

        $parameters = new DataClassRetrievesParameters(null, null, null, null, $joins);

        return static::retrieves(Group::class, $parameters);
    }
}
