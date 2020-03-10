<?php
namespace Chamilo\Core\Rights\Editor\Ajax\Component;

use Chamilo\Core\Rights\Editor\Ajax\Manager;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @author Sven Vanpoucke
 * @package rights.ajax
 * @deprecated Should not be needed anymore
 */
class EntityRightLocationClassComponent extends Manager
{
    const PARAM_LOCATIONS = 'locations';
    const PARAM_RIGHTS = 'rights';
    const PROPERTY_NEW_CLASS = 'new_class';

    /**
     * Returns the required parameters
     * 
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_LOCATIONS, self::PARAM_RIGHTS);
    }

    /**
     * Executes this ajax request
     */
    public function run()
    {
        $locations = $this->getPostDataValue(self::PARAM_LOCATIONS);
        $locations = json_decode($locations);
        
        $rights = $this->getPostDataValue(self::PARAM_RIGHTS);
        $rights = explode('|', $rights);
        
        $context = $rights['1'];
        $right_id = $rights['2'];
        $entity_type = $rights['3'];
        $entity_item_id = $rights['4'];
        
        $rights_util = RightsUtil::getInstance();
        
        if (isset($context) && isset($right_id) && isset($entity_type) && isset($entity_item_id) && isset($locations) &&
             count($locations) > 0)
        {
            $context_dm = ($context . '\DataManager');
            $context_class = ($context . '\RightsLocation');
            
            $first_location_id = $locations[0];
            $first_location = $context_dm::retrieve_by_id($context_class::class_name(), $first_location_id);
            
            $value = $rights_util->is_allowed_for_rights_entity_item_no_inherit(
                $context, 
                $entity_type, 
                $entity_item_id, 
                $right_id, 
                $first_location_id);
            
            if (! $value)
            {
                if ($first_location->inherits())
                {
                    $inherited_value = $rights_util->is_allowed_for_rights_entity_item(
                        $context, 
                        $entity_type, 
                        $entity_item_id, 
                        $right_id, 
                        $first_location);
                    
                    $new_class = $inherited_value ? 'rightInheritTrue' : 'rightFalse';
                }
                else
                {
                    $new_class = 'rightFalse';
                }
            }
            else
            {
                $new_class = 'rightTrue';
            }
            
            $result = new JsonAjaxResult();
            $result->set_property(self::PROPERTY_NEW_CLASS, $new_class);
            $result->display();
        }
        else
        {
            JsonAjaxResult::bad_request();
        }
    }
}