<?php
namespace Chamilo\Core\Repository\Implementation\Box\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Implementation\Box\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{

    public function import_external_repository_object($external_object)
    {
        if ($external_object->is_importable())
        {
            $file = ContentObject::factory(File::class_name());
            $file->set_title($external_object->get_title());
            
            if (Configuration::getInstance()->get_setting(
                array(\Chamilo\Core\Repository\Manager::context(), 'description_required')) && StringUtilities::getInstance()->isNullOrEmpty(
                $external_object->get_description()))
            {
                $file->set_description('-');
            }
            else
            {
                $file->set_description($external_object->get_description());
            }
            
            $file->set_owner_id($this->get_user_id());
            $file->set_filename(Filesystem::create_safe_name($external_object->get_title()));
            $file->set_in_memory_file($external_object->get_content_data($external_object->get_id()));
            
            if ($file->create())
            {
                SynchronizationData::quicksave($file, $external_object, $this->get_external_repository()->get_id());
                
                $parameters = $this->get_parameters();
                $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
                $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $file->get_id();
                
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
