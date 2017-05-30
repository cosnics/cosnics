<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Component;

use Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass\Matterhorn;
use Chamilo\Core\Repository\Implementation\Matterhorn\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{

    public function import_external_repository_object($object)
    {
        if ($object->is_importable())
        {
            
            $streaming_video_clip = ContentObject::factory(Matterhorn::class_name());
            $streaming_video_clip->set_title($object->get_title());
            $streaming_video_clip->set_description($object->get_description());
            $streaming_video_clip->set_owner_id($this->get_user_id());
            
            $external_sync = new SynchronizationData();
            $external_sync->set_external_id($this->get_external_repository()->get_id());
            
            $streaming_video_clip->set_synchronization_data($external_sync);
            
            if ($streaming_video_clip->create())
            {
                SynchronizationData::quicksave(
                    $streaming_video_clip, 
                    $object, 
                    $this->get_external_repository()->get_id());
                $parameters = $this->get_parameters();
                $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
                $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS;
                
                $this->redirect(
                    Translation::get('ObjectImported', null, Utilities::COMMON_LIBRARIES), 
                    false, 
                    $parameters, 
                    array(self::PARAM_EXTERNAL_REPOSITORY));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
                $this->redirect(
                    Translation::get('ObjectFailedImported', null, Utilities::COMMON_LIBRARIES), 
                    true, 
                    $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirect(null, false, $parameters);
        }
    }
}
