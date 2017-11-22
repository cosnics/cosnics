<?php
namespace Chamilo\Core\Group\Storage;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\EmptyResultSet;

/**
 *
 * @package group.lib
 */
/**
 * This is a skeleton for a data manager for the Users table.
 * Data managers must extend this class and implement its
 * abstract methods.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'group_';

    /**
     * Cache for the direct subscribed groups
     *
     * @var Group[]
     */
    private static $direct_subscribed_groups_cache;

    public static function get_root_group()
    {
        return static::retrieve(
            Group::class_name(),
            new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0))));
    }

    public static function retrieve_group_by_code($code)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_CODE),
            new StaticConditionVariable($code));

        return self::retrieve(Group::class_name(), new DataClassRetrieveParameters($condition));
    }

    public static function retrieve_group_by_code_and_parent_id($code, $parent_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_CODE),
            new StaticConditionVariable($code));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_id));
        $condition = new AndCondition($conditions);

        return self::retrieve(Group::class_name(), new DataClassRetrieveParameters($condition));
    }

    public static function subscribe_user_to_group_by_official_code_and_group_code($official_code, $group_code)
    {
        $group_rel_user = self::make_group_rel_user_from_official_code_and_group_code($official_code, $group_code);

        if ($group_rel_user)
        {
            return $group_rel_user->create();
        }

        return false;
    }

    public static function remove_user_from_group_by_official_code_and_group_code($official_code, $group_code)
    {
        $group_rel_user = self::make_group_rel_user_from_official_code_and_group_code($official_code, $group_code);

        if ($group_rel_user)
        {
            return $group_rel_user->delete();
        }

        return false;
    }

    // cache to make subscribing users in batch more performant
    private static $group_rel_user_cache;

    private static function make_group_rel_user_from_official_code_and_group_code($official_code, $group_code)
    {
        if (! self::$group_rel_user_cache[$group_code][$official_code])
        {
            $group = self::retrieve_group_by_code($group_code);
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_official_code($official_code);

            if (! $group || ! $user)
            {
                return false;
            }

            $group_rel_user = new GroupRelUser();
            $group_rel_user->set_group_id($group->get_id());
            $group_rel_user->set_user_id($user->get_id());
            self::$group_rel_user_cache[$group_code][$official_code] = $group_rel_user;
        }

        return self::$group_rel_user_cache[$group_code][$official_code];
    }

    public static function is_group_member($group_id, $user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
        $condition = new AndCondition($conditions);

        return static::count(GroupRelUser::class_name(), $condition) > 0;
    }

    public static function retrieve_user_groups($user_id)
    {
        $join = new Join(
            GroupRelUser::class_name(),
            new AndCondition(
                array(
                    new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
                        new StaticConditionVariable($user_id)),

                    new EqualityCondition(
                        new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
                        new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID)))));

        $joins = new Joins(array($join));

        $parameters = new DataClassRetrievesParameters(null, null, null, null, $joins);
        return static::retrieves(Group::class_name(), $parameters);
    }

    public static $allSubscribedGroupsCache = array();

    public static function retrieve_all_subscribed_groups_array($user_id, $only_retrieve_ids = false)
    {
        $cacheId = md5(serialize(array($user_id, $only_retrieve_ids)));

        if (! isset(self::$allSubscribedGroupsCache[$cacheId]))
        {
            // First: retrieve the left and right values of groups the user is directly subscribed to.
            $properties = new DataClassProperties();

            $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_LEFT_VALUE));
            $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_RIGHT_VALUE));
            $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID));
            $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID));

            $join_conditions = array();

            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));

            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID));

            $join = new Join(GroupRelUser::class_name(), new AndCondition($join_conditions));

            $parameters = new RecordRetrievesParameters($properties, null, null, null, null, new Joins(array($join)));

            $directly_subscribed_group_nesting_values = static::records(Group::class_name(), $parameters)->as_array();

            // Second: retrieve the (ids of) directly subscribed groups and their ancestors
            if (count($directly_subscribed_group_nesting_values) > 0)
            {
                $treeConditions = array();
                $alreadyIncludedParents = array();
                $directGroupIds = array();

                foreach ($directly_subscribed_group_nesting_values as $descendent)
                {
                    if (! in_array($descendent[Group::PROPERTY_PARENT_ID], $alreadyIncludedParents))
                    {

                        $treeConditions[] = new AndCondition(
                            array(
                                new InequalityCondition(
                                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_LEFT_VALUE),
                                    InequalityCondition::LESS_THAN_OR_EQUAL,
                                    new StaticConditionVariable($descendent[Group::PROPERTY_LEFT_VALUE])),

                                new InequalityCondition(
                                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_RIGHT_VALUE),
                                    InequalityCondition::GREATER_THAN_OR_EQUAL,
                                    new StaticConditionVariable($descendent[Group::PROPERTY_RIGHT_VALUE]))));

                        $alreadyIncludedParents[] = $descendent[Group::PROPERTY_PARENT_ID];
                    }

                    $directGroupIds[] = $descendent[Group::PROPERTY_ID];
                }

                $treeConditions[] = new InCondition(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID),
                    $directGroupIds);

                $condition = new OrCondition($treeConditions);

                if ($only_retrieve_ids)
                {
                    $parameters = new DataClassDistinctParameters(
                        $condition,
                        new DataClassProperties(array(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID))));
                    $group_ids = static::distinct(Group::class_name(), $parameters);

                    self::$allSubscribedGroupsCache[$cacheId] = $group_ids;
                }
                else
                {
                    $parameters = new DataClassRetrievesParameters($condition);

                    self::$allSubscribedGroupsCache[$cacheId] = static::retrieves(Group::class_name(), $parameters);
                }
            }
            else
            {
                if ($only_retrieve_ids)
                {
                    self::$allSubscribedGroupsCache[$cacheId] = array();
                }
                else
                {
                    // If the user is not a member of any group
                    self::$allSubscribedGroupsCache[$cacheId] = new EmptyResultSet();
                }
            }
        }

        $groups = self::$allSubscribedGroupsCache[$cacheId];

        if (! $only_retrieve_ids)
        {
            $groups->reset();
        }

        return $groups;
    }

    public static function retrieve_groups_and_subgroups($group_ids, $additional_condition = null, $count = null, $offset = null,
        $order_by = array())
    {
        if (count($group_ids) == 0)
        {
            $group_ids[] = - 1;
        }

        // First: retrieve the left and right values of the groups provided by the caller.
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_RIGHT_VALUE));

        $condition = new InCondition(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID), $group_ids);

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $group_nesting_values = static::records(Group::class_name(), $parameters)->as_array();

        // Second: retrieve the indicated groups and their descendents
        if (count($group_nesting_values) > 0)
        {
            $conditions = array();

            if ($additional_condition instanceof Condition)
            {
                $conditions[] = $additional_condition;
            }

            $or_conditions = array();

            foreach ($group_nesting_values as $ancestor)
            {
                $or_conditions[] = new AndCondition(
                    array(
                        new InequalityCondition(
                            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_LEFT_VALUE),
                            InequalityCondition::GREATER_THAN_OR_EQUAL,
                            new StaticConditionVariable($ancestor[Group::PROPERTY_LEFT_VALUE])),

                        new InequalityCondition(
                            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_RIGHT_VALUE),
                            InequalityCondition::LESS_THAN_OR_EQUAL,
                            new StaticConditionVariable($ancestor[Group::PROPERTY_RIGHT_VALUE]))));
            }

            $conditions[] = new OrCondition($or_conditions);

            $parameters = new DataClassRetrievesParameters(new AndCondition($conditions), $count, $offset, $order_by);

            return static::retrieves(Group::class_name(), $parameters);
        }

        // If the provided group_ids do not exist or were empty
        return new EmptyResultSet();
    }

    /**
     * Retrieves the direct subscribed groups as an array
     *
     * @param int $user_id
     * @param bool $only_retrieve_ids
     *
     * @return Group[] | int[]
     */
    public static function retrieve_direct_subscribed_groups_array($user_id, $only_retrieve_ids = false)
    {
        $cache_hash = md5($user_id . ':' . (int) $only_retrieve_ids);

        if (is_null(self::$direct_subscribed_groups_cache[$cache_hash]))
        {
            $direct_subscribed_groups = self::retrieve_direct_subscribed_groups($user_id);
            if (! $direct_subscribed_groups->is_empty())
            {
                if ($only_retrieve_ids)
                {
                    while ($direct_subscribed_group = $direct_subscribed_groups->next_result())
                    {
                        self::$direct_subscribed_groups_cache[$cache_hash][] = $direct_subscribed_group->get_id();
                    }
                }
                else
                {
                    while ($direct_subscribed_group = $direct_subscribed_groups->next_result())
                    {
                        self::$direct_subscribed_groups_cache[$cache_hash][] = $direct_subscribed_group;
                    }
                }
            }
        }
        return self::$direct_subscribed_groups_cache[$cache_hash];
    }

    /**
     * Retrieves the groups a user is linked to Use the condition to limit or exclude certain groups
     *
     * @param int $user_id
     *
     * @return Mdb2ResultSet
     */
    public static function retrieve_direct_subscribed_groups($user_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));

        $joins = array();
        $joins[] = new Join(
            GroupRelUser::class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID)));
        $joins = new Joins($joins);

        $parameters = new DataClassRetrievesParameters($condition, null, null, null, $joins);
        return self::retrieves(Group::class_name(), $parameters);
    }

    public static function retrieve_group_rel_users_with_user_join($condition = null, $offset = null, $count = null,
        $order_property = null)
    {
        $joins = array();
        $joins[] = new Join(
            User::class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID)));

        return DataManager::retrieves(
            GroupRelUser::class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property, new Joins($joins)));
    }
}
