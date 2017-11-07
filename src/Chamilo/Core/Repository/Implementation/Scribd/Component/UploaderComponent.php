<?php
namespace Chamilo\Core\Repository\Implementation\Scribd\Component;

use Chamilo\Core\Repository\Implementation\Scribd\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Scribd\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class UploaderComponent extends Manager
{

    public function run()
    {
        $form = new ExternalObjectForm(ExternalObjectForm::TYPE_CREATE, $this->get_url(), $this);
        if ($form->validate())
        {
            $id = $form->upload_document();
            
            if ($id)
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                
                $redirect = new Redirect($parameters);
                $redirect->toUrl();
            }
            else
            {
                Request::set_get(Application::PARAM_ERROR_MESSAGE, Translation::get('ScribdUploadProblem'));
                
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
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
