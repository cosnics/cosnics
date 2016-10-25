<?php
namespace Chamilo\Core\Repository\Implementation\Wikipedia\Component;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Implementation\Wikipedia\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{

    public function import_external_repository_object($external_object)
    {
        if ($external_object->is_importable())
        {
            $wiki = ContentObject :: factory(Webpage :: class_name());
            $wiki->set_title($external_object->get_title());

            if (PlatformSetting :: get('description_required', \Chamilo\Core\Repository\Manager :: context()) && StringUtilities :: getInstance()->isNullOrEmpty(
                $external_object->get_description()))
            {
                $wiki->set_description('-');
            }
            else
            {
                $wiki->set_description($external_object->get_description());
            }

            $wiki->set_owner_id($this->get_user_id());
            $wiki->set_filename($external_object->get_title() . '.' . 'html');

            $wiki->set_in_memory_file($external_object->get_content_data($external_object));

            if ($wiki->create())
            {
                SynchronizationData :: quicksave($wiki, $external_object, $this->get_external_repository()->get_id());

                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager :: context();
                $parameters[Application :: PARAM_ACTION] = \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID] = $wiki->get_id();

                $this->redirect(
                    Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES),
                    false,
                    $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();

                $this->redirect(
                    Translation :: get('ObjectFailedImported', null, Utilities :: COMMON_LIBRARIES),
                    true,
                    $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();

            $this->redirect(null, false, $parameters);
        }
    }
}
