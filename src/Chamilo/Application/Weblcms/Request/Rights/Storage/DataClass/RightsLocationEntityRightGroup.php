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
     * Get the data class data manager
     *
     * @return \libraries\Datamanager
     */
    function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties
     *
     * @param $extended_property_names multitype:string
     *
     * @return multitype:string The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_LOCATION_ENTITY_RIGHT_ID;
        $extended_property_names[] = self::PROPERTY_GROUP_ID;

        return parent::get_default_property_names($extended_property_names);
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
        return $this->get_default_property(self::PROPERTY_GROUP_ID);
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
        return $this->get_default_property(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'weblcms_request_rights_location_entity_right_group';
    }

    function set_group_id($group_id)
    {
        $this->set_default_property(self::PROPERTY_GROUP_ID, $group_id);
    }

    function set_location_entity_right_id($location_entity_right_id)
    {
        $this->set_default_property(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID, $location_entity_right_id);
    }
}
