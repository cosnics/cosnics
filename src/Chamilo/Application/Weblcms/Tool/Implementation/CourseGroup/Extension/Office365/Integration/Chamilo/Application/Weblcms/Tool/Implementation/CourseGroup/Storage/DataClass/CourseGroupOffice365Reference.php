<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365Reference extends DataClass
{
    const PROPERTY_COURSE_GROUP_ID = 'course_group_id';
    const PROPERTY_LINKED = 'linked';
    const PROPERTY_OFFICE365_GROUP_ID = 'office365_group_id';
    const PROPERTY_OFFICE365_HAS_TEAM = 'office365_has_team';
    const PROPERTY_OFFICE365_PLAN_ID = 'office365_plan_id';

    /**
     * @return int
     */
    public function getCourseGroupId()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_GROUP_ID);
    }

    /**
     * @return string
     */
    public function getOffice365GroupId()
    {
        return $this->get_default_property(self::PROPERTY_OFFICE365_GROUP_ID);
    }

    /**
     * @return string
     */
    public function getOffice365PlanId()
    {
        return $this->get_default_property(self::PROPERTY_OFFICE365_PLAN_ID);
    }

    /**
     * @param array $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
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
    public static function getTableName(): string
    {
        return 'weblcms_course_group_office365_reference';
    }

    /**
     *
     */
    public function hasTeam(): bool
    {
        return !empty($this->get_default_property(self::PROPERTY_OFFICE365_HAS_TEAM));
    }

    /**
     * Returns whether or not the reference to the office365 is still active. Used to deactivate the connection
     * without loosing the reference information
     *
     * @return bool
     */
    public function isLinked()
    {
        return (bool) $this->get_default_property(self::PROPERTY_LINKED);
    }

    /**
     * @param int $courseGroupId
     *
     * @return CourseGroupOffice365Reference
     */
    public function setCourseGroupId($courseGroupId)
    {
        $this->set_default_property(self::PROPERTY_COURSE_GROUP_ID, $courseGroupId);

        return $this;
    }

    /**
     * @param bool $hasTeam
     */
    public function setHasTeam(bool $hasTeam)
    {
        $this->set_default_property(self::PROPERTY_OFFICE365_HAS_TEAM, $hasTeam);
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
        $this->set_default_property(self::PROPERTY_LINKED, $linked);

        return $this;
    }

    /**
     * @param string $office365GroupId
     *
     * @return CourseGroupOffice365Reference
     */
    public function setOffice365GroupId($office365GroupId)
    {
        $this->set_default_property(self::PROPERTY_OFFICE365_GROUP_ID, $office365GroupId);

        return $this;
    }

    /**
     * @param string $office365PlanId
     *
     * @return CourseGroupOffice365Reference
     */
    public function setOffice365PlanId($office365PlanId)
    {
        $this->set_default_property(self::PROPERTY_OFFICE365_PLAN_ID, $office365PlanId);

        return $this;
    }

}