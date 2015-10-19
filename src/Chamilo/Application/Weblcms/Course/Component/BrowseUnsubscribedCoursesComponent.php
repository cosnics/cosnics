<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Application\Weblcms\Course\Table\UnsubscribedCourse\UnsubscribedCourseTable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;

/**
 * This class describes a browser for the courses where a user is not subscribed to
 *
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseUnsubscribedCoursesComponent extends BrowseSubscriptionCoursesComponent
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the condition for the table
     *
     * @param $object_table_class_name
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($object_table_class_name)
    {
        $conditions = array();

        $parent_condition = parent :: get_table_condition($object_table_class_name);
        if ($parent_condition)
        {
            $conditions[] = $parent_condition;
        }

        $user = $this->get_user();

        $userConditions = array();
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($user->get_id()));
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation :: ENTITY_TYPE_USER));

        $conditions[] = new NotCondition(
            new SubselectCondition(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID),
                new PropertyConditionVariable(
                    CourseEntityRelation :: class_name(),
                    CourseEntityRelation :: PROPERTY_COURSE_ID),
                CourseEntityRelation :: get_table_name(),
                new AndCondition($userConditions),
                Course :: get_table_name()));

        $groups = $user->get_groups(true);
        if ($groups)
        {
            $groups_condition = new InCondition(
                new PropertyConditionVariable(
                    CourseGroupRelation :: class_name(),
                    CourseGroupRelation :: PROPERTY_GROUP_ID),
                $user->get_groups(true),
                CourseGroupRelation :: get_table_name());

            $conditions[] = new NotCondition(
                new SubselectCondition(
                    new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID),
                    new PropertyConditionVariable(
                        CourseGroupRelation :: class_name(),
                        CourseGroupRelation :: PROPERTY_COURSE_ID),
                    CourseGroupRelation :: get_table_name(),
                    $groups_condition,
                    Course :: get_table_name()));
        }

        return new AndCondition($conditions);
    }

    /**
     * Returns the course table for this component
     *
     * @return CourseTable
     */
    protected function get_course_table()
    {
        return new UnsubscribedCourseTable($this);
    }
}
