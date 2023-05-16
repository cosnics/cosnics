<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365Reference extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_COURSE_GROUP_ID = 'course_group_id';
    public const PROPERTY_LINKED = 'linked';
    public const PROPERTY_OFFICE365_GROUP_ID = 'office365_group_id';
    public const PROPERTY_OFFICE365_HAS_TEAM = 'office365_has_team';
    public const PROPERTY_OFFICE365_PLAN_ID = 'office365_plan_id';

    /**
     * @return int
     */
    public function getCourseGroupId()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_GROUP_ID);
    }

    /**
     * @param array $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_GROUP_ID;
        $extendedPropertyNames[] = self::PROPERTY_OFFICE365_HAS_TEAM;
        $extendedPropertyNames[] = self::PROPERTY_OFFICE365_GROUP_ID;
        $extendedPropertyNames[] = self::PROPERTY_OFFICE365_PLAN_ID;
        $extendedPropertyNames[] = self::PROPERTY_LINKED;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public function getOffice365GroupId()
    {
        return $this->getDefaultProperty(self::PROPERTY_OFFICE365_GROUP_ID);
    }

    /**
     * @return string
     */
    public function getOffice365PlanId()
    {
        return $this->getDefaultProperty(self::PROPERTY_OFFICE365_PLAN_ID);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_group_office365_reference';
    }

    /**
     *
     */
    public function hasTeam(): bool
    {
        return !empty($this->getDefaultProperty(self::PROPERTY_OFFICE365_HAS_TEAM));
    }

    /**
     * Returns whether or not the reference to the office365 is still active. Used to deactivate the connection
     * without loosing the reference information
     *
     * @return bool
     */
    public function isLinked()
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_LINKED);
    }

    /**
     * @param int $courseGroupId
     *
     * @return CourseGroupOffice365Reference
     */
    public function setCourseGroupId($courseGroupId)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_GROUP_ID, $courseGroupId);

        return $this;
    }

    /**
     * @param bool $hasTeam
     */
    public function setHasTeam(bool $hasTeam)
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICE365_HAS_TEAM, $hasTeam);
    }

    /**
     * Stores the linked property. Used to deactivate the connection
     * without loosing the reference information
     *
     * @param bool $linked
     *
     * @return $this
     */
    public function setLinked($linked = true)
    {
        $this->setDefaultProperty(self::PROPERTY_LINKED, $linked);

        return $this;
    }

    /**
     * @param string $office365GroupId
     *
     * @return CourseGroupOffice365Reference
     */
    public function setOffice365GroupId($office365GroupId)
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICE365_GROUP_ID, $office365GroupId);

        return $this;
    }

    /**
     * @param string $office365PlanId
     *
     * @return CourseGroupOffice365Reference
     */
    public function setOffice365PlanId($office365PlanId)
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICE365_PLAN_ID, $office365PlanId);

        return $this;
    }

}