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
        return $this->getDefaultProperty(self::PROPERTY_COURSE_SETTING_ID);
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
    static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_SETTING_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
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
        $this->setDefaultProperty(self::PROPERTY_COURSE_SETTING_ID, $course_setting_id);
    }
}