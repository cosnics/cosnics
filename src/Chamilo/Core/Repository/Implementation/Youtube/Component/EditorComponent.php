<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EditorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (!$this->get_external_repository()->get_user_setting('session_token'))
        {
            throw new NotAllowedException();
        }
        else
        {
            $id = Request::get(Manager::PARAM_EXTERNAL_REPOSITORY_ID);
            $form = new ExternalObjectForm(
                ExternalObjectForm::TYPE_EDIT, $this->get_url(array(Manager::PARAM_EXTERNAL_REPOSITORY_ID => $id)),
                $this
            );

            $object = $this->retrieve_external_repository_object($id);
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    null, Translation::get(
                    'EditorComponent', array('TITLE' => $object->get_title()),
                    \Chamilo\Core\Repository\External\Manager::context()
                )
                )
            );

            $form->set_external_repository_object($object);

            if ($form->validate())
            {
                $success = $form->update_video();

                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

                return new RedirectResponse($this->getUrlGenerator()->fromParameters($parameters));
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }
}
