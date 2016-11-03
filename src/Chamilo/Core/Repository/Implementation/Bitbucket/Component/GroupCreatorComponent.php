<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Form\GroupForm;
use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class GroupCreatorComponent extends Manager
{

    public function run()
    {
        $parameters = $this->get_parameters();
        $group_form = new GroupForm($this->get_url($parameters), $this);
        if ($group_form->validate())
        {
            $success = $group_form->create_group();

            $message = $success ? Translation :: get('GroupCreated') : Translation :: get('GroupNotCreated');
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
