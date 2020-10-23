<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class describes an action to create a course
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CreateComponent extends CourseFormActionComponent
{
    /**
     * Checks the authorization for the current component
     *
     * @param Course $course
     */
    protected function checkComponentAuthorization(Course $course)
    {
        $countDirect = $countRequest = 0;

        $courseManagementRights = CourseManagementRights::getInstance();
        $courseTypes = DataManager::retrieve_active_course_types();

        foreach($courseTypes as $courseType)
        {
            if ($courseManagementRights->is_allowed(
                CourseManagementRights::CREATE_COURSE_RIGHT,
                $courseType->get_id(),
                CourseManagementRights::TYPE_COURSE_TYPE))
            {
                $countDirect ++;
            }
        }

        $allowCourseCreationWithoutCoursetype = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms', 'allow_course_creation_without_coursetype'));

        if ($allowCourseCreationWithoutCoursetype)
        {
            $countDirect ++;
        }

        if (!$this->isAuthorized(Manager::context(), 'ManageCourses') &&
            $countDirect == 0
        )
        {
            throw new NotAllowedException();
        }
    }

    /**
     * Returns the course for this form action
     * 
     * @return Course
     */
    public function get_course()
    {
        return new Course();
    }

    /**
     * Handles the course form
     * 
     * @param Course $course
     * @param string[string]
     * @return boolean
     */
    public function handle_form(Course $course, $form_values)
    {
        if (! $course->create() || ! $course->create_course_settings_from_values($form_values))
        {
            return false;
        }
        
        $courseEntityRelation = new CourseEntityRelation();
        $courseEntityRelation->set_course_id($course->get_id());
        $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_USER);
        $courseEntityRelation->setEntityId($course->get_titular_id());
        $courseEntityRelation->set_status(CourseEntityRelation::STATUS_TEACHER);
        
        if (! $courseEntityRelation->create())
        {
            return false;
        }
        
        return true;
    }

    /**
     * Returns the redirect message with the given succes
     * 
     * @param boolean $succes
     */
    public function get_redirect_message($succes)
    {
        $message = $succes ? 'ObjectCreated' : 'ObjectNotCreated';
        return Translation::get($message, array('OBJECT' => Translation::get('Course')), Utilities::COMMON_LIBRARIES);
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
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
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_url(), Translation::get('CourseManagerBrowseComponent')));
    }
}
