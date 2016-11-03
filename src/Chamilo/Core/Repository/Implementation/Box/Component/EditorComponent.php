<?php
namespace Chamilo\Core\Repository\Implementation\Box\Component;

use Chamilo\Core\Repository\Implementation\Box\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Box\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;

class EditorComponent extends Manager
{

    public function run()
    {
        $id = Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $form = new ExternalObjectForm(
            ExternalObjectForm :: TYPE_EDIT,
            $this->get_url(array(Manager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)),
            $this);

        $object = $this->retrieve_external_repository_object($id);

        $form->set_external_repository_object($object);

        if ($form->validate())
        {
            $success = $form->update_file();

            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

            $redirect = new Redirect($parameters);
            $redirect->toUrl();
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
