<?php
namespace Chamilo\Core\Rights\Editor\Component;

use Chamilo\Core\Rights\Editor\Manager;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Sven Vanpoucke
 * @package application.common.rights_editor_manager.component
 */
class EntityRightsSetterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $entity_item_id = Request::get(self::PARAM_ENTITY_ID);
        $right_id = Request::get(self::PARAM_RIGHT_ID);
        
        $entity_type = $this->get_selected_entity()->get_entity_type();
        
        $this->set_parameter(self::PARAM_ENTITY_ID, $entity_item_id);
        $this->set_parameter(self::PARAM_RIGHT_ID, $right_id);
        $this->set_parameter(self::PARAM_ENTITY_TYPE, $entity_type);
        
        $context = $this->get_context();
        $locations = $this->get_locations();
        
        $rights_util = RightsUtil::getInstance();
        
        if (isset($entity_item_id) && isset($entity_type) && isset($right_id) && isset($locations) &&
             count($locations) > 0)
        {
            foreach ($locations as $location)
            {
                $success = $rights_util->invert_location_entity_right(
                    $context, 
                    $right_id, 
                    $entity_item_id, 
                    $entity_type, 
                    $location->get_id());
            }
            
            $this->redirect(
                Translation::get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), 
                ! $success, 
                array_merge(
                    $this->get_parameters(), 
                    array(
                        self::PARAM_ACTION => self::ACTION_EDIT_ADVANCED_RIGHTS, 
                        self::PARAM_ENTITY_ID => null, 
                        self::PARAM_RIGHT_ID => null)));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectsSelected', null, Utilities::COMMON_LIBRARIES)));
        }
    }
}
