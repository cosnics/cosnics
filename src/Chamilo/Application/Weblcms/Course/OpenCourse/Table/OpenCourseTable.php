<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Table;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Displays open courses
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseTable extends CourseTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_COURSE_ID;

    /**
     * Returns the available table actions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(Manager::context(), self::TABLE_IDENTIFIER);
        
        if ($this->get_component()->isAuthorized(Manager::context(), 'ManageOpenCourses'))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)), 
                    Translation::getInstance()->getTranslation(
                        'MarkSelectedCoursesAsNoLongerOpen', 
                        null, 
                        Manager::context())));
        }
        
        return $actions;
    }
}
