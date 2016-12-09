<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request::get(self::PARAM_PUBLICATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $publication = DataManager::retrieve_by_id(Publication::class_name(), $id);
                
                if (! $this->get_user()->is_platform_admin() && $publication->get_publisher() != $this->get_user_id())
                {
                    throw new NotAllowedException();
                }
                
                if (! $publication->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectNotDeleted', 
                        array('OBJECT' => Translation::get('Publication')), 
                        Utilities::COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsNotDeleted', 
                        array('OBJECT' => Translation::get('Publications')), 
                        Utilities::COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectDeleted', 
                        array('OBJECT' => Translation::get('Publication')), 
                        Utilities::COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsDeleted', 
                        array('OBJECT' => Translation::get('Publications')), 
                        Utilities::COMMON_LIBRARIES);
                }
            }
            
            $this->redirect(
                $message, 
                ($failures ? true : false), 
                array(
                    \Chamilo\Application\Calendar\Manager::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::context(), 
                    \Chamilo\Application\Calendar\Manager::PARAM_ACTION => \Chamilo\Application\Calendar\Manager::ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectsSelected', null, Utilities::COMMON_LIBRARIES)));
        }
    }
}
