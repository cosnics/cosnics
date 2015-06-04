<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * This class describes an action to create a course quickly without going through the browser.
 * This class redirects you
 * out of the submanager
 *
 * @package \application\weblcms\course
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class QuickCreateComponent extends CreateComponent
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Runs this component and display's it's output
     */
    public function run()
    {
        return CourseFormActionComponent :: run();
    }

    /**
     * Handles the form Basic form handler + subscription of current user
     *
     * @param Course $course
     * @param string[] $form_values
     */
    public function handle_form(Course $course, $form_values)
    {
        if (! parent :: handle_form($course, $form_values))
        {
            return false;
        }

        if ($course->get_titular_id() != $this->get_user_id())
        {
            $course_user_relation = new CourseUserRelation();

            $course_user_relation->set_course($course);
            $course_user_relation->set_user($this->get_user());
            $course_user_relation->set_status(CourseUserRelation :: STATUS_TEACHER);

            if (! $course_user_relation->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Redirects this component after the creation
     *
     * @param boolean $succes
     * @param String $message
     */
    protected function redirect_after_form_handling($succes, $message)
    {
        $this->get_parent()->redirect_after_quick_create($succes, $message);
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component...
     * Use this
     * function to add other breadcrumbs between the application / subapplication and the current component
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_create');
    }
}
