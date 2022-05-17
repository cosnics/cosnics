<?php
namespace Chamilo\Core\Repository\Quota\Rights\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocationEntityRightGroup extends DataClass
{

    const PROPERTY_GROUP_ID = 'group_id';

    const PROPERTY_LOCATION_ENTITY_RIGHT_ID = 'location_entity_right_id';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = [])
    {
        $extended_property_names[] = self::PROPERTY_LOCATION_ENTITY_RIGHT_ID;
        $extended_property_names[] = self::PROPERTY_GROUP_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * @return integer
     */
    public function get_group_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }

    /**
     * @return integer
     */
    public function get_location_entity_right_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_quote_rights_location_entity_right_group';
    }

    /**
     * @param integer $groupId
     *
     * @throws \Exception
     */
    public function set_group_id($groupId)
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $groupId);
    }

    /**
     * @param integer $locationEntityRightId
     *
     * @throws \Exception
     */
    public function set_location_entity_right_id($locationEntityRightId)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID, $locationEntityRightId);
    }
}
