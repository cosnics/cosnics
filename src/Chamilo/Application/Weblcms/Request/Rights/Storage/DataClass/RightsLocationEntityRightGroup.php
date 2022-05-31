<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Storage\DataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @author Hans De Bisschop
 */
class RightsLocationEntityRightGroup extends DataClass
{

    const PROPERTY_GROUP_ID = 'group_id';
    const PROPERTY_LOCATION_ENTITY_RIGHT_ID = 'location_entity_right_id';

    /**
     * The group of the RightsLocationEntityRightGroup
     *
     * @var \group\Group
     */
    private $group;

    /**
     *
     * @var RightsLocationEntityRightGroup
     */
    private $location_entity_right;

    /**
     * Get the default properties
     *
     * @param $extendedPropertyNames string[]
     *
     * @return string[] The property names.
     */
    static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_LOCATION_ENTITY_RIGHT_ID;
        $extendedPropertyNames[] = self::PROPERTY_GROUP_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    function get_group()
    {
        if (!isset($this->group))
        {
            $this->group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                Group::class, $this->get_group_id()
            );
        }

        return $this->group;
    }

    function get_group_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }

    function get_location_entity_right()
    {
        if (!isset($this->location_entity_right))
        {
            $this->location_entity_right =
                \Chamilo\Core\Rights\Storage\DataManager::retrieve_rights_location_entity_right_by_id(
                    Manager::context(), $this->get_location_entity_right_id()
                );
        }

        return $this->location_entity_right;
    }

    function get_location_entity_right_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_request_rights_location_entity_right_group';
    }

    function set_group_id($group_id)
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $group_id);
    }

    function set_location_entity_right_id($location_entity_right_id)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID, $location_entity_right_id);
    }
}
