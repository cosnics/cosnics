<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseCategoryEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class CourseEntityHelper
{
    const PROPERTY_PATH = 'path';
    const PROPERTY_COURSE_ID = 'course_id';

    public static function get_table_columns()
    {
        $columns = array();
        $columns[] = new DataClassPropertyTableColumn(Course :: class_name(), Course :: PROPERTY_TITLE);
        $columns[] = new StaticTableColumn(self :: PROPERTY_PATH);
        $columns[] = new DataClassPropertyTableColumn(Course :: class_name(), Course :: PROPERTY_VISUAL_CODE);
        return $columns;
    }

    public static function render_table_cell($renderer, $column, $result)
    {
        switch ($column->get_name())
        {
            case Course :: PROPERTY_TITLE :
                $url = self :: get_target_url($renderer, $result);
                return '<a href="' . $url . '">' . $result[Course :: PROPERTY_TITLE] . '</a>';
                break;
            case self :: PROPERTY_PATH :
                $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_by_id(
                    Course :: class_name(),
                    $result[self :: PROPERTY_COURSE_ID]);
                return $course->get_fully_qualified_name();
            default :
                return null;
        }

        return null;
    }

    public static function get_target_url($renderer, $result)
    {
        return $renderer->get_component()->get_url(
            array(
                \Chamilo\Application\Weblcms\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE,
                \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE => $result[self :: PROPERTY_COURSE_ID]),
            array(
                Manager :: PARAM_ACTION,
                Manager :: PARAM_TARGET_TYPE,
                Manager :: PARAM_ENTITY_TYPE,
                Manager :: PARAM_ENTITY_ID));
    }

    /**
     * Returns the data as a resultset
     *
     * @param \libraries\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \libraries\ResultSet
     */
    public static function retrieve_table_data($condition, $count, $offset, $order_property)
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ORIGIN));
        $properties->add(new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_TITLE));
        $properties->add(new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_VISUAL_CODE));
        $properties->add(
            new FixedPropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID, self :: PROPERTY_COURSE_ID));

        $parameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $count,
            $offset,
            $order_property,
            self :: get_joins());

        return DataManager :: records(Admin :: class_name(), $parameters);
    }

    /**
     * Counts the data
     *
     * @param \libraries\Condition $condition
     *
     * @return int
     */
    public static function count_table_data($condition)
    {
        $parameters = new DataClassCountParameters(
            $condition,
            self :: get_joins(),
            new FunctionConditionVariable(
                FunctionConditionVariable :: DISTINCT,
                new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ID)));

        return DataManager :: count(Admin :: class_name(), $parameters);
    }

    private static function get_joins()
    {
        $join = new Join(
            Course :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_TARGET_ID),
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID)));
        return new Joins(array($join));
    }

    public static function expand($entity_id)
    {
        $entities = array();

        $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: class_name(),
            $entity_id);

        if ($course instanceof \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course)
        {
            $entities[CourseEntity :: ENTITY_TYPE][] = $course->get_id();

            $course_category = $course->get_category();

            if ($course_category instanceof CourseCategory)
            {
                $entities[CourseCategoryEntity :: ENTITY_TYPE][] = $course_category->get_id();

                $parent_course_category_ids = $course_category->get_parent_ids();

                foreach ($parent_course_category_ids as $parent_course_category_id)
                {
                    $entities[CourseCategoryEntity :: ENTITY_TYPE][] = $parent_course_category_id;
                }
            }
        }

        return $entities;
    }

    public static function get_course_ids($entity_id)
    {
        return array($entity_id);
    }

    /**
     * Get the fully qualified class name of the object
     *
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }
}
