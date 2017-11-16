<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class GranterComponent extends Manager
{

    public function run()
    {
        $id = Request::get(self::PARAM_EXTERNAL_REPOSITORY_ID);
        $user = Request::get(self::PARAM_EXTERNAL_REPOSITORY_USER);
        
        $privilege = Request::get(self::PARAM_EXTERNAL_REPOSITORY_PRIVILEGE);
        
        if ($id || ($id && $user && $privilege))
        {
            $success = $this->get_external_repository_manager_connector()->grant_user_privilege($id, $user, $privilege);
            if ($success)
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY_PRIVILEGES;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_USER] = $user;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_PRIVILEGE] = $privilege;
                $this->redirect(Translation::get('PrivilegesGranted'), false, $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY_PRIVILEGES;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                $this->redirect(Translation::get('PrivilegesNotGranted'), true, $parameters);
            }
        }
        else
        {
        }
    }
}
