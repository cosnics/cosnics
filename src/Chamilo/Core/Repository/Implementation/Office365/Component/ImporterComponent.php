<?php
namespace Chamilo\Core\Repository\Implementation\Office365\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;
use Chamilo\Core\Repository\Implementation\Office365\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{

    public function import_external_repository_object($externalObject)
    {
        if ($externalObject->is_importable())
        {
            $contentObject = $this->createContentObject($externalObject);
            $this->synchronizeContentObjectWithExternalObject($contentObject, $externalObject);
            if ($contentObject->create())
            {
                SynchronizationData::quicksave(
                    $contentObject, 
                    $externalObject, 
                    $this->get_external_repository()->get_id());
                
                $parameters = $this->get_parameters();
                $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
                $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $contentObject->get_id();
                $this->redirect(
                    Translation::get('ObjectImported', null, Utilities::COMMON_LIBRARIES), 
                    false, 
                    $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $externalObject->get_id();
                $this->redirect(
                    Translation::get('ObjectFailedImported', null, Utilities::COMMON_LIBRARIES), 
                    true, 
                    $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $externalObject->get_id();
            $this->redirect(null, false, $parameters);
        }
    }

    public function createContentObject($externalObject)
    {
        if (empty(Request::get(self::PARAM_IMPORT_AS_LINK)))
        {
            return ContentObject::factory(File::class_name());
        }
        else
        {
            return ContentObject::factory(Link::class_name());
        }
    }
}
