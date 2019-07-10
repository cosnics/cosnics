<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlatformGroupTeam extends DataClass
{
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_TEAM_ID = 'team_id';
    const PROPERTY_NAME = 'name';

    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_ID;
        $extendedPropertyNames[] = self::PROPERTY_TEAM_ID;
        $extendedPropertyNames[] = self::PROPERTY_NAME;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function getCourseId()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * @return string
     */
    public function getTeamId()
    {
        return $this->get_default_property(self::PROPERTY_TEAM_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * @param int $courseId
     *
     * @return $this
     */
    public function setCourseId(int $courseId)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $courseId);
        return $this;
    }

    /**
     * @param string $teamId
     *
     * @return $this
     */
    public function setTeamId(string $teamId)
    {
        $this->set_default_property(self::PROPERTY_TEAM_ID, $teamId);
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
        return $this;
    }
}