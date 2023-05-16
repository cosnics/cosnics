<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract class that describes a relation with a course setting
 *
 * @package application\weblcms\course_type;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class CourseSettingRelation extends DataClass
{
    public const ALIAS_OBJECT_ID = 'object_id';

    public const CONTEXT = Manager::CONTEXT;

    public const FOREIGN_PROPERTY_COURSE_SETTING = 'course_setting';

    public const PROPERTY_COURSE_SETTING_ID = 'course_setting_id';
    public const PROPERTY_VALUE = 'value';

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
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_SETTING_ID;
        $extendedPropertyNames[] = self::PROPERTY_VALUE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * Returns the course setting of this CourseSettingRelation object (lazy
     * loading)
     *
     * @return CourseSetting
     */
    public function get_course_setting()
    {
        return DataManager::retrieve_by_id(CourseSetting::class, $this->get_course_setting_id());
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
    public function get_course_setting_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_SETTING_ID);
    }

    /**
     * Returns the value of this CourseSettingValue object
     *
     * @return String
     */
    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    /**
     * Sets the course setting of this CourseSettingRelation object
     *
     * @param $course_setting CourseSetting
     */
    public function set_course_setting(CourseSetting $course_setting)
    {
        $this->setForeignProperty(self::FOREIGN_PROPERTY_COURSE_SETTING, $course_setting);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * Sets the course_setting_id of this CourseSettingRelation object
     *
     * @param $course_setting_id String
     */
    public function set_course_setting_id($course_setting_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_SETTING_ID, $course_setting_id);
    }

    /**
     * Sets the value of this CourseSettingValue object
     *
     * @param $value String
     */
    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }

    /**
     * Truncates the values for this given course type rel course setting object If this course type rel course setting
     * object is locked than all the values for the courses connected to this course type are deleted.
     *
     * @return bool
     */
    public function truncate_values()
    {
        $this->set_value(null);

        return $this->update();
    }
}