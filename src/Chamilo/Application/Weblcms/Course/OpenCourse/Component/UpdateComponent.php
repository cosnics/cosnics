<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Component;

use Chamilo\Application\Weblcms\Course\OpenCourse\Form\OpenCourseForm;
use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component to update the roles that can access a course
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdateComponent extends Manager
{

    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageOpenCourses');

        $courseIds = $this->getCourseIdsFromRequest();

        $course = new Course();
        $course->setId($courseIds[0]);

        $form = new OpenCourseForm(OpenCourseForm::FORM_TYPE_EDIT, $this->get_url(), Translation::getInstance());
        $form->setDefaultRoles($this->getOpenCourseService()->getRolesForOpenCourse($course)->as_array());

        if ($form->validate())
        {
            $exportValues = $form->exportValues();

            try
            {
                $this->getOpenCourseService()->updateRolesForCourses(
                    $this->getUser(), $courseIds, $exportValues['roles']['role']
                );

                $success = true;
                $messageVariable = 'OpenCoursesUpdated';
            }
            catch (\Exception $ex)
            {
                $success = false;
                $messageVariable = 'OpenCoursesUpdated';
            }

            $this->redirect(
                Translation::getInstance()->getTranslation($messageVariable, null, Manager::context()),
                !$success,
                array(self::PARAM_ACTION => self::ACTION_BROWSE)
            );
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the list of additional parameters that need to be registered
     *
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_COURSE_ID);
    }
}