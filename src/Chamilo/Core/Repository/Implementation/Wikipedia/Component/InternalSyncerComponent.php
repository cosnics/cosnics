<?php
namespace Chamilo\Core\Repository\Implementation\Wikipedia\Component;

use Chamilo\Core\Repository\Implementation\Wikipedia\ExternalObject;
use Chamilo\Core\Repository\Implementation\Wikipedia\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class InternalSyncerComponent extends Manager
{

    public function synchronize_internal_repository_object(ExternalObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $content_object = $synchronization_data->get_content_object();

        $content_object->set_title($external_object->get_title());
        if (PlatformSetting :: get('description_required', \Chamilo\Core\Repository\Manager :: context()) && StringUtilities :: getInstance()->isNullOrEmpty(
            $external_object->get_description()))
        {
            $content_object->set_description('-');
        }
        else
        {
            $content_object->set_description($external_object->get_description());
        }

        $content_object->set_filename($external_object->get_title() . '.' . 'html');

        $content_object->set_in_memory_file($external_object->get_content_data($external_object));

        if ($content_object->update())
        {
            $synchronization_data->set_content_object_timestamp($content_object->get_modification_date());
            $synchronization_data->set_external_object_timestamp($external_object->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID] = $content_object->get_id();
                $this->redirect(
                    Translation :: get(
                        'ObjectUpdated',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES),
                    false,
                    $parameters,
                    array(Manager :: PARAM_EXTERNAL_REPOSITORY, Manager :: PARAM_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirect(
                    Translation :: get(
                        'ObjectFailedUpdated',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES),
                    true,
                    $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(
                Translation :: get(
                    'ObjectUpdated',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES),
                true,
                $parameters);
        }
    }
}
