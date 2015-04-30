<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class GroupDeleterComponent extends Manager
{

    public function run()
    {
        $group = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_GROUP);
        if ($group)
        {
            $success = $this->get_external_repository_manager_connector()->delete_group($group);
        }
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_GROUPS_VIEWER;
            $parameters[self :: PARAM_EXTERNAL_REPOSITORY_GROUP] = $group;
            $this->redirect(Translation :: get('GroupDeleted'), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_GROUPS_VIEWER;
            $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $group;
            $this->redirect(Translation :: get('GroupNotDeleted'), true, $parameters);
        }
    }
}
