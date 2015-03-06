<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 *
 * @package repository.lib
 */
/**
 *
 * @author Sven Vanpoucke
 */
class ContentObjectGroupShare extends ContentObjectShare
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_GROUP_ID = 'group_id';
    const TYPE_GROUP_SHARE = 'group';

    public function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    public function get_group()
    {
        return \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(Group :: class_name(), $this->get_group_id());
    }

    public function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    /**
     * Get the default properties of all groups.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_GROUP_ID));
    }
}
