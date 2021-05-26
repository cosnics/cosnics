<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;

/**
 * Logs a teacher in/out of the student view.
 * 
 * @author Tom Goethals
 */
class ViewAsComponent extends Manager
{

    public function run()
    {
        $userViewAllowed = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms', 'allow_view_as_user'));
        
        if (! $userViewAllowed)
        {
            throw new NotAllowedException();
        }
        
        $course_settings_controller = CourseSettingsController::getInstance();
        $course_access = $course_settings_controller->get_course_setting(
            $this->get_course(), 
            CourseSettingsConnector::COURSE_ACCESS);
        
        // if ($course_access == CourseSettingsConnector::COURSE_ACCESS_CLOSED)
        // {
        // throw new NotAllowedException();
        // }
        
        $view_as_user_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
        if (! isset($view_as_user_id))
        {
            // if the teacher is already logged in as another user, log him out
            // this time.
            
            Session::unregister(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
            Session::unregister(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);
            
            $this->redirect(
                Translation::get('ViewAsOriginal'), 
                false, 
                array(
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => null, 
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => null, 
                    \Chamilo\Application\Weblcms\Manager::PARAM_USERS => null));
        }
        else
        {
            if ($this->get_parent()->is_teacher())
            {
                Session::register(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID, $view_as_user_id);
                Session::register(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID, 
                    Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE));
                $this->redirect(
                    Translation::get('ViewAsUser'), 
                    false, 
                    array(
                        \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => null, 
                        \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => null, 
                        \Chamilo\Application\Weblcms\Manager::PARAM_USERS => null));
            }
            else
            {
                throw new NotAllowedException();
            }
        }
    }
}
