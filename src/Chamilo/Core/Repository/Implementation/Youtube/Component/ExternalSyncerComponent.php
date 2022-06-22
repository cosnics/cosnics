<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\ExternalObject;
use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExternalSyncerComponent extends Manager
{

    public function run()
    {
        if (! $this->get_external_repository()->get_user_setting('session_token'))
        {
            throw new NotAllowedException();
        }
        else
        {
            return parent::run();
        }
    }

    public function synchronize_external_repository_object(ExternalObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $content_object = $synchronization_data->get_content_object();
        
        $values = [];
        $values[ExternalObject::PROPERTY_ID] = $external_object->get_id();
        $values[ExternalObject::PROPERTY_TITLE] = trim(html_entity_decode(strip_tags($content_object->get_title())));
        $values[ExternalObject::PROPERTY_DESCRIPTION] = trim(
            html_entity_decode(strip_tags($content_object->get_description())));
        $values[ExternalObject::PROPERTY_CATEGORY] = $external_object->get_category();
        $values[ExternalObject::PROPERTY_TAGS] = $external_object->get_tags();
        
        if ($this->get_external_repository_manager_connector()->update_youtube_video($values))
        {
            $external_object = $this->get_external_repository_manager_connector()->retrieve_external_repository_object(
                $external_object->get_id());
            
            $synchronization_data->set_content_object_timestamp($content_object->get_modification_date());
            $synchronization_data->set_external_object_timestamp($external_object->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $content_object->get_id();
                $this->redirectWithMessage(
                    Translation::get(
                        'ObjectUpdated', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        StringUtilities::LIBRARIES),
                    false, 
                    $parameters, 
                    array(Manager::PARAM_EXTERNAL_REPOSITORY, Manager::PARAM_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirectWithMessage(
                    Translation::get(
                        'ObjectFailedUpdated', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        StringUtilities::LIBRARIES),
                    true, 
                    $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirectWithMessage(
                Translation::get(
                    'ObjectFailedUpdated', 
                    array('OBJECT' => Translation::get('ExternalRepository')), 
                    StringUtilities::LIBRARIES),
                true, 
                $parameters);
        }
    }
}
