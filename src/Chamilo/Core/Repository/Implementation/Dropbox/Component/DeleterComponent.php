<?php
namespace Chamilo\Core\Repository\Implementation\Dropbox\Component;

use Chamilo\Core\Repository\Implementation\Dropbox\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DeleterComponent extends Manager
{

    public function delete_external_repository_object($id)
    {
        $success = parent :: delete_external_repository_object($id);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(
                Translation :: get('ObjectDeleted', null, Utilities :: COMMON_LIBRARIES),
                false,
                $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $this->redirect(
                Translation :: get('ObjectFailedDeleted', null, Utilities :: COMMON_LIBRARIES),
                true,
                $parameters);
        }
    }
}
