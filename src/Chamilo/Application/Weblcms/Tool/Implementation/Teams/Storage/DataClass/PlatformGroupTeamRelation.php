<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlatformGroupTeamRelation extends DataClass
{
    const PROPERTY_PLATFORM_GROUP_TEAM_ID = 'platform_group_team_id';
    const PROPERTY_GROUP_ID = 'group_id';

    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_PLATFORM_GROUP_TEAM_ID;
        $extendedPropertyNames[] = self::PROPERTY_GROUP_ID;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function getPlatformGroupTeamId()
    {
        return $this->get_default_property(self::PROPERTY_PLATFORM_GROUP_TEAM_ID);
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->get_default_property(self::PROPERTY_GROUP_ID);
    }

    /**
     * @param int $platformGroupTeamId
     *
     * @return $this
     */
    public function setPlatformGroupTeamId(int $platformGroupTeamId)
    {
        $this->set_default_property(self::PROPERTY_PLATFORM_GROUP_TEAM_ID, $platformGroupTeamId);
        return $this;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId(int $groupId)
    {
        $this->set_default_property(self::PROPERTY_GROUP_ID, $groupId);
        return $this;
    }
}