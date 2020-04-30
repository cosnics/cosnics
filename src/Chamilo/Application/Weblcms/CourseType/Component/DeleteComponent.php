<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class describes an action to delete a course type
 * 
 * @package \application\weblcms\course_type
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
        $course_type_ids = $this->get_selected_course_type_ids();
        $this->set_parameter(self::PARAM_COURSE_TYPE_ID, $course_type_ids);
        
        $failures = 0;
        
        foreach ($course_type_ids as $course_type_id)
        {
            $course_type = DataManager::retrieve_by_id(CourseType::class, $course_type_id);
            
            if (! $course_type)
            {
                throw new ObjectNotExistException(Translation::get('CourseType'), $course_type_id);
            }
            
            if (! DataManager::has_course_type_courses($course_type->get_id()))
            {
                if (! $course_type->delete())
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
            count($course_type_ids), 
            'SelectedCourseTypeNotDeleted', 
            'SelectedCourseTypesNotDeleted', 
            'SelectedCourseTypeDeleted', 
            'SelectedCourseTypesDeleted');
        
        $this->redirect(
            $message, 
            ($failures > 0), 
            array(self::PARAM_ACTION => self::ACTION_BROWSE), 
            array(self::PARAM_COURSE_TYPE_ID));
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
        $breadcrumbtrail->add_help('weblcms_course_type_deleter');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_type_url(), Translation::get('CourseTypeManagerBrowseComponent')));
    }
}
