<?php
namespace Chamilo\Application\Weblcms\CourseType\Storage\DataClass;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingRelation;

/**
 * This class describes the relation between a course type and a course setting
 *
 * @package application\weblcms\course_type;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CourseTypeRelCourseSetting extends CourseSettingRelation
{
    public const CONTEXT = Manager::CONTEXT;

    public const FOREIGN_PROPERTY_COURSE_TYPE = 'course_type';

    public const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    public const PROPERTY_DEFAULT_VALUE = 'default_value';
    public const PROPERTY_LIMITED = 'limited';
    public const PROPERTY_LOCKED = 'locked';
    public const PROPERTY_OBJECT_ID = self::PROPERTY_COURSE_TYPE_ID;

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
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_TYPE_ID;
        $extendedPropertyNames[] = self::PROPERTY_LOCKED;
        $extendedPropertyNames[] = self::PROPERTY_DEFAULT_VALUE;
        $extendedPropertyNames[] = self::PROPERTY_LIMITED;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_type_rel_course_setting';
    }

    /**
     * Returns the course type of this CourseTypeRelSetting object (lazy loading)
     *
     * @return CourseType
     */
    public function get_course_type()
    {
        return $this->getForeignProperty(self::FOREIGN_PROPERTY_COURSE_TYPE, CourseType::class);
    }

    /**
     * Returns the course_type_id of this CourseTypeRelSetting object
     *
     * @return String
     */
    public function get_course_type_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_TYPE_ID);
    }

    /**
     * Returns the default_value of this CourseTypeRelCourseSetting object
     *
     * @return String
     */
    public function is_default_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_DEFAULT_VALUE);
    }

    /**
     * Returns the limited of this CourseTypeRelCourseSetting object
     *
     * @return String
     */
    public function is_limited()
    {
        return $this->get_limited_property(self::PROPERTY_LIMITED);
    }

    /**
     * Returns the locked of this CourseTypeRelSetting object
     *
     * @return String
     */
    public function is_locked()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCKED);
    }

    /**
     * Sets the course type of this CourseTypeRelSetting object
     *
     * @param $course_type CourseType
     */
    public function set_course_type(CourseType $course_type)
    {
        $this->setForeignProperty(self::FOREIGN_PROPERTY_COURSE_TYPE, $course_type);
    }

    /**
     * Sets the course_type_id of this CourseTypeRelSetting object
     *
     * @param $course_type_id String
     */
    public function set_course_type_id($course_type_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * Sets the default_value of this CourseTypeRelCourseSetting object
     *
     * @param $default_value String
     */
    public function set_default_value($default_value)
    {
        $this->setDefaultProperty(self::PROPERTY_DEFAULT_VALUE, $default_value);
    }

    /**
     * Sets the limited of this CourseTypeRelCourseSetting object
     *
     * @param $limited String
     */
    public function set_limited($limited)
    {
        $this->set_limited_property(self::PROPERTY_LIMITED, $limited);
    }

    /**
     * Sets the locked of this CourseTypeRelSetting object
     *
     * @param $locked String
     */
    public function set_locked($locked)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCKED, $locked);
    }
}
