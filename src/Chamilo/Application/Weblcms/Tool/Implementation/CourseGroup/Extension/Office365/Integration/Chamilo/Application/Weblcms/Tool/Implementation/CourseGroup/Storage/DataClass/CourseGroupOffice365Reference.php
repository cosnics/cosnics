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
    const PROPERTY_OFFICE365_GROUP_ID = 'office365_group_id';
    const PROPERTY_OFFICE365_HAS_TEAM = 'office365_has_team';
    const PROPERTY_OFFICE365_PLAN_ID = 'office365_plan_id';
    const PROPERTY_LINKED = 'linked';

    /**
     * @param array $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_COURSE_GROUP_ID;
        $extended_property_names[] = self::PROPERTY_OFFICE365_HAS_TEAM;
        $extended_property_names[] = self::PROPERTY_OFFICE365_GROUP_ID;
        $extended_property_names[] = self::PROPERTY_OFFICE365_PLAN_ID;
        $extended_property_names[] = self::PROPERTY_LINKED;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * @return int
     */
    public function getCourseGroupId()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_GROUP_ID);
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
     * @return string
     */
    public function getOffice365GroupId()
    {
        return $this->get_default_property(self::PROPERTY_OFFICE365_GROUP_ID);
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
     * @return string
     */
    public static function get_table_name()
    {
        return 'weblcms_course_group_office365_reference';
    }

}
