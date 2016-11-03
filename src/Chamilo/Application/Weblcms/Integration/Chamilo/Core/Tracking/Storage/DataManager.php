<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\RecordResultSet;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'tracking_weblcms_';

    /**
     * **************************************************************************************************************
     * CourseVisit Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves a course visit tracker record by user and course data category_id and publication_id can be null, but
     * still need to be a condition because we can also register access to a course without a publication or category
     *
     * @param int $user_id
     * @param int $course_id
     * @param int $tool_id
     * @param int $category_id
     * @param int $publication_id
     *
     * @return CourseVisit
     */
    public static function retrieve_course_visit_by_user_and_course_data($user_id, $course_id, $tool_id,
        $category_id = null, $publication_id = null)
    {
        $condition = self :: get_course_visit_conditions_by_user_and_course_data(
            $user_id,
            $course_id,
            $tool_id,
            $category_id,
            $publication_id);

        return self :: retrieve(CourseVisit :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Returns the summary data of a the access of a course, optionally limited by user
     *
     * @param int $course_id
     * @param int $user_id
     *
     * @return string[]
     */
    public static function retrieve_course_access_summary_data($course_id, $user_id = null)
    {
        $condition = $user_id ? self :: get_course_visit_conditions_by_user_and_course_data(
            $user_id,
            $course_id,
            null,
            null,
            null,
            false) : self :: get_course_visit_conditions_by_course_data($course_id, null, null, null, false);

        $parameters = new RecordRetrieveParameters(self :: get_course_visit_summary_select_properties(), $condition);

        return self :: record(CourseVisit :: class_name(), $parameters);
    }

    /**
     * Returns the summary access data for the tools (optionally limited to a user or not)
     *
     * @param int $course_id
     * @param int $user_id = null
     * @return RecordResultSet
     */
    public static function retrieve_tools_access_summary_data($course_id = null, $user_id = null)
    {
        $course_tool_name_variable = new PropertyConditionVariable(
            CourseTool :: class_name(),
            CourseTool :: PROPERTY_NAME);

        $properties = self :: get_course_visit_summary_select_properties();
        $properties->add($course_tool_name_variable);

        $join_conditions = array();

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_TOOL_ID),
            new PropertyConditionVariable(CourseTool :: class_name(), CourseTool :: PROPERTY_ID));

        if ($course_id)
        {
            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_COURSE_ID),
                new StaticConditionVariable($course_id));
        }

        if ($user_id)
        {
            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));
        }

        $join_condition = new AndCondition($join_conditions);

        $joins = new Joins();
        $joins->add(new Join(CourseTool :: class_name(), $join_condition, Join :: TYPE_RIGHT));

        $group_by = new GroupBy();
        $group_by->add($course_tool_name_variable);

        $parameters = new RecordRetrievesParameters($properties, null, null, null, array(), $joins, $group_by);

        return self :: records(CourseVisit :: class_name(), $parameters);
    }

    /**
     * Retrieves the summary access data for a publication (optionally limited to a user or not)
     *
     * @param int $course_id
     * @param int $tool_id
     * @param int $category_id
     * @param int $publication_id
     * @param int $user_id - [OPTIONAL]
     * @return RecordResultSet
     */
    public static function retrieve_publication_access_summary_data($course_id, $tool_id, $category_id, $publication_id,
        $user_id = null)
    {
        $condition = $user_id ? self :: get_course_visit_conditions_by_user_and_course_data(
            $user_id,
            $course_id,
            $tool_id,
            $category_id,
            $publication_id,
            false) : self :: get_course_visit_conditions_by_course_data(
            $course_id,
            $tool_id,
            $category_id,
            $publication_id,
            false);

        $parameters = new RecordRetrieveParameters(self :: get_course_visit_summary_select_properties(), $condition);

        return self :: record(CourseVisit :: class_name(), $parameters);
    }

    /**
     * Counts the courses that are accessed after a given timestamp
     *
     * @param int $timestamp
     *
     * @return int
     */
    public static function count_courses_with_last_access_after_time($timestamp)
    {
        return self :: count_courses_with_last_access_against_time(
            $timestamp,
            InequalityCondition :: GREATER_THAN_OR_EQUAL);
    }

    /**
     * Counts the courses that are accessed before a given timestamp
     *
     * @param int $timestamp
     *
     * @return int
     */
    public static function count_courses_with_last_access_before_time($timestamp)
    {
        return self :: count_courses_with_last_access_against_time(
            $timestamp,
            InequalityCondition :: LESS_THAN_OR_EQUAL);
    }

    /**
     * **************************************************************************************************************
     * CourseVisit Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the conditions for the course visit tracker
     *
     * @param int $course_id
     * @param int $tool_id
     * @param int $user_id
     * @param int $category_id
     * @param int $publication_id
     * @param bool $use_null_values - use null values as actual select values or not - default true
     * @return \libraries\storage\AndCondition
     */
    public static function get_course_visit_conditions_by_user_and_course_data($user_id, $course_id, $tool_id,
        $category_id, $publication_id, $use_null_values = true)
    {
        $conditions = array();

        $conditions[] = self :: get_course_visit_conditions_by_user_data($user_id);
        $conditions[] = self :: get_course_visit_conditions_by_course_data(
            $course_id,
            $tool_id,
            $category_id,
            $publication_id,
            $use_null_values);

        return new AndCondition($conditions);
    }

    /**
     * Gets the condition to retrieve a course visit tracker by user data
     *
     * @param int $user_id
     *
     * @return EqualityCondition
     */
    public static function get_course_visit_conditions_by_user_data($user_id)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
    }

    /**
     * Gets the condition to retrieve a course visit tracker by course data
     *
     * @param int $course_id
     * @param int $tool_id
     * @param int $category_id
     * @param int $publication_id
     * @param bool $use_null_values - use null values as actual select values or not - default true
     * @return AndCondition
     */
    public static function get_course_visit_conditions_by_course_data($course_id, $tool_id, $category_id,
        $publication_id, $use_null_values = true)
    {
        $conditions = array();

        if (! is_null($course_id) || $use_null_values)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_COURSE_ID),
                ! is_null($course_id) ? new StaticConditionVariable($course_id) : null);
        }

        if (! is_null($tool_id) || $use_null_values)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_TOOL_ID),
                ! is_null($tool_id) ? new StaticConditionVariable($tool_id) : null);
        }

        if (! is_null($category_id) || $use_null_values)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_CATEGORY_ID),
                ! is_null($category_id) ? new StaticConditionVariable($category_id) : null);
        }

        if (! is_null($publication_id) || $use_null_values)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_PUBLICATION_ID),
                ! is_null($publication_id) ? new StaticConditionVariable($publication_id) : null);
        }

        return new AndCondition($conditions);
    }

    /**
     * Returns the select properties for a summary of the course visit tracker
     *
     * @return DataClassProperties
     */
    public static function get_course_visit_summary_select_properties()
    {
        $properties = new DataClassProperties();

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: MIN,
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_FIRST_ACCESS_DATE),
                CourseVisit :: PROPERTY_FIRST_ACCESS_DATE));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: MAX,
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_LAST_ACCESS_DATE),
                CourseVisit :: PROPERTY_LAST_ACCESS_DATE));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: SUM,
                new PropertyConditionVariable(
                    CourseVisit :: class_name(),
                    CourseVisit :: PROPERTY_TOTAL_NUMBER_OF_ACCESS),
                CourseVisit :: PROPERTY_TOTAL_NUMBER_OF_ACCESS));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: SUM,
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_TOTAL_TIME),
                CourseVisit :: PROPERTY_TOTAL_TIME));

        return $properties;
    }

    /**
     * Counts the courses that are accessed against a given timestamp, the operator is given to determine how to handle
     * the timestamp (before, after, equals...)
     *
     * @param int $timestamp
     *
     * @param $operator
     * @return int
     */
    public static function count_courses_with_last_access_against_time($timestamp, $operator)
    {
        $having = new InequalityCondition(
            new FunctionConditionVariable(
                FunctionConditionVariable :: MAX,
                new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_LAST_ACCESS_DATE)),
            $operator,
            new StaticConditionVariable($timestamp));

        $parameters = new DataClassCountGroupedParameters(
            null,
            new DataClassProperties(
                array(new PropertyConditionVariable(CourseVisit :: class_name(), CourseVisit :: PROPERTY_COURSE_ID))),
            $having);

        return self :: count_grouped(CourseVisit :: class_name(), $parameters);
    }

    /**
     * **************************************************************************************************************
     * AssessmentAttempt Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves all the assessment attempts joined with the user table
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_by
     *
     * @return RecordResultSet
     */
    public static function retrieve_assessment_attempts_with_user($condition = null, $offset = null, $count = null,
        $order_by = array())
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertiesConditionVariable(AssessmentAttempt :: class_name()));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));

        $parameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $count,
            $offset,
            $order_by,
            self :: get_assessment_attempts_user_joins());

        return self :: records(AssessmentAttempt :: class_name(), $parameters);
    }

    /**
     * Counts the assessment attempts with the given user
     *
     * @param Condition $condition
     *
     * @return int
     */
    public static function count_assessment_attempts_with_user($condition = null)
    {
        $parameters = new DataClassCountParameters($condition, self :: get_assessment_attempts_user_joins());
        return self :: count(AssessmentAttempt :: class_name(), $parameters);
    }

    /**
     * **************************************************************************************************************
     * AssessmentAttempt Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the joins for the assessment attempt with the user table
     *
     * @return Joins
     */
    public static function get_assessment_attempts_user_joins()
    {
        $join_conditions = array();

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt :: class_name(), AssessmentAttempt :: PROPERTY_USER_ID),
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID));

        $join_condition = new AndCondition($join_conditions);

        $joins = new Joins();
        $joins->add(new Join(User :: class_name(), $join_condition));

        return $joins;
    }
}