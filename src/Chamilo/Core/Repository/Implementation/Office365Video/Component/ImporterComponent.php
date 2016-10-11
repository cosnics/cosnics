<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video\Component;

use Chamilo\Core\Repository\ContentObject\Office365Video\Storage\DataClass\Office365Video;
use Chamilo\Core\Repository\Implementation\Office365Video\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager implements DelegateComponent
{
    public function import_external_repository_object($object)
    {
        $office365Video = ContentObject :: factory(Office365Video :: class_name());
        $office365Video->set_title($object->get_title());
        $office365Video->set_description($object->get_description());
        $office365Video->set_owner_id($this->get_user_id());

        if ($office365Video->create())
        {
            SynchronizationData :: quicksave($office365Video, $object, $this->get_external_repository()->get_id());

            $parameters = $this->get_parameters();
            $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager :: context();
            $parameters[Application :: PARAM_ACTION] = \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS;
            $parameters[\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID] = $office365Video->get_id();

            $this->redirect(
                Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES),
                false,
                $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirect(
                Translation :: get('ObjectFailedImported', null, Utilities :: COMMON_LIBRARIES),
                true,
                $parameters);
        }
    }
}
