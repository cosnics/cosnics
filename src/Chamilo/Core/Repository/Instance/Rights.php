<?php
namespace Chamilo\Core\Repository\Instance;

use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\RightsUtil;

/**
 * $Id: repository_rights.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib
 */
class Rights extends RightsUtil
{
    const USE_RIGHT = 1;
    const TYPE_EXTERNAL_INSTANCE = 1;

    private static $instance;

    /**
     *
     * @return \core\repository\instance\Rights
     */
    public static function getInstance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    public static function get_available_rights_for_external_instances_substree()
    {
        return array('Use' => self :: USE_RIGHT);
    }

    // External Instances subtree
    public function create_location_in_external_instances_subtree($identifier, $parent)
    {
        return parent :: create_location(__NAMESPACE__, self :: TYPE_EXTERNAL_INSTANCE, $identifier, 0, $parent);
    }

    public function get_external_instances_subtree_root()
    {
        return parent :: get_root(__NAMESPACE__);
    }

    public function get_external_instances_subtree_root_id()
    {
        return parent :: get_root_id(__NAMESPACE__);
    }

    public function get_location_id_by_identifier_from_external_instances_subtree($identifier)
    {
        return parent :: get_location_id_by_identifier(__NAMESPACE__, self :: TYPE_EXTERNAL_INSTANCE, $identifier);
    }

    public function get_location_by_identifier_from_external_instances_subtree($identifier)
    {
        return parent :: get_location_by_identifier(__NAMESPACE__, self :: TYPE_EXTERNAL_INSTANCE, $identifier);
    }

    public function is_allowed_in_external_instances_subtree($right, $external_instance_id)
    {
        $entities = array();
        $entities[] = new UserEntity();
        $entities[] = new PlatformGroupEntity();

        return parent :: is_allowed(
            $right,
            __NAMESPACE__,
            null,
            $entities,
            $external_instance_id,
            self :: TYPE_EXTERNAL_INSTANCE);
    }

    public function create_external_instances_subtree_root_location()
    {
        return parent :: create_subtree_root_location(__NAMESPACE__);
    }

    public function invert_repository_location_entity_right($right_id, $entity_id, $entity_type, $location_id)
    {
        return parent :: invert_location_entity_right(__NAMESPACE__, $right_id, $entity_id, $entity_type, $location_id);
    }

    public function set_location_entity_right($right_id, $entity_id, $entity_type, $location_id)
    {
        return parent :: set_location_entity_right(__NAMESPACE__, $right_id, $entity_id, $entity_type, $location_id);
    }
}
