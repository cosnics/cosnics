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
 * $Id: course_type_user_category.class.php 216 2009-11-13 14:08:06Z Tristan $
 * 
 * @package application.lib.weblcms.course_type
 */
class CourseTypeUserCategoryRelCourse extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_COURSE_TYPE_USER_CATEGORY_ID = 'course_type_user_category_id';
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SORT = 'sort';

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent::__construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
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
            self::PROPERTY_SORT);
    }

    public function get_course_type_user_category_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID);
    }

    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function get_sort()
    {
        return $this->get_default_property(self::PROPERTY_SORT);
    }

    public function set_course_type_user_category_id($course_type_user_category_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_id);
    }

    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function set_sort($sort)
    {
        $this->set_default_property(self::PROPERTY_SORT, $sort);
    }

    public function update()
    {
        $this->notify(DataClassListener::BEFORE_UPDATE);
        $success = DataManager::getInstance()->update($this, $this->get_primary_key_conditions());
        $this->notify(DataClassListener::AFTER_UPDATE, array($success));
        
        return $success;
    }

    public function delete()
    {
        $this->notify(DataClassListener::BEFORE_DELETE);
        $success = DataManager::deletes(self::class_name(), $this->get_primary_key_conditions());
        $this->notify(DataClassListener::AFTER_DELETE, array($success));
        
        return $success;
    }

    /**
     * Returns the primary key condition
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_primary_key_conditions()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategoryRelCourse::class_name(), 
                CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($this->get_course_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategoryRelCourse::class_name(), 
                CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID), 
            new StaticConditionVariable($this->get_course_type_user_category_id()));
        
        return new AndCondition($conditions);
    }

    /**
     * Returns the property for the display order
     * 
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class_name(), self::PROPERTY_SORT);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID), 
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_USER_ID));
    }
}
