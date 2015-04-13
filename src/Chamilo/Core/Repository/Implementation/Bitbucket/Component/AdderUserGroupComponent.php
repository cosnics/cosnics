<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Form\GroupUserAdditionForm;
use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class AdderUserGroupComponent extends Manager
{

    public function run()
    {
        $parameters = $this->get_parameters();
        $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_GROUP] = Request :: get(
            Manager :: PARAM_EXTERNAL_REPOSITORY_GROUP);

        $group_form = new GroupUserAdditionForm($this->get_url($parameters), $this);

        if ($group_form->validate())
        {
            $success = $group_form->add_user_to_group();
            $message = $success ? Translation :: get('UserAddedToGroup') : Translation :: get('UserNotAddedToGroup');
            $parameters = $this->get_parameters();
            if ($success)
            {
                $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_GROUPS_VIEWER;
            }
            else
            {
                $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_GROUP] = Request :: get(
                    Manager :: PARAM_EXTERNAL_REPOSITORY_GROUP);
            }
            $this->redirect($message, ! $success, $parameters);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $group_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
