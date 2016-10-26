<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Translation;

class CreatorComponent extends Manager
{

    public function run()
    {
        $parameters = $this->get_parameters();
        $group_form = new ExternalObjectForm($this->get_url($parameters), $this);
        if ($group_form->validate())
        {
            $success = $group_form->create_repository();
            $message = $success ? Translation :: get('Created') : Translation :: get('NotCreated');
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;

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
