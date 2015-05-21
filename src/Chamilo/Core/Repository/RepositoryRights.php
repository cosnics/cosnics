<?php
namespace Chamilo\Core\Repository;

use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use ErrorException;
use Exception;
use ReflectionClass;

/**
 * $Id: repository_rights.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib
 */
class RepositoryRights extends RightsUtil
{
    const ADD_RIGHT = '1';
    const EDIT_RIGHT = '2';
    const DELETE_RIGHT = '3';
    const SEARCH_RIGHT = '4';
    const VIEW_RIGHT = '5';
    const USE_RIGHT = '6';
    const COLLABORATE_RIGHT = '7';
    const COPY_RIGHT = '8';
    
    // Tree that contains all the categories/content objects of a user
    const TREE_TYPE_USER = 1;
    const TREE_TYPE_CONTENT_OBJECT = 2;
    const TREE_TYPE_EXTERNAL_INSTANCE = 3;
    const TYPE_CONTENT_OBJECT = 1;
    const TYPE_USER_CATEGORY = 2;
    const TYPE_USER_CONTENT_OBJECT = 3;
    const TYPE_EXTERNAL_INSTANCE = 4;

    private static $instance;

    /**
     *
     * @return RepositoryRights
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    public function get_available_rights()
    {
        $reflect = new ReflectionClass('RepositoryRights');
        return $reflect->getConstants();
    }

    public static function get_available_rights_for_users_subtree()
    {
        return array(
            'Search' => self :: SEARCH_RIGHT, 
            'View' => self :: VIEW_RIGHT, 
            'Use' => self :: USE_RIGHT, 
            'Collaborate' => self :: COLLABORATE_RIGHT, 
            'Copy' => self :: COPY_RIGHT);
    }

    public static function get_share_rights()
    {
        $rights = array();
        
        if (! PlatformSetting :: get('all_objects_searchable', Manager :: context()))
        {
            $rights[self :: SEARCH_RIGHT] = Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES);
        }
        
        $rights[self :: VIEW_RIGHT] = Translation :: get('View', null, Utilities :: COMMON_LIBRARIES);
        $rights[self :: USE_RIGHT] = Translation :: get('Use', null, Utilities :: COMMON_LIBRARIES);
        $rights[self :: COLLABORATE_RIGHT] = Translation :: get('Collaborate', null, Utilities :: COMMON_LIBRARIES);
        
        return $rights;
    }

    public static function get_copy_right()
    {
        return $rights[self :: COPY_RIGHT] = Translation :: get('Copy', null, Utilities :: COMMON_LIBRARIES);
    }

    public static function get_available_rights_for_content_object_subtree()
    {
        return array('View' => self :: VIEW_RIGHT, 'Add' => self :: ADD_RIGHT);
    }

    public static function get_available_rights_for_external_instances_substree()
    {
        return array('Use' => self :: USE_RIGHT);
    }

    public function is_allowed($right, $user_id, $entities, $identifier, $type)
    {
        try
        {
            return parent :: is_allowed(
                $right, 
                __NAMESPACE__, 
                $user_id, 
                $entities, 
                $identifier, 
                $type, 
                $tree_type = self :: TREE_TYPE_ROOT);
        }
        catch (ErrorException $exception)
        {
            $exception->getMessage();
            return false;
        }
    }

    public function get_repository_location_by_identifier($type, $identifier, $tree_identifier = '0', $tree_type = 0)
    {
        return parent :: get_location_by_identifier(__NAMESPACE__, $type, $identifier, $tree_identifier, $tree_type);
    }

    public function get_repository_location_id_by_identifier($type, $identifier, $tree_identifier = '0', $tree_type = 0)
    {
        return parent :: get_location_id_by_identifier(__NAMESPACE__, $type, $identifier, $tree_identifier, $tree_type);
    }
    
    // User Subtree
    public function create_user_root($user_id)
    {
        return parent :: create_subtree_root_location(__NAMESPACE__, $user_id, self :: TREE_TYPE_USER);
    }

    public function create_location_in_user_tree($type, $identifier, $parent, $user_id, $create_in_batch = false)
    {
        return parent :: create_location(
            __NAMESPACE__, 
            $type, 
            $identifier, 
            1, 
            $parent, 
            0, 
            $user_id, 
            self :: TREE_TYPE_USER, 
            true, 
            $create_in_batch);
    }

    public function get_user_root_id($user_id)
    {
        return parent :: get_root_id(__NAMESPACE__, self :: TREE_TYPE_USER, $user_id);
    }

    public function get_user_root($user_id)
    {
        return parent :: get_root(__NAMESPACE__, self :: TREE_TYPE_USER, $user_id);
    }

    public function get_location_id_by_identifier_from_user_subtree($type, $identifier, $user_id)
    {
        return parent :: get_location_id_by_identifier(
            __NAMESPACE__, 
            $type, 
            $identifier, 
            $user_id, 
            self :: TREE_TYPE_USER);
    }

    public function get_location_by_identifier_from_users_subtree($type, $identifier, $user_id)
    {
        return parent :: get_location_by_identifier(__NAMESPACE__, $type, $identifier, $user_id, self :: TREE_TYPE_USER);
    }

    /**
     * Handles the share rights
     * 
     * @param type $right
     * @param type $identifier
     * @param type $type
     * @param type $user_tree_identifier
     * @return bool
     */
    public function is_allowed_in_user_subtree($right, $identifier, $type, $user_tree_identifier, $user_id = null)
    {
        $entities = array();
        $entities[] = new UserEntity();
        $entities[] = new PlatformGroupEntity();
        
        // all rights except copy right are cumulative
        // extra right checks give no query overhead because all rights for a location/entities are cached
        if ($right == RepositoryRights :: COPY_RIGHT)
        {
            try
            {
                return parent :: is_allowed(
                    $right, 
                    __NAMESPACE__, 
                    $user_id, 
                    $entities, 
                    $identifier, 
                    $type, 
                    $user_tree_identifier, 
                    self :: TREE_TYPE_USER);
            }
            catch (ErrorException $exception)
            {
                $exception->getMessage();
                return false;
            }
        }
        else
        {
            $current_right = RepositoryRights :: COLLABORATE_RIGHT;
            $is_allowed = false;
            while ($current_right >= $right && ! $is_allowed)
            {
                try
                {
                    $is_allowed = parent :: is_allowed(
                        $current_right, 
                        __NAMESPACE__, 
                        $user_id, 
                        $entities, 
                        $identifier, 
                        $type, 
                        $user_tree_identifier, 
                        self :: TREE_TYPE_USER);
                }
                catch (Exception $exception)
                {
                    $exception->getMessage();
                    $is_allowed = false;
                }
                $current_right --;
            }
            return $is_allowed;
        }
    }
    
