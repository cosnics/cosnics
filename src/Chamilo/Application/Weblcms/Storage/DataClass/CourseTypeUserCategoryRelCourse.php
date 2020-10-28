<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.course_type
 */
class CourseTypeUserCategoryRelCourse extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_COURSE_TYPE_USER_CATEGORY_ID = 'course_type_user_category_id';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_USER_ID = 'user_id';

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent::__construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    public function get_course_type_user_category_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID);
    }

    /**
     * Get the default properties of all user course user categories.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return array(
            self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID,
            self::PROPERTY_COURSE_ID,
            self::PROPERTY_USER_ID,
            self::PROPERTY_SORT
        );
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_display_order_context_properties()
    {
        return array(
            new PropertyConditionVariable(self::class, self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID),
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
        return $this->get_default_property(self::PROPERTY_SORT);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'weblcms_course_type_user_category_rel_course';
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    public function set_course_type_user_category_id($course_type_user_category_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_id);
    }

    public function set_sort($sort)
    {
        $this->set_default_property(self::PROPERTY_SORT, $sort);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }
}
