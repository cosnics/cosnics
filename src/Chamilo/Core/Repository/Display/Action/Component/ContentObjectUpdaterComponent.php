<?php
namespace Chamilo\Core\Repository\Display\Action\Component;

use Chamilo\Core\Repository\Display\Action\Manager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Original author unknown
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectUpdaterComponent extends Manager
{

    public function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed_to_edit_content_object())
        {
            $pid = Request::get('pid') ? Request::get('pid') : $_POST['pid'];
            
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $pid);
            
            $content_object->set_default_property(ContentObject::PROPERTY_OWNER_ID, $this->get_user_id());
            
            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, 
                new PersonalWorkspace($this->get_user()), 
                $content_object, 
                'edit', 
                'post', 
                $this->get_url(
                    array(
                        \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Display\Manager::ACTION_UPDATE_CONTENT_OBJECT, 
                        'pid' => $pid)));
            
            if ($form->validate() || Request::get('validated'))
            {
                $succes = $form->update_content_object();
                
                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'), 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES));
                
                $params = array();
                $params['pid'] = Request::get('pid');
                $params['tool_action'] = Request::get('tool_action');
                $params[\Chamilo\Core\Repository\Display\Manager::PARAM_ACTION] = \Chamilo\Core\Repository\Display\Action\Manager::ACTION_VIEW_CLO;
                
                $this->redirect($message, (! $succes), $params);
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
        else
        {
            throw new NotAllowedException();
        }
    }
}
