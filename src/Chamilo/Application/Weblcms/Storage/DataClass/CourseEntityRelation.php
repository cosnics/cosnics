<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents a relation between a weblcms course and an entity (a
 * user, a group...)
 *
 * @package application\weblcms\course;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CourseEntityRelation extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const ENTITY_TYPE_GROUP = 2;
    public const ENTITY_TYPE_ROLE = 3;
    public const ENTITY_TYPE_USER = 1;

    public const FOREIGN_PROPERTY_COURSE = 'course';

    public const PROPERTY_COURSE_ID = 'course_id';
    public const PROPERTY_ENTITY_ID = 'entity_id';
    public const PROPERTY_ENTITY_TYPE = 'entity_type';
    public const PROPERTY_STATUS = 'status';

    public const STATUS_STUDENT = 5;
    public const STATUS_TEACHER = 1;

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_ID;
        $extendedPropertyNames[] = self::PROPERTY_STATUS;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    public function getEntityId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    public function getEntityType()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_entity_relation';
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
     * Returns the course id of this course user relation object
     *
     * @return int
     */
    public function get_course_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_ID);
    }

    /**
     * Returns the status of this course user relation object
     *
     * @return int
     */
    public function get_status()
    {
        return $this->getDefaultProperty(self::PROPERTY_STATUS);
    }

    public function setEntityId($entityId)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entityId);
    }

    public function setEntityType($entityType)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entityType);
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
     * Sets the course id of this course user relation object
     *
     * @param $course_id int
     */
    public function set_course_id($course_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * Sets the status of this course user relation object
     *
     * @param $status int
     */
    public function set_status($status)
    {
        $this->setDefaultProperty(self::PROPERTY_STATUS, $status);
    }
}
