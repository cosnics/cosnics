<?php
namespace Chamilo\Core\Repository\Implementation\Hq23\Component;

use Chamilo\Core\Repository\Implementation\Hq23\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ExporterComponent extends Manager
{

    public function export_external_repository_object($object)
    {
        $success = parent :: export_external_repository_object($object);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(
                Translation :: get('ObjectExported', null, Utilities :: COMMON_LIBRARIES),
                false,
                $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_EXPORT_EXTERNAL_REPOSITORY;
            $this->redirect(
                Translation :: get('ObjectFailedExported', null, Utilities :: COMMON_LIBRARIES),
                true,
                $parameters);
        }
    }
}
