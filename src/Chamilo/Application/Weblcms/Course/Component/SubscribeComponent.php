<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class describes an action to subscribe to a course
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubscribeComponent extends Manager
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
        $this->checkAuthorization(\Chamilo\Application\Weblcms\Manager::CONTEXT, 'ManagePersonalCourses');
        
        $failures = 0;
        
        $course_management_rights = CourseManagementRights::getInstance();
        $course_ids = $this->get_selected_course_ids();
        $this->set_parameter(self::PARAM_COURSE_ID, $course_ids);
        
        foreach ($course_ids as $course_id)
        {
            if ($course_management_rights->is_allowed_management(CourseManagementRights::DIRECT_SUBSCRIBE_RIGHT, $course_id))
            {
                $courseEntityRelation = new CourseEntityRelation();
                
                $courseEntityRelation->setEntityId($this->get_user_id());
                $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_USER);
                $courseEntityRelation->set_course_id($course_id);
                $courseEntityRelation->set_status(CourseEntityRelation::STATUS_STUDENT);
                
                if (! $courseEntityRelation->create())
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
            count($course_ids), 
            'UserNotSubscribedToSelectedCourses', 
            'UserNotSubscribedToSelectedCourse', 
            'UserSubscribedToSelectedCourses', 
            'UserSubscribedToSelectedCourse');
        
        $this->redirectWithMessage(
            $message, 
            ($failures > 0), 
            array(self::PARAM_ACTION => self::ACTION_BROWSE_UNSUBSCRIBED_COURSES), 
            array(self::PARAM_COURSE_ID));
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component...
     * Use this
     * function to add other breadcrumbs between the application / subapplication and the current component
     * 
     * @param $breadcrumbtrail \libraries\format\BreadcrumbTrail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_browse_course_url(), 
                Translation::get('CourseManagerBrowseUnsubscribedCoursesComponent')));
    }
}
