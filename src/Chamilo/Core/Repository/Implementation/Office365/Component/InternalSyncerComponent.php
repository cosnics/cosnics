<?php
namespace Chamilo\Core\Repository\Implementation\Office365\Component;

use Chamilo\Core\Repository\Implementation\Office365\ExternalObject;
use Chamilo\Core\Repository\Implementation\Office365\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class InternalSyncerComponent extends Manager
{

    public function synchronize_internal_repository_object(ExternalObject $externalObject)
    {
        $synchronization_data = $externalObject->get_synchronization_data();
        $contentObject = $synchronization_data->get_content_object();
        
        $this->synchronizeContentObjectWithExternalObject($contentObject, $externalObject);
        
        if ($contentObject->update())
        {
            $synchronization_data->set_content_object_timestamp($contentObject->get_modification_date());
            $synchronization_data->set_external_object_timestamp($externalObject->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
                $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $contentObject->get_id();
                $this->redirect(
                    Translation::get(
                        'ObjectUpdated', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES), 
                    false, 
                    $parameters, 
                    array(Manager::PARAM_EXTERNAL_REPOSITORY));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $externalObject->get_id();
                $this->redirect(
                    Translation::get(
                        'ObjectFailedUpdated', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES), 
                    true, 
                    $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $externalObject->get_id();
            $this->redirect(
                Translation::get(
                    'ObjectFailedUpdated', 
                    array('OBJECT' => Translation::get('ContentObject')), 
                    Utilities::COMMON_LIBRARIES), 
                true, 
                $parameters);
        }
    }
}
