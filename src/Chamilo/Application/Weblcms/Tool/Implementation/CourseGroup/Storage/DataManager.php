<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;

/**
 * This class represents the data manager for this package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package application.weblcms.tool.assignment
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'weblcms_';

    /**
     * **************************************************************************************************************
     * CourseGroup Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves a course group by a given name
     *
     * @param string $name
     *
     * @return CourseGroup
     */
    public static function retrieve_course_group_by_name($name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_NAME),
            new StaticConditionVariable($name));

        return self :: retrieve(CourseGroup :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves the root course group of a given course
     *
     * @param int $course_id
     *
     * @return CourseGroup
     */
    public static function retrieve_course_group_root($course_id)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
            new StaticConditionVariable(0));

        $condition = new AndCondition($conditions);

        return self :: retrieve(CourseGroup :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieve given course groups with their subgroups
     *
     * @param int[] $group_ids
     * @param \libraries\storage\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     *
     * @return \libraries\storage\ResultSet<CourseGroup>
     */
    public static function retrieve_course_groups_and_subgroups($group_ids, $condition = null, $offset = null, $count = null,
        $order_by = null)
    {
        if (count($group_ids) == 0)
        {
            $group_ids[] = - 1;
        }

        $dg_condition = new InCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID),
            $group_ids);

        $direct_groups = self :: retrieves(CourseGroup :: class_name(), new DataClassRetrievesParameters($dg_condition));

        $direct_group_conditions = array();
        while ($group = $direct_groups->next_result())
        {
            $and_conditions = array();

            $and_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_LEFT_VALUE),
                InequalityCondition :: GREATER_THAN_OR_EQUAL,
                new StaticConditionVariable($group->get_left_value()));

            $and_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_RIGHT_VALUE),
                InequalityCondition :: LESS_THAN_OR_EQUAL,
                new StaticConditionVariable($group->get_right_value()));

            $and_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
                new StaticConditionVariable($group->get_course_code()));

            $direct_group_conditions[] = new AndCondition($and_conditions);
        }

        if (count($direct_group_conditions) > 0)
        {
            $group_conditions = array();

            if ($condition)
            {
                $group_conditions[] = $condition;
            }

            $group_conditions[] = new OrCondition($direct_group_conditions);

            $group_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
                InequalityCondition :: GREATER_THAN,
                new StaticConditionVariable(0));

            $group_condition = new AndCondition($group_conditions);
        }
        else
        {
            $group_condition = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID),
                new StaticConditionVariable(- 1));
        }

        return self :: retrieves(
            CourseGroup :: class_name(),
            new DataClassRetrievesParameters($group_condition, $count, $offset, $order_by));
    }

    /**
     * **************************************************************************************************************
     * CourseGroupUserRelation Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the user ids from a given course group
     *
     * @param int $course_group_id
     *
     * @return int[]
     */
    public static function retrieve_course_group_user_ids($course_group_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation :: class_name(),
                CourseGroupUserRelation :: PROPERTY_COURSE_GROUP),
            new StaticConditionVariable($course_group_id));

        $relations = self :: retrieves(
            CourseGroupUserRelation :: class_name(),
            new DataClassRetrievesParameters($condition));
        $user_ids = array();

        while ($relation = $relations->next_result())
        {
            $user_ids[] = $relation->get_user();
        }

        return $user_ids;
    }

    /**
     * Retrieves the course group users as user objects
     *
     * @param int $course_group_id
     * @param \libraries\storage\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_property
     *
     * @return \libraries\storage\ResultSet<\user\User>
     */
    public static function retrieve_course_group_users($course_group_id, $condition = null, $offset = null, $count = null,
        $order_property = null)
    {
        $user_ids = self :: retrieve_course_group_user_ids($course_group_id);

        if (count($user_ids) > 0)
        {
            $user_condition = new InCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                $user_ids);

            if (is_null($condition))
            {
                $condition = $user_condition;
            }
            else
            {
                $condition = new AndCondition($condition, $user_condition);
            }
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                new StaticConditionVariable('-1000'));
        }

        return \Chamilo\Core\User\Storage\DataManager :: retrieves(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     * Subscribes the given users to the given course group
     *
     * @param \core\user\User[] $users
     * @param CourseGroup $course_group
     *
     * @return bool
     */
    public static function subscribe_users_to_course_groups($users, $course_group)
    {
        if (! is_array($users))
        {
            $users = array($users);
        }

        foreach ($users as $user)
        {
            $course_group_user_relation = new CourseGroupUserRelation();

            $course_group_user_relation->set_course_group($course_group->get_id());
            $course_group_user_relation->set_user($user->get_id());

            if (! $course_group_user_relation->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Unsubscribes users from a given course group
     *
     * @param int[] $user_ids
     * @param int $course_group_id
     *
     * @return bool
     */
    public static function unsubscribe_users_from_course_groups($user_ids, $course_group_id)
    {
        if (! is_array($user_ids))
        {
            $user_ids = array($user_ids);
        }

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation :: class_name(),
                CourseGroupUserRelation :: PROPERTY_COURSE_GROUP),
            new StaticConditionVariable($course_group_id));

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation :: class_name(),
                CourseGroupUserRelation :: PROPERTY_USER),
            $user_ids);

        $condition = new AndCondition($conditions);
        return self :: deletes(CourseGroupUserRelation :: class_name(), $condition);
    }

    /**
     * Checks if a given user is a member of a given course group
     *
     * @param int $course_group_id
     * @param int $user_id
     *
     * @return bool
     */
    public static function is_course_group_member($course_group_id, $user_id)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation :: class_name(),
                CourseGroupUserRelation :: PROPERTY_COURSE_GROUP),
            new StaticConditionVariable($course_group_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation :: class_name(),
                CourseGroupUserRelation :: PROPERTY_USER),
            new StaticConditionVariable($user_id));

        $condition = new AndCondition($conditions);

        return self :: count(CourseGroupUserRelation :: class_name(), $condition) > 0;
    }

    /**
     * Counts the course group users by a given course group and additionally user conditions
     *
     * @param int $course_group_id
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public static function count_course_group_users($course_group_id, $condition = null)
    {
        $user_ids = DataManager :: retrieve_course_group_user_ids($course_group_id);
        if (count($user_ids) > 0)
        {
            $user_condition = new InCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                $user_ids);

            if (is_null($condition))
            {
                $condition = $user_condition;
            }
            else
            {
                $condition = new AndCondition($condition, $user_condition);
            }

            return \Chamilo\Core\User\Storage\DataManager :: count(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                $condition);
        }
        else
        {
            return 0;
        }
    }

    /**
     * Checks and returns whether or not more subscriptions are allowed for a user in a group
     *
     * @param int $course_group_id
     * @param int $user_id
     *
     * @return bool
     */
    public static function more_subscriptions_allowed_for_user_in_group($course_group_id, $user_id)
    {
        if ($course_group_id == 0)
        {
            return true;
        }

        $course_group = self :: retrieve_by_id(CourseGroup :: class_name(), $course_group_id);
        if (self :: retrieve_course_group_root($course_group->get_course_code())->get_id() == $course_group->get_id())
        {
            return true; // If the parent is the root course group, allow it.
        }

        $all_groups = $course_group->get_children(false);

        $num_groups = 0;
        $max_groups = $course_group->get_max_number_of_course_group_per_member();

        /**
         * max members per group = 0 => not limited
         */
        if ($max_groups == 0)
        {
            return true;
        }

        while ($group_course_group = $all_groups->next_result())
        {
            $conditions = array();

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseGroupUserRelation :: class_name(),
                    CourseGroupUserRelation :: PROPERTY_COURSE_GROUP),
                new StaticConditionVariable($group_course_group->get_id()));

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseGroupUserRelation :: class_name(),
                    CourseGroupUserRelation :: PROPERTY_USER),
                new StaticConditionVariable($user_id));

            $condition = new AndCondition($conditions);

            $users = self :: retrieves(
                CourseGroupUserRelation :: class_name(),
                new DataClassRetrievesParameters($condition));

            if ($users->next_result() != null)
            {
                $num_groups ++;
            }
        }

        return $num_groups < $max_groups;
    }

    /**
     * Retrieves the course group from a given user and optionally a given course
     *
     * @param $user_id
     * @param null $course_id
     * @return mixed
     */
    public static function retrieve_course_groups_from_user($user_id, $course_id = null)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                CourseGroupUserRelation :: class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseGroupUserRelation :: class_name(),
                        CourseGroupUserRelation :: PROPERTY_COURSE_GROUP),
                    new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID))));

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation :: class_name(),
                CourseGroupUserRelation :: PROPERTY_USER),
            new StaticConditionVariable($user_id));

        if (! is_null($course_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
                new StaticConditionVariable($course_id));
        }

        $condition = new AndCondition($conditions);

        $parameters = new DataClassRetrievesParameters($condition, null, null, array(), $joins);

        return self :: retrieves(CourseGroup :: class_name(), $parameters);
    }

    /**
     * Returns the course groups form a given user for a given course as a string
     *
     * @param int $user_id
     * @param int $course_id
     *
     * @return string
     */
    public static function get_course_groups_from_user_as_string($user_id, $course_id)
    {
        $data_set = self :: retrieve_course_groups_from_user($user_id, $course_id);

        $course_groups_subscribed = array();
        while ($course_group = $data_set->next_result())
        {
            $course_groups_subscribed[] = $course_group->get_name();
        }

        return implode(', ', $course_groups_subscribed);
    }

    /**
     * Retrieves the possible users for a course group
     *
     * @param CourseGroup $course_group
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[]$order_property
     * @return \core\user\User[]
     */
    public static function retrieve_possible_course_group_users($course_group, $condition = null, $offset = null, $count = null,
        $order_property = null)
    {
        $condition = self :: get_possible_course_group_users_condition($course_group, $condition);

        return \Chamilo\Core\User\Storage\DataManager :: retrieves(
            User :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     * Counts the possible users for a course group
     *
     * @param CourseGroup $course_group
     * @param Condition $conditions
     *
     * @return int
     */
    public static function count_possible_course_group_users($course_group, $conditions = null)
    {
        $condition = self :: get_possible_course_group_users_condition($course_group, $conditions);
        return \Chamilo\Core\User\Storage\DataManager :: count(User :: class_name(), $condition);
    }

    /**
     * Returns the course groups for a given user
     *
     * @static
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     * @param int $user_id
     * @param int $course_id
     *
     * @return CourseGroup[]
     */
    public static function get_user_course_groups($user_id, $course_id)
    {
        $course_groups = self :: retrieve_course_groups_from_user($user_id, $course_id)->as_array();

        $course_groups_recursive = array();

        foreach ($course_groups as $course_group)
        {
            if (! array_key_exists($course_group->get_id(), $course_groups_recursive))
            {
                $course_groups_recursive[$course_group->get_id()] = $course_group;
            }

            $parents = $course_group->get_parents(false);

            while ($parent = $parents->next_result())
            {
                if (! array_key_exists($parent->get_id(), $course_groups_recursive))
                {
                    $course_groups_recursive[$parent->get_id()] = $parent;
                }
            }
        }

        return $course_groups_recursive;
    }

    /**
     * **************************************************************************************************************
     * CourseGroupUserRelation Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the condition for the possible course group users
     *
     * @param CourseGroup $course_group
     * @param Condition $condition
     *
     * @return Condition
     */
    protected static function get_possible_course_group_users_condition($course_group, $condition)
    {
        $course_condition = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_group->get_course_code()));

        $course_users = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseUserRelation :: class_name(),
            new DataClassRetrievesParameters($course_condition));

        $group_user_ids = DataManager :: retrieve_course_group_user_ids($course_group->get_id());

        $course_user_ids = array();

        while ($course_user = $course_users->next_result())
        {
            $course_user_ids[] = $course_user->get_user();
        }

        $conditions = array();
        $conditions[] = $condition;

        $user_id_variable = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID);

        $conditions[] = new InCondition($user_id_variable, new $course_user_ids());

        $conditions[] = new NotCondition(new InCondition($user_id_variable, $group_user_ids));

        return new AndCondition($conditions);
    }
}
