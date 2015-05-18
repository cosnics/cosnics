<?php
namespace Chamilo\Core\Repository\Share\Component;

use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Deletes shares for a specific user / group on a specific content object
 * 
 * @author Sven Vanpoucke
 */
class DeleteComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $failures = 0;
        $content_object_ids = $this->get_content_object_ids();
        
        $user_ids = Request :: get(self :: PARAM_TARGET_USERS);
        if (! is_null($user_ids) && ! is_array($user_ids))
        {
            $user_ids = array($user_ids);
        }
        
        $group_ids = Request :: get(self :: PARAM_TARGET_GROUPS);
        if (! is_null($group_ids) && ! is_array($group_ids))
        {
            $group_ids = array($group_ids);
        }
        
        foreach ($content_object_ids as $content_object_id)
        {
            $location = $this->get_current_user_tree_location(Session :: get_user_id(), $content_object_id);
            
            if ($user_ids) // delete the shares with selected users
            {
                foreach ($user_ids as $user_id)
                {
                    
                    $isDeleted = RepositoryRights :: get_instance()->clear_share_entity_rights(
                        $location, 
                        UserEntity :: ENTITY_TYPE, 
                        $user_id);
                    if (! $isDeleted)
                        $failures ++;
                }
            }
            else 
                if ($group_ids) // delete the shares with selected groups
                {
                    foreach ($group_ids as $group_id)
                    {
                        $isDeleted = RepositoryRights :: get_instance()->clear_share_entity_rights(
                            $location, 
                            PlatformGroupEntity :: ENTITY_TYPE, 
                            $group_id);
                        if (! $isDeleted)
                            $failures ++;
                    }
                }
        }
        
        if ($failures > 0)
        {
            $message = Translation :: get(
                'ObjectsNotDeleted', 
                array('OBJECTS' => Translation :: get('SharedContentObjects')), 
                Utilities :: COMMON_LIBRARIES);
        }
        else
        {
            $message = Translation :: get(
                'ObjectsDeleted', 
                array('OBJECTS' => Translation :: get('SharedContentObjects')), 
                Utilities :: COMMON_LIBRARIES);
        }
        
        $parameters = $this->get_parameters();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE;
        
        $this->redirect($message, ($failures ? true : false), $parameters);
    }

    public function get_additional_parameters()
    {
        $parameters[] = self :: PARAM_TARGET_USERS;
        $parameters[] = self :: PARAM_TARGET_GROUPS;
        
        return $parameters;
    }
}
