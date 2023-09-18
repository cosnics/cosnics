<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes an action to create a course
 *
 * @package \application\weblcms\course
 * @author  Yannick & Tristan
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CreateComponent extends CourseFormActionComponent
{

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_browse_course_url(),
                $this->getTranslator()->trans('CourseManagerBrowseComponent', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * Checks the authorization for the current component
     *
     * @param Course $course
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkComponentAuthorization(Course $course): void
    {
        $countDirect = 0;

        $courseManagementRights = CourseManagementRights::getInstance();
        $courseTypes = DataManager::retrieve_active_course_types();

        foreach ($courseTypes as $courseType)
        {
            if ($courseManagementRights->is_allowed_management(
                CourseManagementRights::CREATE_COURSE_RIGHT, $courseType->getId(), WeblcmsRights::TYPE_COURSE_TYPE
            ))
            {
                $countDirect ++;
            }
        }

        $allowCourseCreationWithoutCoursetype = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Application\Weblcms', 'allow_course_creation_without_coursetype']
        );

        if ($allowCourseCreationWithoutCoursetype)
        {
            $countDirect ++;
        }

        if (!$this->isAuthorized(Manager::CONTEXT, 'ManageCourses') && $countDirect == 0)
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @return Course
     */
    public function get_course(): Course
    {
        return new Course();
    }

    /**
     * Returns the redirect message with the given succes
     */
    public function get_redirect_message(bool $succes): string
    {
        $translator = $this->getTranslator();
        $message = $succes ? 'ObjectCreated' : 'ObjectNotCreated';

        return $translator->trans($message, ['OBJECT' => $translator->trans('Course', [], Manager::CONTEXT)],
            StringUtilities::LIBRARIES);
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Handles the course form
     *
     * @param Course $course
     * @param string[] $form_values
     *
     * @return bool
     * @throws \Exception
     */
    public function handle_form(Course $course, $form_values): bool
    {
        if (!$course->create() || !$course->create_course_settings_from_values($form_values))
        {
            return false;
        }

        $courseEntityRelation = new CourseEntityRelation();
        $courseEntityRelation->set_course_id($course->getId());
        $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_USER);
        $courseEntityRelation->setEntityId($course->get_titular_id());
        $courseEntityRelation->set_status(CourseEntityRelation::STATUS_TEACHER);

        if (!$courseEntityRelation->create())
        {
            return false;
        }

        return true;
    }
}
