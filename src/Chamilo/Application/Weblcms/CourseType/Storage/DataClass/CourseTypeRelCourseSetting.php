<?php
namespace Chamilo\Application\Weblcms\CourseType\Storage\DataClass;

use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingRelation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

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

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Course Settings Functionality *
     * **************************************************************************************************************
     */

    /**
     * Adds a value for this course type rel course setting object
     *
     * @param $value string
     *
     * @return CourseTypeRelCourseSettingValue
     */
    public function add_course_setting_value($value)
    {
        $course_type_rel_setting_value = new CourseTypeRelCourseSettingValue();
        $course_type_rel_setting_value->set_course_type_rel_course_setting($this);
        $course_type_rel_setting_value->set_value($value);

        if (! $course_type_rel_setting_value->create())
        {
            throw new \Exception(Translation :: get('CouldNotCreateCourseTypeRelCourseSettingValue'));
        }

        return $course_type_rel_setting_value;
    }

    /**
     * Truncates the values for this given course type rel course setting object If this course type rel course setting
     * object is locked than all the values for the courses connected to this course type are deleted.
     *
     * @return boolean
     */
    public function truncate_values()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeRelCourseSettingValue :: class_name(),
                CourseTypeRelCourseSettingValue :: PROPERTY_COURSE_TYPE_REL_COURSE_SETTING_ID),
            new StaticConditionVariable($this->get_id()));

        return DataManager :: deletes(CourseTypeRelCourseSettingValue :: class_name(), $condition);
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
