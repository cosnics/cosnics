<?php
namespace Chamilo\Application\Weblcms\CourseType\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingRelation;

/**
 * This class describes the relation between a course type and a course setting
 *
 * @package application\weblcms\course_type;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseTypeRelCourseSetting extends CourseSettingRelation
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_DEFAULT_VALUE = 'default_value';
    const PROPERTY_LIMITED = 'limited';
    const PROPERTY_OBJECT_ID = self :: PROPERTY_COURSE_TYPE_ID;

    /**
     * **************************************************************************************************************
     * Foreign properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_COURSE_TYPE = 'course_type';

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
        $extended_property_names[] = self :: PROPERTY_COURSE_TYPE_ID;
        $extended_property_names[] = self :: PROPERTY_LOCKED;
        $extended_property_names[] = self :: PROPERTY_DEFAULT_VALUE;
        $extended_property_names[] = self :: PROPERTY_LIMITED;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the course_type_id of this CourseTypeRelSetting object
     *
     * @return String
     */
    public function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    /**
     * Sets the course_type_id of this CourseTypeRelSetting object
     *
     * @param $course_type_id String
     */
    public function set_course_type_id($course_type_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    /**
     * Returns the locked of this CourseTypeRelSetting object
     *
     * @return String
     */
    public function is_locked()
    {
        return $this->get_default_property(self :: PROPERTY_LOCKED);
    }

    /**
     * Sets the locked of this CourseTypeRelSetting object
     *
     * @param $locked String
     */
    public function set_locked($locked)
    {
        $this->set_default_property(self :: PROPERTY_LOCKED, $locked);
    }

    /**
     * Returns the default_value of this CourseTypeRelCourseSetting object
     *
     * @return String
     */
    public function is_default_value()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_VALUE);
    }

    /**
     * Sets the default_value of this CourseTypeRelCourseSetting object
     *
     * @param $default_value String
     */
    public function set_default_value($default_value)
    {
        $this->set_default_property(self :: PROPERTY_DEFAULT_VALUE, $default_value);
    }

    /**
     * Returns the limited of this CourseTypeRelCourseSetting object
     *
     * @return String
     */
    public function is_limited()
    {
        return $this->get_limited_property(self :: PROPERTY_LIMITED);
    }

    /**
     * Sets the limited of this CourseTypeRelCourseSetting object
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
     * Returns the course type of this CourseTypeRelSetting object (lazy loading)
     *
     * @return CourseType
     */
    public function get_course_type()
    {
        return $this->get_foreign_property(self :: FOREIGN_PROPERTY_COURSE_TYPE, CourseType :: class_name());
    }

    /**
     * Sets the course type of this CourseTypeRelSetting object
     *
     * @param $course_type CourseType
     */
    public function set_course_type(CourseType $course_type)
    {
        $this->set_foreign_property(self :: FOREIGN_PROPERTY_COURSE_TYPE, $course_type);
    }
}
