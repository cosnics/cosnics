<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Component;

use Chamilo\Core\Repository\Implementation\GoogleDocs\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class UploaderComponent extends Manager
{

    public function run()
    {
        $form = new ExternalObjectForm(ExternalObjectForm::TYPE_CREATE, $this->get_url(), $this);
        
        if ($form->validate())
        {
            $id = $form->upload_file($this->set_folder_from_values($form->exportValues()));
            
            if (! is_null($id))
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                
                $redirect = new Redirect($parameters);
                $redirect->toUrl();
            }
            else
            {
                Request::set_get(Application::PARAM_ERROR_MESSAGE, Translation::get('GoogleDocsUploadProblem'));
                
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

    public function set_folder_from_values($values)
    {
        $parent_id = $values[ExternalObjectForm::PARENT_ID];
        $new_folder_name = $values[ExternalObjectForm::NEW_FOLDER];
        
        if (! StringUtilities::getInstance()->isNullOrEmpty($new_folder_name, true))
        {
            $new_folder = $this->create_new_folder($new_folder_name, $parent_id);
            if ($new_folder)
            {
                return $new_folder;
            }
        }
        return $parent_id;
    }

    public function create_new_folder($category_name, $parent_id)
    {
        return $this->get_external_repository_manager_connector()->create_external_repository_object(
            $category_name, 
            $parent_id);
    }
}
