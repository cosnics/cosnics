<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Form\PublicationForm;
use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Storage\DataManager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class EditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');
        
        $id = Request::get(self::PARAM_SYSTEM_ANNOUNCEMENT_ID);
        $this->set_parameter(self::PARAM_SYSTEM_ANNOUNCEMENT_ID, $id);
        
        $user = $this->get_user();
        
        if ($id)
        {
            $system_announcement_publication = DataManager::retrieve_by_id(Publication::class_name(), $id);
            
            $content_object = $system_announcement_publication->get_content_object();
            
            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, 
                new PersonalWorkspace($this->get_user()), 
                $content_object, 
                'edit', 
                'post', 
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_EDIT, 
                        self::PARAM_SYSTEM_ANNOUNCEMENT_ID => $system_announcement_publication->get_id())));
            if ($form->validate() || Request::get('validated'))
            {
                $form->update_content_object();
                if ($form->is_version())
                {
                    $system_announcement_publication->set_content_object_id($content_object->get_latest_version_id());
                    $system_announcement_publication->update();
                }
                
                $publications = array($system_announcement_publication);
                
                $publication_form = new PublicationForm(
                    PublicationForm::TYPE_UPDATE, 
                    $publications, 
                    $this->get_url(array('validated' => 1)));
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->handle_form_submit();
                    $this->redirect(
                        Translation::get(
                            $success ? 'ObjectUpdated' : 'ObjectNotUpdated', 
                            array('OBJECT' => Translation::get('SystemAnnouncementPublication')), 
                            Utilities::COMMON_LIBRARIES), 
                        ($success ? false : true), 
                        array(self::PARAM_ACTION => self::ACTION_BROWSE));
                }
                else
                {
                    $html = array();
                    
                    $html[] = $this->render_header();
                    $html[] = $publication_form->toHtml();
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
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', 
                        array('OBJECT' => Translation::get('SystemAnnouncement')), 
                        Utilities::COMMON_LIBRARIES)));
        }
    }
}
