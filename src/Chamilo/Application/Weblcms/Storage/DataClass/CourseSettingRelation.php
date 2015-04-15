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
        $extended_property_names[] = self :: PROPERTY_COURSE_SETTING_ID;
        
        return parent :: get_default_property_names($extended_property_names);
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
        return $this->get_default_property(self :: PROPERTY_COURSE_SETTING_ID);
    }

    /**
     * Sets the course_setting_id of this CourseSettingRelation object
     * 
     * @param $course_setting_id String
     */
    function set_course_setting_id($course_setting_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_SETTING_ID, $course_setting_id);
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
        return DataManager :: retrieve_by_id(CourseSetting :: class_name(), $this->get_course_setting_id());
    }

    /**
     * Sets the course setting of this CourseSettingRelation object
     * 
     * @param $course_setting CourseSetting
     */
    function set_course_setting(CourseSetting $course_setting)
    {
        $this->set_foreign_property(self :: FOREIGN_PROPERTY_COURSE_SETTING, $course_setting);
    }
}

?>