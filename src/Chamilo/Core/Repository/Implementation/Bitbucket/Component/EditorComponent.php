<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class EditorComponent extends Manager
{

    public function run()
    {
        $id = Request::get(Manager::PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->retrieve_external_repository_object($id);
        $parameters = $this->get_parameters();
        $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
        $group_form = new ExternalObjectForm($this->get_url($parameters), $this);
        $group_form->set_external_repository_object($object);
        
        if ($group_form->validate())
        {
            $success = $group_form->update_repository();
            $message = $success ? Translation::get('Updated') : Translation::get('NotUpdated');
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            
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
