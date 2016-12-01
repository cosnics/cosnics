<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: publication_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete an object publication from the publication
 * overview.
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request::get(self::PARAM_PUBLICATION_ID);
        $application = Request::get(self::PARAM_PUBLICATION_APPLICATION);
        $this->set_parameter(self::PARAM_PUBLICATION_ID, $id);
        $this->set_parameter(self::PARAM_PUBLICATION_APPLICATION, $application);
        
        if (! empty($id) && ! empty($application))
        {
            $succes = DataManager::delete_content_object_publication($application, $id);
            
            if ($succes)
            {
                $message = Translation::get(
                    'ObjectDeleted', 
                    array('OBJECT' => Translation::get('Publication')), 
                    Utilities::COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation::get(
                    'ObjectNotDeleted', 
                    array('OBJECT' => Translation::get('Publication')), 
                    Utilities::COMMON_LIBRARIES);
            }
            
            $this->redirect($message, ! $succes, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES)));
        }
    }
}
