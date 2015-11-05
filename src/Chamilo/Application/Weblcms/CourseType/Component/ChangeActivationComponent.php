<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class describes an action to change the activation status of a course type
 *
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class ChangeActivationComponent extends Manager
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
        $active_status = $active = $this->get_active_status();
        $course_types = $this->get_selected_course_types();
        $this->set_parameter(self :: PARAM_COURSE_TYPE_ID, $course_types);
        $failures = 0;

        while ($course_type = $course_types->next_result())
        {

            if (is_null($active_status))
            {
                $active = ! $course_type->is_active();
            }

            $course_type->set_active($active);

            if (! $course_type->update())
            {
                $failures ++;
            }
        }

        if ($active == 0)
        {
            $message = $this->get_result(
                $failures,
                count($course_types->size()),
                'CourseTypeNotDeactivated',
                'CourseTypesNotDeactivated',
                'CourseTypeDeactivated',
                'CourseTypesDeactivated');
        }
        else
        {
            $message = $this->get_result(
                $failures,
                count($course_types->size()),
                'CourseTypeNotActivated',
                'CourseTypesNotActivated',
                'CourseTypeActivated',
                'CourseTypesActivated');
        }

        $this->redirect(
            $message,
            ($failures > 0),
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE),
            array(self :: PARAM_COURSE_TYPE_ID));
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component... Use this
     * function to add other breadcrumbs between the application / subapplication and the current component
     *
     * @param $breadcrumbtrail \libraries\format\BreadcrumbTrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_type_activation_changer');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_type_url(), Translation :: get('CourseTypeManagerBrowseComponent')));
    }

    /**
     * **************************************************************************************************************
     * Dummy Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the active status. If this function returns null the status is inverted. This function should be
     * overriden in specific components to either activate or deactivate the course type
     *
     * @return boolean
     */
    protected function get_active_status()
    {
        return null;
    }
}
