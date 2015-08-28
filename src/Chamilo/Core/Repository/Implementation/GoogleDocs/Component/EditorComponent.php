<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Component;

use Chamilo\Core\Repository\Implementation\GoogleDocs\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class EditorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! $this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            throw new NotAllowedException();
        }
        else
        {
            $id = Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_ID);
            $form = new ExternalObjectForm(
                ExternalObjectForm :: TYPE_EDIT,
                $this->get_url(array(Manager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)),
                $this);

            $object = $this->retrieve_external_repository_object($id);
            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb(
                    null,
                    Translation :: get(
                        'EditorComponent',
                        array('TITLE' => $object->get_title()),
                        \Chamilo\Core\Repository\External\Manager :: context())));

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
}
