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
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\StorageParameters;
use Chamilo\Libraries\Translation\Translation;

class CourseEntityHelper
{
    public const PROPERTY_COURSE_ID = 'course_id';

    public const PROPERTY_PATH = 'path';

    /**
     * Get the fully qualified class name of the object
     *
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }

    /**
     * Counts the data
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public static function count_table_data($condition)
    {
        $parameters = new StorageParameters(
            condition: $condition, joins: self::get_joins(), retrieveProperties: new RetrieveProperties(
            [
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ID)
                )
            ]
        )
        );

        return DataManager::count(Admin::class, $parameters);
    }

    public static function expand($entity_id)
    {
        $entities = [];

        $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
            Course::class, $entity_id
        );

        if ($course instanceof Course)
        {
            $entities[CourseEntity::ENTITY_TYPE][] = $course->get_id();

            $course_category = $course->get_category();

            if ($course_category instanceof CourseCategory)
            {
                $entities[CourseCategoryEntity::ENTITY_TYPE][] = $course_category->get_id();

                $parent_course_category_ids = $course_category->get_parent_ids();

                foreach ($parent_course_category_ids as $parent_course_category_id)
                {
                    $entities[CourseCategoryEntity::ENTITY_TYPE][] = $parent_course_category_id;
                }
            }
        }

        return $entities;
    }

    public static function get_course_ids($entity_id)
    {
        return [$entity_id];
    }

    private static function get_joins()
    {
        $join = new Join(
            Course::class, new EqualityCondition(
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_TARGET_ID),
                new PropertyConditionVariable(Course::class, Course::PROPERTY_ID)
            )
        );

        return new Joins([$join]);
    }

    /**
     * @throws \Exception
     */
    public static function get_table_columns()
    {
        $translator = Translation::getInstance();

        $columns = [];
        $columns[] = new DataClassPropertyTableColumn(
            Course::class, Course::PROPERTY_TITLE,
            $translator->getTranslation('Title', [], \Chamilo\Application\Weblcms\Manager::CONTEXT)
        );
        $columns[] = new StaticTableColumn(
            self::PROPERTY_PATH, $translator->getTranslation('Path', [], \Chamilo\Application\Weblcms\Manager::CONTEXT)
        );
        $columns[] = new DataClassPropertyTableColumn(
            Course::class, Course::PROPERTY_VISUAL_CODE,
            $translator->getTranslation('VisualCode', [], \Chamilo\Application\Weblcms\Manager::CONTEXT)
        );

        return $columns;
    }

    public static function get_target_url($renderer, $result)
    {
        return $renderer->getUrlGenerator()->fromRequest(
            [
                \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $result[self::PROPERTY_COURSE_ID]
            ], [
                Manager::PARAM_ACTION,
                Manager::PARAM_TARGET_TYPE,
                Manager::PARAM_ENTITY_TYPE,
                Manager::PARAM_ENTITY_ID
            ]
        );
    }

    public static function render_table_cell($renderer, $column, $result)
    {
        switch ($column->get_name())
        {
            case Course::PROPERTY_TITLE :
                $url = self::get_target_url($renderer, $result);

                return '<a href="' . $url . '">' . $result[Course::PROPERTY_TITLE] . '</a>';
                break;
            case self::PROPERTY_PATH :
                $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
                    Course::class, $result[self::PROPERTY_COURSE_ID]
                );

                return $course->get_fully_qualified_name();
            default :
                return null;
        }

        return null;
    }

    /**
     * Returns the data as a resultset
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public static function retrieve_table_data($condition, $count, $offset, $order_property)
    {
        $properties = new RetrieveProperties();
        $properties->add(new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ORIGIN));
        $properties->add(new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE));
        $properties->add(new PropertyConditionVariable(Course::class, Course::PROPERTY_VISUAL_CODE));
        $properties->add(
            new PropertyConditionVariable(Course::class, Course::PROPERTY_ID, self::PROPERTY_COURSE_ID)
        );

        $parameters = new StorageParameters(
            condition: $condition, count: $count, offset: $offset, orderBy: $order_property, joins: self::get_joins(),
            retrieveProperties: $properties
        );

        return DataManager::records(Admin::class, $parameters);
    }
}
