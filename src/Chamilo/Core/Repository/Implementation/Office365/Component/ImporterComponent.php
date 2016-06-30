<?php
namespace Chamilo\Core\Repository\Implementation\Office365\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Implementation\Office365\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{
    public function import_external_repository_object($externalObject)
    {
        if ($externalObject->is_importable())
        {
            $file = ContentObject :: factory(File :: class_name());
            $this->sychronize_file_with_external_object($file, $externalObject);
  
            if ($file->create())
            {
                SynchronizationData :: quicksave($file, $externalObject, $this->get_external_repository()->get_id());

                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager :: context();
                $parameters[Application :: PARAM_ACTION] = \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID] = $file->get_id();
                $this->redirect(Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES), false, $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $externalObject->get_id();
                $this->redirect(Translation :: get('ObjectFailedImported', null, Utilities :: COMMON_LIBRARIES), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $externalObject->get_id();
            $this->redirect(null, false, $parameters);
        }
    }
}



