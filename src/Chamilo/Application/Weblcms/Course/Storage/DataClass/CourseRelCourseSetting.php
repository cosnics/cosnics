<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingRelation;

/**
 * This class describes the relation between a course and a course setting
 *
 * @package application\weblcms\course;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseRelCourseSetting extends CourseSettingRelation
{
    const FOREIGN_PROPERTY_COURSE = 'course';

    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_OBJECT_ID = self::PROPERTY_COURSE_ID;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the course of this course user relation object
     *
     * @return \application\weblcms\course\Course
     */
    public function get_course()
    {
        return $this->get_foreign_property(self::FOREIGN_PROPERTY_COURSE, Course::class);
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
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * Returns the default properties of this dataclass
     *
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        $extended_property_names[] = self::PROPERTY_COURSE_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_course_rel_course_setting';
    }

    /**
     * Sets the course of this course user relation object
     *
     * @param $course \application\weblcms\course\Course
     */
    public function set_course(Course $course)
    {
        $this->set_foreign_property(self::FOREIGN_PROPERTY_COURSE, $course);
    }

    /**
     * Sets the course_id of this CourseRelSetting object
     *
     * @param $course_id String
     */
    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }
}
