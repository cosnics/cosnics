<?php
namespace Chamilo\Core\Repository\Implementation\Hq23\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Implementation\Hq23\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{

    public function import_external_repository_object($external_object)
    {
        if ($external_object->is_importable())
        {
            $image = ContentObject::factory(File::class_name());
            
            if (StringUtilities::getInstance()->isNullOrEmpty($external_object->get_title()))
            {
                $image->set_title($external_object->get_id() . '.jpg');
            }
            else
            {
                $image->set_title($external_object->get_title());
            }
            
            if (Configuration::getInstance()->get_setting(
                array(\Chamilo\Core\Repository\Manager::context(), 'description_required')) && StringUtilities::getInstance()->isNullOrEmpty(
                $external_object->get_description()))
            {
                $image->set_description('-');
            }
            else
            {
                $image->set_description($external_object->get_description());
            }
            
            $image->set_owner_id($this->get_user_id());
            $image->set_filename($external_object->get_id() . '.jpg');
            
            $sizes = $external_object->get_available_sizes();
            $image->set_in_memory_file(file_get_contents($external_object->get_url(array_pop($sizes))));
            
            if ($image->create())
            {
                SynchronizationData::quicksave($image, $external_object, $this->get_external_repository()->get_id());
                
                $parameters = $this->get_parameters();
                $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
                $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $image->get_id();
                
                $this->redirect(
                    Translation::get('ObjectImported', null, Utilities::COMMON_LIBRARIES), 
                    false, 
                    $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
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
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(null, false, $parameters);
        }
    }
}
