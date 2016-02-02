<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseDeleter\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseDeleter\Forms\CourseDeleterForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseDeleter\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Translation;

/**
 * this component is used to delete a course
 *
 * @author Mattias De Pauw - Hogeschool Gent
 */
class CourseDeleterComponent extends Manager
{

    public $delete_page;

    /**
     * this function checks whether the user have the rights to delete the form, build the delete form and checks if the
     * form is valid
     */
    public function run()
    {
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $this->delete_page = new CourseDeleterForm($this);
        $this->delete_page->buildForm();

        if ($this->delete_page->validate())
        {
            $this->delete_course();
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = Display :: error_message(Translation :: get('DeleteReDoMessage'));
            $html[] = $this->delete_page->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Deletes the course and redirect
     */
    public function delete_course()
    {
        $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_by_id(
            Course :: class_name(),
            $this->get_course_id());

        if (! $course->delete())
        {
            throw new \Exception(Translation :: get('CourseDeleteFailed'));
        }
        else
        {
            $this->redirect(
                Translation :: get('CourseDeleted'),
                false,
                array(
                    \Chamilo\Application\Weblcms\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_WEBLCMS_HOME));
        }
    }
}
