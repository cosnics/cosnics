<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class RevokerComponent extends Manager
{

    public function run()
    {
        $id = Request::get(self::PARAM_EXTERNAL_REPOSITORY_ID);
        $user = Request::get(self::PARAM_EXTERNAL_REPOSITORY_USER);
        $group = Request::get(self::PARAM_EXTERNAL_REPOSITORY_GROUP);
        if ($id || ($id && $user) || ($id && $group))
        {
            if (! $user && ! $group)
            {
                $success = $this->get_external_repository_manager_connector()->revoke_user_privilege($id);
                $success = $this->get_external_repository_manager_connector()->revoke_group_privilege($id);
            }
            elseif ($user)
            {
                $success = $this->get_external_repository_manager_connector()->revoke_user_privilege($id, $user);
            }
            elseif ($group)
            {
                $success = $this->get_external_repository_manager_connector()->revoke_group_privilege($id, $group);
            }
            if ($success)
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY_PRIVILEGES;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                $this->redirect(Translation::get('PrivilegesRevoked'), false, $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY_PRIVILEGES;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                $this->redirect(Translation::get('PrivilegesNotRevoked'), true, $parameters);
            }
        }
    }
}
