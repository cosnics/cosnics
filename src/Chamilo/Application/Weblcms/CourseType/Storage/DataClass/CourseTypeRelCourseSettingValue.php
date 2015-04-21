<?php
namespace Chamilo\Application\Weblcms\CourseType\Storage\DataClass;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingValue;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a value for the relation between a course type and a
 * course setting
 * 
 * @package application\weblcms\course_type;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseTypeRelCourseSettingValue extends CourseSettingValue
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_TYPE_REL_COURSE_SETTING_ID = 'course_type_rel_course_setting_id';
    const PROPERTY_DEFAULT_VALUE = 'default_value';
    const PROPERTY_LIMITED = 'limited';
    
    /**
     * **************************************************************************************************************
     * Foreign Properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_COURSE_TYPE_REL_COURSE_SETTING = 'course_type_rel_course_setting';

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
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_COURSE_TYPE_REL_COURSE_SETTING_ID;
        $extended_property_names[] = self :: PROPERTY_DEFAULT_VALUE;
        $extended_property_names[] = self :: PROPERTY_LIMITED;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * CRUD Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Creates the value for the given course setting rel course type
     * If the course type rel course setting is locked and the course setting is
     * a copy setting than this value needs
     * to be pushed to all the courses that are connected to the course type
     * 
     * @return boolean
     */
    public function create()
    {
        if (! parent :: create())
        {
            return false;
        }
        
        $course_type_rel_course_setting = $this->get_course_type_rel_course_setting();
        if ($course_type_rel_course_setting->is_locked())
        {
            CourseSettingsController :: get_instance()->clear_cache_for_type_and_object(
                CourseSettingsController :: SETTING_TYPE_COURSE_TYPE, 
                $course_type_rel_course_setting->get_course_type_id());
            
            \Chamilo\Application\Weblcms\Course\Storage\DataManager :: copy_course_settings_from_course_type(
                $course_type_rel_course_setting->get_course_type_id(), 
                $course_type_rel_course_setting->get_course_setting_id());
            
            $course_setting = $course_type_rel_course_setting->get_course_setting();
            
            $course_property = CourseSettingsConnector :: get_course_property_for_setting($course_setting);
            if (! is_null($course_property))
            {
                $course_type = $course_type_rel_course_setting->get_course_type();
                
                $properties = new DataClassProperties();
                
                $properties->add(
                    new DataClassProperty(
                        new PropertyConditionVariable(Course :: class_name(), $course_property), 
                        new StaticConditionVariable($this->get_value())));
                
                return \Chamilo\Application\Weblcms\Course\Storage\DataManager :: update_courses_from_course_type_with_properties(
                    $course_type->get_id(), 
                    $properties);
            }
        }
        
        return true;
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course_type_rel_course_setting_id of this
     * CourseTypeRelCourseSettingValue object
     * 
     * @return String
     */
    public function get_course_type_rel_course_setting_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_REL_COURSE_SETTING_ID);
    }

    /**
     * Sets the course_type_rel_course_setting_id of this
     * CourseTypeRelCourseSettingValue object
     * 
     * @param $course_type_rel_course_setting_id String
     */
    public function set_course_type_rel_course_setting_id($course_type_rel_course_setting_id)
    {
        $this->set_default_property(
            self :: PROPERTY_COURSE_TYPE_REL_COURSE_SETTING_ID, 
            $course_type_rel_course_setting_id);
    }

    /**
     * Returns the default_value of this CourseTypeRelCourseSettingValue object
     * 
     * @return String
     */
    public function is_default_value()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_VALUE);
    }

    /**
     * Sets the default_value of this CourseTypeRelCourseSettingValue object
     * 
     * @param $default_value String
     */
    public function set_default_value($default_value)
    {
        $this->set_default_property(self :: PROPERTY_DEFAULT_VALUE, $default_value);
    }

    /**
     * Returns the limited of this CourseTypeRelCourseSettingValue object
     * 
     * @return String
     */
    public function is_limited()
    {
        return $this->get_limited_property(self :: PROPERTY_LIMITED);
    }

    /**
     * Sets the limited of this CourseTypeRelCourseSettingValue object
     * 
     * @param $limited String
     */
    public function set_limited($limited)
    {
        $this->set_limited_property(self :: PROPERTY_LIMITED, $limited);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course_type_rel_course_setting of this
     * CourseTypeRelCourseSettingValue object (lazy loading)
     * 
     * @return CourseTypeRelCourseSetting
     */
    public function get_course_type_rel_course_setting()
    {
        return $this->get_foreign_property(self :: FOREIGN_PROPERTY_COURSE_TYPE_REL_COURSE_SETTING);
    }

    /**
     * Sets the course_type_rel_course_setting of this
     * CourseTypeRelCourseSettingValue object
     * 
     * @param $course_type_rel_course_setting CourseTypeRelCourseSetting
     */
    public function set_course_type_rel_course_setting(CourseTypeRelCourseSetting $course_type_rel_course_setting)
    {
        $this->set_foreign_property(
            self :: FOREIGN_PROPERTY_COURSE_TYPE_REL_COURSE_SETTING, 
            $course_type_rel_course_setting);
    }
}
