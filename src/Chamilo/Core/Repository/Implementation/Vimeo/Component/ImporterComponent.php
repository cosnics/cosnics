<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo\Component;

use Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo;
use Chamilo\Core\Repository\Implementation\Vimeo\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ImporterComponent extends Manager
{

    public function import_external_repository_object($object)
    {
        $vimeo = ContentObject::factory(Vimeo::class);
        $vimeo->set_title($object->get_title());
        $vimeo->set_description($object->get_description());
        $vimeo->set_owner_id($this->get_user_id());
        
        if ($vimeo->create())
        {
            SynchronizationData::quicksave($vimeo, $object, $this->get_external_repository()->get_id());
            
            $parameters = $this->get_parameters();
            $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
            $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
            $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $vimeo->get_id();
            
            $this->redirectWithMessage(Translation::get('ObjectImported', null, StringUtilities::LIBRARIES), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirectWithMessage(
                Translation::get('ObjectFailedImported', null, StringUtilities::LIBRARIES),
                true, 
                $parameters);
        }
    }
}
