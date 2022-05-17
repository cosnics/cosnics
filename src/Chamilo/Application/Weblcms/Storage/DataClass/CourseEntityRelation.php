<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents a relation between a weblcms course and an entity (a
 * user, a group...)
 *
 * @package application\weblcms\course;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseEntityRelation extends DataClass
{
    const ENTITY_TYPE_GROUP = 2;
    const ENTITY_TYPE_ROLE = 3;
    const ENTITY_TYPE_USER = 1;

    const FOREIGN_PROPERTY_COURSE = 'course';

    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_STATUS = 'status';

    const STATUS_STUDENT = 5;
    const STATUS_TEACHER = 1;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    public function getEntityId()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    public function getEntityType()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

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
     * Returns the course id of this course user relation object
     *
     * @return int
     */
    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_ID;
        $extendedPropertyNames[] = self::PROPERTY_STATUS;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * Returns the status of this course user relation object
     *
     * @return int
     */
    public function get_status()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    public static function getTableName(): string
    {
        return 'weblcms_course_entity_relation';
    }

    public function setEntityId($entityId)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entityId);
    }

    public function setEntityType($entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
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
     * Sets the course id of this course user relation object
     *
     * @param $course_id int
     */
    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * Sets the status of this course user relation object
     *
     * @param $status int
     */
    public function set_status($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
    }
}
