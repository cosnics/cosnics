<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EditorComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user = $this->get_user();
        
        $id = Request::get(self::PARAM_PUBLICATION_ID);
        if ($id)
        {
            $calendar_event_publication = DataManager::retrieve_by_id(Publication::class_name(), $id);
            
            if (! $user->is_platform_admin() && $calendar_event_publication->get_publisher() != $user->get_id())
            {
                throw new NotAllowedException();
            }
            
            $content_object = $calendar_event_publication->get_publication_object();
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(null, Translation::get('Edit', array('TITLE' => $content_object->get_title()))));
            
            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, 
                new PersonalWorkspace($this->get_user()), 
                $content_object, 
                'edit', 
                'post', 
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_EDIT, 
                        self::PARAM_PUBLICATION_ID => $calendar_event_publication->get_id())));
            
            if ($form->validate() || Request::get('validated'))
            {
                if (! Request::get('validated'))
                {
                    $success = $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $calendar_event_publication->set_content_object($content_object->get_latest_version());
                    $calendar_event_publication->update();
                }
                
                $publication_form = new PublicationForm(
                    PublicationForm::TYPE_SINGLE, 
                    $content_object, 
                    $user, 
                    $this->get_url(
                        array(Manager::PARAM_PUBLICATION_ID => $calendar_event_publication->get_id(), 'validated' => 1)));
                $publication_form->set_publication($calendar_event_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_calendar_event_publication();
                    $message = $success ? Translation::get(
                        'ObjectUpdated', 
                        array('OBJECT' => Translation::get('PersonalCalendar')), 
                        Utilities::COMMON_LIBRARIES) : Translation::get(
                        'ObjectNotUpdated', 
                        array('OBJECT' => Translation::get('PersonalCalendar')), 
                        Utilities::COMMON_LIBRARIES);
                    
                    $this->redirect(
                        $message, 
                        ($success ? false : true), 
                        array(
                            self::PARAM_ACTION => Manager::ACTION_VIEW, 
                            self::PARAM_PUBLICATION_ID => $calendar_event_publication->get_id()));
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
                htmlentities(Translation::get('NoObjectsSelected', null, Utilities::COMMON_LIBRARIES)));
        }
    }
}
