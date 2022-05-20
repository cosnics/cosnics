<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseCategoryEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Course\Component\BrowseComponent;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class CourseCategoryEntityHelper
{
    const PROPERTY_PATH = 'path';
    const PROPERTY_COURSE_CATEGORY_ID = 'course_category_id';

    public static function get_table_columns()
    {
        $columns = [];
        $columns[] = new DataClassPropertyTableColumn(CourseCategory::class, CourseCategory::PROPERTY_NAME);
        $columns[] = new StaticTableColumn(self::PROPERTY_PATH);
        $columns[] = new DataClassPropertyTableColumn(CourseCategory::class, CourseCategory::PROPERTY_CODE);
        return $columns;
    }

    public static function render_table_cell($renderer, $column, $result)
    {
        switch ($column->get_name())
        {
            case CourseCategory::PROPERTY_NAME :
                $url = self::get_target_url($renderer, $result);
                return '<a href="' . $url . '">' . $result[CourseCategory::PROPERTY_NAME] . '</a>';
                break;
            case self::PROPERTY_PATH :
                $course_category = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                    CourseCategory::class,
                    $result[self::PROPERTY_COURSE_CATEGORY_ID]);
                return $course_category->get_fully_qualified_name();
            default :
                return null;
        }

        return null;
    }

    public static function get_target_url($renderer, $result)
    {
        return $renderer->get_component()->get_url(
            array(
                \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_MANAGER,
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_BROWSE,
                BrowseComponent::PARAM_CATEGORY_ID => $result[self::PROPERTY_COURSE_CATEGORY_ID]),
            array(
                Manager::PARAM_ACTION,
                Manager::PARAM_TARGET_TYPE,
                Manager::PARAM_ENTITY_TYPE,
                Manager::PARAM_ENTITY_ID));
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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_table_data($condition, $count, $offset, $order_property)
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ORIGIN));
        $properties->add(
            new PropertyConditionVariable(
                CourseCategory::class,
                CourseCategory::PROPERTY_NAME));
        $properties->add(
            new PropertyConditionVariable(
                CourseCategory::class,
                CourseCategory::PROPERTY_CODE));
        $properties->add(
            new PropertyConditionVariable(
                CourseCategory::class,
                CourseCategory::PROPERTY_ID,
                self::PROPERTY_COURSE_CATEGORY_ID));

        $parameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $count,
            $offset,
            $order_property,
            self::get_joins());

        return DataManager::records(Admin::class, $parameters);
    }

    /**
     * Counts the data
     *
     * @param \libraries\Condition $condition
     *
     * @return int
     */
    public function count_table_data($condition)
    {
        $parameters = new DataClassCountParameters(
            $condition,
            self::get_joins(),
            new DataClassProperties(
                array(
                    new FunctionConditionVariable(
                        FunctionConditionVariable::DISTINCT,
                        new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ID)))));

        return DataManager::count(Admin::class, $parameters);
    }

    private static function get_joins()
    {
        $join = new Join(
            CourseCategory::class,
            new EqualityCondition(
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_TARGET_ID),
                new PropertyConditionVariable(
                    CourseCategory::class,
                    CourseCategory::PROPERTY_ID)));
        return new Joins(array($join));
    }

    public static function expand($entity_id)
    {
        $entities = [];

        $course_category = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            CourseCategory::class,
            $entity_id);

        if ($course_category instanceof CourseCategory)
        {
            $entities[CourseCategoryEntity::ENTITY_TYPE][] = $course_category->get_id();

            $parent_course_category_ids = $course_category->get_parent_ids();

            foreach ($parent_course_category_ids as $parent_course_category_id)
            {
                $entities[CourseCategoryEntity::ENTITY_TYPE][] = $parent_course_category_id;
            }
        }

        return $entities;
    }

    public static function get_course_ids($entity_id)
    {
        $course_category = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            CourseCategory::class,
            $entity_id);

        if ($course_category instanceof CourseCategory)
        {
            $course_category_ids = $course_category->get_children_ids();
            $course_category_ids[] = $course_category->get_id();

            $condition = new InCondition(
                new PropertyConditionVariable(Course::class, Course::PROPERTY_CATEGORY_ID),
                $course_category_ids);

            $parameters = new DataClassDistinctParameters(
                $condition,
                new DataClassProperties(array(new PropertyConditionVariable(Course::class, Course::PROPERTY_ID))));

            return \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(Course::class, $parameters);
        }
        else
        {
            return [];
        }
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
