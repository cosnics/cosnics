<?php
namespace Chamilo\Core\Repository\Implementation\Soundcloud\Component;

use Chamilo\Core\Repository\ContentObject\Soundcloud\Storage\DataClass\Soundcloud;
use Chamilo\Core\Repository\Implementation\Soundcloud\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{

    public function import_external_repository_object($object)
    {
        $soundcloud = ContentObject::factory(Soundcloud::class_name());
        $soundcloud->set_title($object->get_title());
        $soundcloud->set_description(nl2br($object->get_description()));
        $soundcloud->set_owner_id($this->get_user_id());
        
        if ($soundcloud->create())
        {
            SynchronizationData::quicksave($soundcloud, $object, $this->get_external_repository()->get_id());
            
            $parameters = $this->get_parameters();
            $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
            $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
            $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $soundcloud->get_id();
            
            $this->redirect(Translation::get('ObjectImported', null, Utilities::COMMON_LIBRARIES), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirect(
                Translation::get('ObjectFailedImported', null, Utilities::COMMON_LIBRARIES), 
                true, 
                $parameters);
        }
    }
}
