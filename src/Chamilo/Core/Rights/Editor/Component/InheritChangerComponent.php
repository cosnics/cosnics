<?php
namespace Chamilo\Core\Rights\Editor\Component;

use Chamilo\Core\Rights\Editor\Manager;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Sven Vanpoucke
 * @package rights.lib.location_manager.component
 */
class InheritChangerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $locations = $this->get_locations();
        $failures = 0;
        
        if (! empty($locations))
        {
            
            foreach ($locations as $location)
            {
                $location->switch_inherit();
                
                if (! $location->update())
                {
                    $failures ++;
                }
            }
            
            $message = $this->get_result(
                $failures, 
                count($locations), 
                'SelectedLocationNotInheriting', 
                'SelectedLocationsNotInheriting', 
                'SelectedLocationInheriting', 
                'SelectedLocationsInheriting');
            
            $this->redirect(
                $message, 
                ($failures ? true : false), 
                array(self::PARAM_ACTION => self::ACTION_EDIT_ADVANCED_RIGHTS));
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation::get('NoLocationSelected')));
        }
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_ENTITY_TYPE);
    }
}
