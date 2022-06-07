<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class DeactivatorComponent extends Manager
{

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $ids = Request::get(self::PARAM_INSTANCE_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $external_instance = DataManager::retrieve_by_id(Instance::class, $id);
                $external_instance->deactivate();
                
                if (! $external_instance->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotDeactivated';
                    $parameter = array('OBJECT' => Translation::get('ExternalInstance'));
                }
                else
                {
                    $message = 'ObjectsNotDeactivated';
                    $parameter = array('OBJECTS' => Translation::get('VideosConferencing'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeactivated';
                    $parameter = array('OBJECT' => Translation::get('ExternalInstance'));
                }
                else
                {
                    $message = 'ObjectsDeactivated';
                    $parameter = array('OBJECTS' => Translation::get('VideosConferencing'));
                }
            }
            
            $this->redirect(
                Translation::get($message, $parameter, StringUtilities::LIBRARIES), (bool) $failures,
                array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation::get('NoExternalInstanceSelected')));
        }
    }
}
