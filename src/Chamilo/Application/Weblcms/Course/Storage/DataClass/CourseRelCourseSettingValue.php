<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingValue;

/**
 * This class describes a value for the relation between a course and a course
 * setting
 *
 * @package application\weblcms\course;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseRelCourseSettingValue extends CourseSettingValue
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_REL_COURSE_SETTING_ID = 'course_rel_course_setting_id';

    /**
     * **************************************************************************************************************
     * Foreign Properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_COURSE_REL_COURSE_SETTING = 'course_rel_course_setting';

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
        $extended_property_names[] = self :: PROPERTY_COURSE_REL_COURSE_SETTING_ID;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the course_rel_course_setting_id of this
     * CourseRelCourseSettingValue object
     *
     * @return String
     */
    public function get_course_rel_course_setting_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_REL_COURSE_SETTING_ID);
    }

    /**
     * Sets the course_rel_course_setting_id of this CourseRelCourseSettingValue
     * object
     *
     * @param $course_rel_course_setting_id String
     */
    public function set_course_rel_course_setting_id($course_rel_course_setting_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_REL_COURSE_SETTING_ID, $course_rel_course_setting_id);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * Returns the course_rel_course_setting of this CourseRelCourseSettingValue
     * object (lazy loading)
     *
     * @return CourseRelSetting
     */
    public function get_course_rel_course_setting()
    {
        return $this->get_foreign_property(
            self :: FOREIGN_PROPERTY_COURSE_REL_COURSE_SETTING,
            CourseRelCourseSetting :: class_name());
    }

    /**
     * Sets the course_rel_course_setting of this CourseRelCourseSettingValue
     * object
     *
     * @param $course_rel_course_setting CourseCourseRelCourseSettingRelSetting
     */
    public function set_course_rel_course_setting(CourseRelCourseSetting $course_rel_course_setting)
    {
        $this->set_foreign_property(self :: FOREIGN_PROPERTY_COURSE_REL_COURSE_SETTING, $course_rel_course_setting);
    }
}
