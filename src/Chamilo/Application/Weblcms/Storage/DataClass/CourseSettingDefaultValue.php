<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

/**
 * A class that describes a default course_setting_id for a course setting
 *
 * @package application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSettingDefaultValue extends CourseSettingValue
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_SETTING_ID = 'course_setting_id';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the course_setting_id of this CourseSettingDefaultValue object
     *
     * @return String
     */
    function get_course_setting_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_SETTING_ID);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    static function get_default_property_names($extended_property_names = [])
    {
        $extended_property_names[] = self::PROPERTY_COURSE_SETTING_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_course_setting_default_value';
    }

    /**
     * Sets the course_setting_id of this CourseSettingDefaultValue object
     *
     * @param $course_setting_id String
     */
    function set_course_setting_id($course_setting_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_SETTING_ID, $course_setting_id);
    }
}