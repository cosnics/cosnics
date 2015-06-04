<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Component;

use Chamilo\Core\Repository\Implementation\GoogleDocs\ExternalObject;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class InternalSyncerComponent extends Manager
{

    public function synchronize_internal_repository_object(ExternalObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $document = $synchronization_data->get_content_object();

        $document->set_title($external_object->get_title());
        if (PlatformSetting :: get('description_required', \Chamilo\Core\Repository\Manager :: context()) && StringUtilities :: getInstance()->isNullOrEmpty(
            $external_object->get_description()))
        {
            $document->set_description('-');
        }
        else
        {
            $document->set_description($external_object->get_description());
        }

        $export_format = $document->get_extension();

        if (! in_array($export_format, $external_object->get_export_types()))
        {
            $export_format = 'pdf';
        }

        $document->set_in_memory_file($external_object->get_content_data($export_format));
        $document->set_filename(Filesystem :: create_safe_name($external_object->get_title()) . '.' . $export_format);

        if ($document->update())
        {
            $synchronization_data->set_content_object_timestamp($document->get_modification_date());
            $synchronization_data->set_external_object_timestamp($external_object->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID] = $document->get_id();
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
                    'ObjectFailedUpdated',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES),
                true,
                $parameters);
        }
    }
}
