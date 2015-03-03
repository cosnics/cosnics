<?php
namespace Chamilo\Core\Repository\Implementation\Slideshare\Component;

use Chamilo\Core\Repository\Implementation\Slideshare\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Slideshare\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class EditorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $id = Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $form = new ExternalObjectForm(
            ExternalObjectForm :: TYPE_EDIT,
            $this->get_url(array(Manager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)),
            $this);

        $object = $this->retrieve_external_repository_object($id);
        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(null, Translation :: get('EditorComponent', array('TITLE' => $object->get_title()))));

        $form->set_external_repository_object($object);

        if ($form->validate())
        {
            $success = $form->update_photo();

            $parameters = $this->get_parameters();
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

            if ($this->is_stand_alone())
            {
                Redirect :: web_link(
                    Path :: getInstance()->getBasePath(true) . 'common/launcher/index.php',
                    $parameters);
            }
            else
            {
                Redirect :: web_link(Path :: getInstance()->getBasePath(true) . 'index.php', $parameters);
            }
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
