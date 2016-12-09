<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalSyncerComponent extends Manager
{

    public function run()
    {
        $id = Request::get(\Chamilo\Core\Repository\External\Manager::PARAM_EXTERNAL_REPOSITORY_ID);
        
        if ($id)
        {
            $object = $this->retrieve_external_repository_object($id);
            
            if (! $object->is_importable() && ($object->get_synchronization_status() ==
                 SynchronizationData::SYNC_STATUS_EXTERNAL ||
                 $object->get_synchronization_status() == SynchronizationData::SYNC_STATUS_CONFLICT))
            {
                $succes = $this->synchronize_external_repository_object($object);
                
                $params = $this->get_parameters();
                $params[\Chamilo\Core\Repository\External\Manager::PARAM_ACTION] = '';
                
                if ($succes)
                {
                    $this->redirect(Translation::get('Succes', null, Utilities::COMMON_LIBRARIES), false, $params);
                }
                else
                {
                    $this->redirect(Translation::get('Failed', null, Utilities::COMMON_LIBRARIES), true, $params);
                }
            }
        }
        else
        {
            $params = $this->get_parameters();
            $params[\Chamilo\Core\Repository\External\Manager::PARAM_ACTION] = \Chamilo\Core\Repository\External\Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $this->redirect(null, false, $params);
        }
    }
}
