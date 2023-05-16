<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataClass;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingRelation;

/**
 * This class describes the relation between a course and a course setting
 *
 * @package application\weblcms\course;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CourseRelCourseSetting extends CourseSettingRelation
{
    public const CONTEXT = Manager::CONTEXT;

    public const FOREIGN_PROPERTY_COURSE = 'course';

    public const PROPERTY_COURSE_ID = 'course_id';
    public const PROPERTY_OBJECT_ID = self::PROPERTY_COURSE_ID;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the default properties of this dataclass
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_ID;

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
        return 'weblcms_course_rel_course_setting';
    }

    /**
     * Returns the course of this course user relation object
     *
     * @return \application\weblcms\course\Course
     */
    public function get_course()
    {
        return $this->getForeignProperty(self::FOREIGN_PROPERTY_COURSE, Course::class);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * Returns the course_id of this CourseRelSetting object
     *
     * @return String
     */
    public function get_course_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_ID);
    }

    /**
     * Sets the course of this course user relation object
     *
     * @param $course \application\weblcms\course\Course
     */
    public function set_course(Course $course)
    {
        $this->setForeignProperty(self::FOREIGN_PROPERTY_COURSE, $course);
    }

    /**
     * Sets the course_id of this CourseRelSetting object
     *
     * @param $course_id String
     */
    public function set_course_id($course_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_ID, $course_id);
    }
}
