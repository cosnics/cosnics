<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract class that describes a relation with a course setting
 * 
 * @package application\weblcms\course_type;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CourseSettingRelation extends DataClass
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_SETTING_ID = 'course_setting_id';
    const PROPERTY_VALUE = 'value';
    const ALIAS_OBJECT_ID = 'object_id';
    
    /**
     * **************************************************************************************************************
     * Foreign properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_COURSE_SETTING = 'course_setting';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the default properties of this dataclass
     * 
     * @return String[] - The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_COURSE_SETTING_ID;
        $extended_property_names[] = self::PROPERTY_VALUE;
        
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * Adds a value for this course type rel course setting object
     * 
     * @param $value string
     *
     * @return CourseTypeRelCourseSetting
     */
    public function add_course_setting_value($value)
    {
        $this->set_value($value);
        return $this->update();
    }

    /**
     * Truncates the values for this given course type rel course setting object If this course type rel course setting
     * object is locked than all the values for the courses connected to this course type are deleted.
     * 
     * @return boolean
     */
    public function truncate_values()
    {
        $this->set_value(null);
        return $this->update();
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course_setting_id of this CourseSettingRelation object
     * 
     * @return String
     */
    function get_course_setting_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_SETTING_ID);
    }

    /**
     * Sets the course_setting_id of this CourseSettingRelation object
     * 
     * @param $course_setting_id String
     */
    function set_course_setting_id($course_setting_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_SETTING_ID, $course_setting_id);
    }

    /**
     * Returns the value of this CourseSettingValue object
     * 
     * @return String
     */
    function get_value()
    {
        return $this->get_default_property(self::PROPERTY_VALUE);
    }

    /**
     * Sets the value of this CourseSettingValue object
     * 
     * @param $value String
     */
    function set_value($value)
    {
        $this->set_default_property(self::PROPERTY_VALUE, $value);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course setting of this CourseSettingRelation object (lazy
     * loading)
     * 
     * @return CourseSetting
     */
    function get_course_setting()
    {
        return DataManager::retrieve_by_id(CourseSetting::class, $this->get_course_setting_id());
    }

    /**
     * Sets the course setting of this CourseSettingRelation object
     * 
     * @param $course_setting CourseSetting
     */
    function set_course_setting(CourseSetting $course_setting)
    {
        $this->set_foreign_property(self::FOREIGN_PROPERTY_COURSE_SETTING, $course_setting);
    }
}