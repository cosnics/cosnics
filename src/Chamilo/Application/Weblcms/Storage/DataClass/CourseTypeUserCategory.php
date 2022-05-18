<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.course_type
 */
class CourseTypeUserCategory extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    const PROPERTY_COURSE_USER_CATEGORY_ID = 'course_user_category_id';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_USER_ID = 'user_id';

    public function __construct($default_properties = [], $optional_properties = [])
    {
        parent::__construct($default_properties = $optional_properties);
        $this->addListener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Compares two instances of CourseTypeUserCategory
     *
     * @param $cata type
     * @param $catb type
     *
     * @return int 0 for equal, 1 for not equal
     */
    public static function compare($cata, $catb)
    {
        if (($cata->get_user_id() == $catb->get_user_id()) &&
            ($cata->get_course_user_category_id() == $catb->get_course_user_category_id()) &&
            ($cata->get_course_type_id() == $catb->get_course_type_id()))
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }

    /**
     * Deletes the dependencies
     */
    protected function deleteDependencies(): bool
    {
        $success = parent::deleteDependencies();

        $course_user_category_id_condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_COURSE_USER_CATEGORY_ID),
            new StaticConditionVariable($this->get_course_user_category_id())
        );
        // the link to this course_type shouldn't be counted as it will be deleted after the dependencies are deleted
        $filter_out_this_course_type_id_condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(self::class, self::PROPERTY_COURSE_TYPE_ID),
                new StaticConditionVariable($this->get_course_type_id())
            )
        );

        $condition = new AndCondition($course_user_category_id_condition, $filter_out_this_course_type_id_condition);

        $count = DataManager::count(self::class, new DataClassCountParameters($condition));
        // if there are no more links to a course type, then the course user category object itself may be deleted
        if ($count == 0)
        {
            $course_user_category = DataManager::retrieve_by_id(
                CourseUserCategory::class, $this->get_course_user_category_id()
            );

            if (!$course_user_category->delete())
            {
                $success = false;
            }
        }

        return $success;
    }

    public function get_course_type_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_TYPE_ID);
    }

    public function get_course_user_category_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_USER_CATEGORY_ID);
    }

    /**
     * Get the default properties of all user course user categories.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return array(
            self::PROPERTY_ID,
            self::PROPERTY_USER_ID,
            self::PROPERTY_COURSE_USER_CATEGORY_ID,
            self::PROPERTY_COURSE_TYPE_ID,
            self::PROPERTY_SORT
        );
    }

    protected function getDependencies(array $dependencies = []): array
    {
        return array(
            CourseTypeUserCategoryRelCourse::class => new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse::class,
                    CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID
                ), new StaticConditionVariable($this->get_id())
            )
        );
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(
            new PropertyConditionVariable(self::class, self::PROPERTY_COURSE_TYPE_ID),
            new PropertyConditionVariable(self::class, self::PROPERTY_USER_ID)
        );
    }

    /**
     * Returns the property for the display order
     *
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class, self::PROPERTY_SORT);
    }

    public function get_sort()
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_course_type_user_category';
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_course_type_id($course_type_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    public function set_course_user_category_id($course_user_category_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_USER_CATEGORY_ID, $course_user_category_id);
    }

    public function set_sort($sort)
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
