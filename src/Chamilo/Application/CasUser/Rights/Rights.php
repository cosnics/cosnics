<?php
namespace Chamilo\Application\CasUser\Rights;

use Chamilo\Application\CasUser\Rights\Storage\DataClass\LocationEntityRightGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\RightsLocationEntityRight;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Rights extends RightsUtil
{
    const VIEW_RIGHT = '1';

    private static $instance;

    private static $target_users;

    /**
     *
     * @return \application\cas_user\rights\Rights
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    public static function get_available_rights()
    {
        return array(Translation :: get('ViewRight') => self :: VIEW_RIGHT);
    }

    public function cas_is_allowed()
    {
        $entities = array();
        $entities[UserEntity :: ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity :: ENTITY_TYPE] = new PlatformGroupEntity();
        
        return parent :: is_allowed(
            self :: VIEW_RIGHT, 
            __NAMESPACE__, 
            null, 
            $entities, 
            0, 
            self :: TYPE_ROOT, 
            0, 
            self :: TREE_TYPE_ROOT);
    }

    public function get_cas_view_rights_location_entity_right($entity_id, $entity_type)
    {
        return parent :: get_rights_location_entity_right(
            __NAMESPACE__, 
            self :: VIEW_RIGHT, 
            $entity_id, 
            $entity_type, 
            self :: get_cas_root_id());
    }

    public function invert_cas_location_entity_right($right_id, $entity_id, $entity_type)
    {
        return parent :: invert_location_entity_right(
            __NAMESPACE__, 
            $right_id, 
            $entity_id, 
            $entity_type, 
            self :: get_cas_root_id());
    }

    public function get_cas_targets_entities()
    {
        return parent :: get_target_entities(self :: VIEW_RIGHT, __NAMESPACE__);
    }

    public function get_cas_root()
    {
        return parent :: get_root(__NAMESPACE__);
    }

    public function get_cas_root_id()
    {
        return parent :: get_root_id(__NAMESPACE__);
    }

    public function create_cas_root()
    {
        return parent :: create_location(__NAMESPACE__);
    }

    public function get_cas_location_entity_right($entity_id, $entity_type)
    {
        return \Chamilo\Core\Rights\Storage\DataManager :: retrieve_rights_location_entity_right(
            __NAMESPACE__, 
            self :: VIEW_RIGHT, 
            $entity_id, 
            $entity_type, 
            $this->get_cas_root_id());
    }

    public function get_target_users(\Chamilo\Core\User\Storage\DataClass\User $user)
    {
        if (! isset(self :: $target_users[$user->get_id()]))
        {
            $allowed_groups = array();
            
            $location_entity_right = $this->get_cas_location_entity_right($user->get_id(), UserEntity :: ENTITY_TYPE);
            if ($location_entity_right instanceof RightsLocationEntityRight)
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        LocationEntityRightGroup :: class_name(), 
                        LocationEntityRightGroup :: PROPERTY_LOCATION_ENTITY_RIGHT_ID), 
                    new StaticConditionVariable($location_entity_right->get_id()));
                $right_groups = \Chamilo\Core\Rights\Storage\DataManager :: retrieves(
                    LocationEntityRightGroup :: class_name(), 
                    $condition);
                
                while ($right_group = $right_groups->next_result())
                {
                    if (! in_array($right_group->get_group_id(), $allowed_groups))
                    {
                        $allowed_groups[] = $right_group->get_group_id();
                    }
                }
            }
            
            $user_group_ids = $user->get_groups(true);
            
            foreach ($user_group_ids as $user_group_id)
            {
                $location_entity_right = $this->get_cas_location_entity_right(
                    $user_group_id, 
                    PlatformGroupEntity :: ENTITY_TYPE);
                if ($location_entity_right instanceof RightsLocationEntityRight)
                {
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            LocationEntityRightGroup :: class_name(), 
                            LocationEntityRightGroup :: PROPERTY_LOCATION_ENTITY_RIGHT_ID), 
                        new StaticConditionVariable($location_entity_right->get_id()));
                    $right_groups = \Chamilo\Core\Rights\Storage\DataManager :: retrieves(
                        LocationEntityRightGroup :: class_name(), 
                        $condition);
                    
                    while ($right_group = $right_groups->next_result())
                    {
                        if (! in_array($right_group->get_group_id(), $allowed_groups))
                        {
                            $allowed_groups[] = $right_group->get_group_id();
                        }
                    }
                }
            }
            
            self :: $target_users[$user->get_id()] = array();
            
            if (count($allowed_groups) > 0)
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID), 
                    $allowed_groups);
                $groups = \Chamilo\Core\Group\Storage\DataManager :: retrieves(Group :: class_name, $condition);
                
                while ($group = $groups->next_result())
                {
                    $user_ids = $group->get_users(true, true);
                    
                    foreach ($user_ids as $user_id)
                    {
                        if (! in_array($user_id, self :: $target_users[$user->get_id()]))
                        {
                            self :: $target_users[$user->get_id()][] = $user_id;
                        }
                    }
                }
            }
        }
        
        return self :: $target_users[$user->get_id()];
    }

    public function is_target_user(\Chamilo\Core\User\Storage\DataClass\User $user, $target_user_id)
    {
        return in_array($target_user_id, $this->get_target_users($user));
    }
}
