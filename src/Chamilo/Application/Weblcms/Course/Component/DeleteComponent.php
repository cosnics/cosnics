<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class describes an action to delete a course
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class DeleteComponent extends Manager
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
        $this->checkAuthorization(\Chamilo\Application\Weblcms\Manager::CONTEXT, 'ManageCourses');
        
        $course_ids = $this->get_selected_course_ids();
        $this->set_parameter(self::PARAM_COURSE_ID, $course_ids);
        $failures = 0;
        
        foreach ($course_ids as $course_id)
        {
            $course = DataManager::retrieve_by_id(Course::class, $course_id);
            
            if (! $course)
            {
                throw new ObjectNotExistException(Translation::get('Course'), $course_id);
            }
            
            if (! $course->delete())
            {
                $failures ++;
            }
        }
        
        $message = $this->get_result(
            $failures, 
            count($course_ids), 
            'SelectedCourseNotDeleted', 
            'SelectedCourseNotDeleted', 
            'SelectedCourseDeleted', 
            'SelectedCourseDeleted');
        
        $this->redirectWithMessage(
            $message, 
            ($failures > 0), 
            array(self::PARAM_ACTION => self::ACTION_BROWSE), 
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
            new Breadcrumb($this->get_browse_course_url(), Translation::get('CourseManagerBrowseComponent')));
    }
}
