<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class describes an action to move a course type (change the display order).
 * This class should be implemented to
 * provide the direction
 * 
 * @package \application\weblcms\course_type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MoverComponent extends Manager
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
        $course_types = $this->get_selected_course_types();
        $this->set_parameter(self::PARAM_COURSE_TYPE_ID, $course_types);
        
        $direction = Request::get(self::PARAM_MOVE_DIRECTION);
        
        $move_counter = ($direction == self::MOVE_DIRECTION_UP) ? - 1 : 1;
        
        $failures = 0;
        while ($course_type = $course_types->next_result())
        {
            $course_type->update_display_order_with_count($move_counter);
            
            if (! $course_type->update())
            {
                $failures ++;
            }
        }
        
        $message = $this->get_result(
            $failures, 
            count($course_types), 
            'CourseTypeNotMoved', 
            'CourseTypesNotMoved', 
            'CourseTypeMoved', 
            'CourseTypesMoved');
        
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
        $breadcrumbtrail->add_help('weblcms_course_type_activation_changer');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_type_url(), Translation::get('CourseTypeManagerBrowseComponent')));
    }
}
