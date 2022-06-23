<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Component;

use Chamilo\Core\Repository\Implementation\Flickr\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Flickr\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UploaderComponent extends Manager
{

    public function run()
    {
        $form = new ExternalObjectForm(ExternalObjectForm::TYPE_CREATE, $this->get_url(), $this);
        
        if ($form->validate())
        {
            
            $id = $form->upload_photo();
            
            if ($id)
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $id;

                return new RedirectResponse($this->getUrlGenerator()->fromParameters($parameters));
            }
            else
            {
                Request::set_get(Application::PARAM_ERROR_MESSAGE, Translation::get('FlickrUploadProblem'));
                
                $html = [];
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
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
