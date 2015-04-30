<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataClass;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingRelation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes the relation between a course and a course setting
 * 
 * @package application\weblcms\course;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseRelCourseSetting extends CourseSettingRelation
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_ID = 'course_id';
    
    /**
     * **************************************************************************************************************
     * Foreign properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_COURSE = 'course';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the default properties of this dataclass
     * 
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_COURSE_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * Get the dependencies for this object
     * 
     * @return boolean
     */
    protected function get_dependencies()
    {
        return array(
            CourseRelCourseSettingValue :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(
                    CourseRelCourseSettingValue :: class_name(), 
                    CourseRelCourseSettingValue :: PROPERTY_COURSE_REL_COURSE_SETTING_ID), 
                new StaticConditionVariable($this->get_id())));
    }

    /**
     * **************************************************************************************************************
     * Course Settings Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Adds a value for this course rel course setting object
     * 
     * @param $value string
     *
     * @return CourseRelCourseSettingValue
     */
    public function add_course_setting_value($value)
    {
        $course_rel_setting_value = new CourseRelCourseSettingValue();
        $course_rel_setting_value->set_course_rel_course_setting($this);
        $course_rel_setting_value->set_value($value);
        
        if (! $course_rel_setting_value->create())
        {
            throw new \Exception(Translation :: get('CouldNotCreateCourseRelCourseSettingValue'));
        }
        
        return $course_rel_setting_value;
    }

    /**
     * Truncates the values for this given course rel course setting object
     * 
     * @return boolean
     */
    public function truncate_values()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseRelCourseSettingValue :: class_name(), 
                CourseRelCourseSettingValue :: PROPERTY_COURSE_REL_COURSE_SETTING_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return DataManager :: deletes(CourseRelCourseSettingValue :: class_name(), $condition);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course_id of this CourseRelSetting object
     * 
     * @return String
     */
    public function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    /**
     * Sets the course_id of this CourseRelSetting object
     * 
     * @param $course_id String
     */
    public function set_course_id($course_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course of this course user relation object
     * 
     * @return \application\weblcms\course\Course
     */
    public function get_course()
    {
        return $this->get_foreign_property(self :: FOREIGN_PROPERTY_COURSE);
    }

    /**
     * Sets the course of this course user relation object
     * 
     * @param $course \application\weblcms\course\Course
     */
    public function set_course(\Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course)
    {
        $this->set_foreign_property(self :: FOREIGN_PROPERTY_COURSE, $course);
    }
}
