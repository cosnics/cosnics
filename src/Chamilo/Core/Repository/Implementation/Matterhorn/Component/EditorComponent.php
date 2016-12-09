<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Component;

use Chamilo\Core\Repository\Implementation\Matterhorn\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Matterhorn\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;

class EditorComponent extends Manager
{

    public function run()
    {
        $id = Request::get(self::PARAM_EXTERNAL_REPOSITORY_ID);
        $form = new ExternalObjectForm(
            ExternalObjectForm::TYPE_EDIT, 
            $this->get_url(array(self::PARAM_EXTERNAL_REPOSITORY_ID => $id)), 
            $this);
        
        $object = $this->retrieve_external_repository_object($id);
        
        $form->set_external_repository_object($object);
        
        if ($form->validate())
        {
            $success = $form->update_video_entry();
            
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            
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
