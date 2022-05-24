<?php
namespace Chamilo\Application\Weblcms\CourseType\Storage;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseTypeUserOrder;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the data manager for this package
 *
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from MDB2
 * @package application.weblcms.course_type
 */
class DataManager extends \Chamilo\Application\Weblcms\Storage\DataManager
{
    const PREFIX = 'weblcms_';

    /**
     * **************************************************************************************************************
     * CourseType Functionality *
     * **************************************************************************************************************
     */

    /**
     * Counts the active course types
     *
     * @return int
     */
    public static function count_active_course_types()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ACTIVE),
            new StaticConditionVariable(1)
        );

        return DataManager::count(CourseType::class, new DataClassCountParameters($condition));
    }

    /**
     * Creates the course type user orders for a given user with the current course types
     *
     * @param $user_id int
     *
     * @return boolean
     */
    public static function create_course_type_user_orders_for_user($user_id)
    {
        $course_types = DataManager::retrieve_active_course_types();

        foreach ($course_types as $course_type)
        {
            $course_type_user_order = new CourseTypeUserOrder();
            $course_type_user_order->set_course_type_id($course_type->get_id());
            $course_type_user_order->set_user_id($user_id);
            if (!$course_type_user_order->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Fixes the course tabs user orders when they are broken due to for example new course tabs, removal or
     * (in)activation of course tabs.
     * When new course types are found / activated, new course type user orders are added
     * with highest order (last) When coures types are deleted or inactivated, the course type user oders are deleted
     * and the display orders are fixed.
     *
     * @param $user_id int
     *
     * @return boolean
     */
    public static function fix_course_tab_user_orders_for_user($user_id)
    {
        $user_condition = new EqualityCondition(
            new PropertyConditionVariable(CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id)
        );

        if (self::count(CourseTypeUserOrder::class, new DataClassCountParameters($user_condition)) == 0)
        {
            return true;
        }

        /**
         * Fix all the new / activated course types
         */
        $subcondition = $user_condition;

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ACTIVE),
            new StaticConditionVariable(1)
        );

        $conditions[] = new NotCondition(
            new SubselectCondition(
                new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ID),
                new PropertyConditionVariable(
                    CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_COURSE_TYPE_ID
                ), $subcondition
            )
        );

        $condition = new AndCondition($conditions);

        $active_course_types = DataManager::retrieves(
            CourseType::class, new DataClassRetrievesParameters($condition)
        );

        foreach ($active_course_types as $course_type)
        {
            $course_type_user_order = new CourseTypeUserOrder();
            $course_type_user_order->set_course_type_id($course_type->get_id());
            $course_type_user_order->set_user_id($user_id);
            if (!$course_type_user_order->create())
            {
                return false;
            }
        }

        /**
         * Fix all the deleted / deactivated course types
         */
        $subcondition = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ACTIVE),
            new StaticConditionVariable(1)
        );

        $conditions = [];

        $conditions[] = new NotCondition(
            new SubselectCondition(
                new PropertyConditionVariable(
                    CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_COURSE_TYPE_ID
                ), new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ID), $subcondition
            )
        );
        $conditions[] = $user_condition;

        $condition = new AndCondition($conditions);

        while ($course_type_user_order = DataManager::retrieve(
            CourseTypeUserOrder::class, new DataClassRetrieveParameters($condition)
        ))
        {
            if (!$course_type_user_order->delete())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the highest display order for the course type user orders for a given user
     *
     * @param $user_id int
     *
     * @return int
     */
    public static function get_max_display_order_for_course_type_user_orders($user_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id)
        );

        return self::retrieve_maximum_value(
            CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_DISPLAY_ORDER, $condition
        );
    }

    /**
     * Returns the highest display order for the course types
     *
     * @return int
     */
    public static function get_max_display_order_for_course_types()
    {
        return self::retrieve_maximum_value(CourseType::class, CourseType::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns whether or not a course type has courses
     *
     * @param $course_type_id int
     *
     * @return int
     */
    public static function has_course_type_courses($course_type_id = 0)
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::count_courses_from_course_type(
                $course_type_id
            ) > 0;
    }

    /**
     * **************************************************************************************************************
     * CourseTypeUserOrder Functionality *
     * **************************************************************************************************************
     */

    /**
     * Checks if the given course type exists
     *
     * @param $course_type_name type
     *
     * @return type
     */
    public static function is_course_type_valid($course_type_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_TITLE),
            new StaticConditionVariable($course_type_name)
        );

        return DataManager::count(CourseType::class, new DataClassCountParameters($condition)) != 0;
    }

    /**
     * Retrieves the active course types
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection<\Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType>
     */
    public static function retrieve_active_course_types()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ACTIVE),
            new StaticConditionVariable(1)
        );

        $order = array(
            new OrderProperty(new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_DISPLAY_ORDER))
        );

        $parameters = new DataClassRetrievesParameters($condition, null, null, new OrderBy($order));

        return DataManager::retrieves(CourseType::class, $parameters);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the active course types ordered by the given user display order
     *
     * @param $user_id int
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection<\Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType>
     */
    public static function retrieve_active_course_types_with_user_order($user_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ACTIVE),
            new StaticConditionVariable(1)
        );

        $join_conditions = [];

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id)
        );

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_USER_ID),
            new StaticConditionVariable(null)
        );

        $join_condition = new OrCondition($join_conditions);

        $order = new OrderBy();

        $order->add(
            new OrderProperty(
                new PropertyConditionVariable(CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_DISPLAY_ORDER),
                SORT_ASC
            )
        );

        $order->add(
            new OrderProperty(
                new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_DISPLAY_ORDER)
            )
        );

        return self::retrieve_course_types_with_user_order(
            $join_condition, $condition, null, null, $order
        );
    }

    /**
     * wrapper
     *
     * @param type $id
     */
    public static function retrieve_course_type($id)
    {
        return self::retrieve_by_id(CourseType::class, $id);
    }

    /**
     * Retrieves a course type by name
     *
     * @param $course_type_name type
     *
     * @return type
     */
    public static function retrieve_course_type_by_name($course_type_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_TITLE),
            new StaticConditionVariable($course_type_name)
        );

        return self::retrieve(CourseType::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves a list of available course types joined with course type user order table
     *
     * @param $join_condition \Chamilo\Libraries\Storage\Query\Condition\Condition
     * @param $condition \Chamilo\Libraries\Storage\Query\Condition\Condition
     * @param $offset int
     * @param $max_objects int
     * @param $order_by \Chamilo\Libraries\Storage\Query\OrderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection<\Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType>
     */
    public static function retrieve_course_types_with_user_order(
        $join_condition = null, $condition = null, $offset = null, $max_objects = null, $order_by = null
    )
    {
        $join_conditions = [];

        if ($join_condition)
        {
            $join_conditions[] = $join_condition;
        }

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ID), new PropertyConditionVariable(
                CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_COURSE_TYPE_ID
            )
        );

        $join_condition = new AndCondition($join_conditions);

        $joins = new Joins();

        $joins->add(new Join(CourseTypeUserOrder::class, $join_condition, Join::TYPE_LEFT));

        $parameters = new RecordRetrievesParameters(
            new DataClassProperties(array(new PropertiesConditionVariable(CourseType::class))), $condition,
            $max_objects, $offset, $order_by, $joins
        );

        return self::records(CourseType::class, $parameters);
    }

    /**
     * Retrieves a CourseTypeUserOrder for a given course type and user
     *
     * @param $course_type_id int
     * @param $user_id int
     *
     * @return CourseTypeUserOrder
     */
    public static function retrieve_user_order_for_course_type($course_type_id, $user_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_COURSE_TYPE_ID
            ), new StaticConditionVariable($course_type_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseTypeUserOrder::class, CourseTypeUserOrder::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id)
        );

        $condition = new AndCondition($conditions);

        return self::retrieve(CourseTypeUserOrder::class, new DataClassRetrieveParameters($condition));
    }
}
