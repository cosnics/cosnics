<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class describes an action to unsubscribe from a course
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class UnsubscribeComponent extends Manager
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(\Chamilo\Application\Weblcms\Manager::context(), 'ManagePersonalCourses');
        
        $failures = 0;
        
        $course_management_rights = CourseManagementRights::getInstance();
        $courses = $this->get_selected_courses();
        $this->set_parameter(self::PARAM_COURSE_ID, $courses);
        
        foreach($courses as $course)
        {
            $course_id = $course->get_id();
            
            if (DataManager::is_user_direct_subscribed_to_course($this->get_user_id(), $course->get_id()) && $course_management_rights->is_allowed_management(
                CourseManagementRights::DIRECT_UNSUBSCRIBE_RIGHT, 
                $course->get_id()) && ! $course->is_subscribed_as_course_admin($this->get_user()))
            {
                if (! DataManager::delete_course_user_relations_for_user_and_courses($this->get_user_id(), $course_id))
                {
                    $failures ++;
                }
            }
            else
            {
                $failures ++;
            }
        }
        
        $message = $this->get_result(
            $failures, 
            $courses->count(),
            'UserNotUnsubscribedFromSelectedCourses', 
            'UserNotUnsubscribedFromSelectedCourse', 
            'UserUnsubscribedFromSelectedCourses', 
            'UserUnsubscribedFromSelectedCourse');
        
        $this->redirect(
            $message, 
            ($failures > 0), 
            array(self::PARAM_ACTION => self::ACTION_BROWSE_SUBSCRIBED_COURSES), 
            array(self::PARAM_COURSE_ID));
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component...
     * Use this
     * function to add other breadcrumbs between the application / subapplication and the current component
     * 
     * @param $breadcrumbtrail \libraries\format\BreadcrumbTrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_unsubscriber');
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_browse_course_url(), 
                Translation::get('CourseManagerBrowseSubscribedCoursesComponent')));
    }
}