    // External Instances subtree
    public function create_location_in_external_instances_subtree($identifier, $parent)
    {
        return parent :: create_location(
            __NAMESPACE__, 
            self :: TYPE_EXTERNAL_INSTANCE, 
            $identifier, 
            0, 
            $parent, 
            0, 
            0, 
            self :: TREE_TYPE_EXTERNAL_INSTANCE);
    }

    public function get_external_instances_subtree_root()
    {
        return parent :: get_root(__NAMESPACE__, self :: TREE_TYPE_EXTERNAL_INSTANCE);
    }

    public function get_external_instances_subtree_root_id()
    {
        return parent :: get_root_id(__NAMESPACE__, self :: TREE_TYPE_EXTERNAL_INSTANCE);
    }

    public function get_location_id_by_identifier_from_external_instances_subtree($identifier)
    {
        return parent :: get_location_id_by_identifier(
            __NAMESPACE__, 
            self :: TYPE_EXTERNAL_INSTANCE, 
            $identifier, 
            0, 
            self :: TREE_TYPE_EXTERNAL_INSTANCE);
    }

    public function get_location_by_identifier_from_external_instances_subtree($identifier)
    {
        return parent :: get_location_by_identifier(
            __NAMESPACE__, 
            self :: TYPE_EXTERNAL_INSTANCE, 
            $identifier, 
            0, 
            self :: TREE_TYPE_EXTERNAL_INSTANCE);
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
            self :: TYPE_EXTERNAL_INSTANCE, 
            0, 
            self :: TREE_TYPE_EXTERNAL_INSTANCE);
    }

    public function create_external_instances_subtree_root_location()
    {
        return parent :: create_subtree_root_location(__NAMESPACE__, 0, self :: TREE_TYPE_EXTERNAL_INSTANCE);
    }

    public function invert_repository_location_entity_right($right_id, $entity_id, $entity_type, $location_id)
    {
        return parent :: invert_location_entity_right(__NAMESPACE__, $right_id, $entity_id, $entity_type, $location_id);
    }

    public function set_location_entity_right($right_id, $entity_id, $entity_type, $location_id)
    {
        return parent :: set_location_entity_right(__NAMESPACE__, $right_id, $entity_id, $entity_type, $location_id);
    }

    public function get_share_target_entities_overview($identifier, $type, $user_id)
    {
        return parent :: get_target_entities(null, __NAMESPACE__, $identifier, $type, $user_id, self :: TREE_TYPE_USER);
    }

    /**
     * Removes the rights a user/group is given on a location
     */
    public function clear_share_entity_rights($location, $entity_type, $entity_id)
    {
        return \Chamilo\Core\Rights\Storage\DataManager :: delete_rights_location_entity_rights(
            $location, 
            $entity_type, 
            $entity_id);
    }
}
