<?php
namespace Chamilo\Core\Repository\Implementation\Dropbox\Component;

use Chamilo\Core\Repository\Implementation\Dropbox\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Dropbox\Manager;

class NewFolderComponent extends Manager
{

    public function run()
    {
        $form = new ExternalObjectForm(ExternalObjectForm :: TYPE_NEWFOLDER, $this->get_url(), $this);
        if ($form->validate())
        {
            $id = $form->create_folder();
            if (! is_null($id))
            {
                $parameters = $this->get_parameters();
                $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                $this->redirect('Folder is created', false, $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_NEWFOLDER_EXTERNAL_REPOSITORY;
                $this->redirect('Folder is not created', true, $parameters);
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
