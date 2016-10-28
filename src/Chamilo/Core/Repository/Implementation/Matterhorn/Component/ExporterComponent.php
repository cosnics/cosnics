<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Component;

use Chamilo\Core\Repository\Implementation\Matterhorn\Manager;
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
            $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(
                Translation :: get('ObjectExported', null, Utilities :: COMMON_LIBRARIES),
                false,
                $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_EXPORT_EXTERNAL_REPOSITORY;
            $this->redirect(
                Translation :: get('ObjectFailedExported', null, Utilities :: COMMON_LIBRARIES),
                true,
                $parameters);
        }
    }
}
