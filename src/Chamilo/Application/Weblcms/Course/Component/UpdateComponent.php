<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class describes an action to update a course
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class UpdateComponent extends CourseFormActionComponent
{

    /**
     * Returns the course for this form action
     * 
     * @return Course
     */
    public function get_course()
    {
        return $this->get_selected_course();
    }

    /**
     * Handles the course form
     * 
     * @param $course_type Course
     * @param string [string]
     * @return boolean
     */
    public function handle_form(Course $course, $form_values)
    {
        if (! $course->update() || ! $course->update_course_settings_from_values($form_values))
        {
            return false;
        }
        
        if ($course->isCourseTypeChanged())
        {
            DataManager::delete_course_type_user_category_rel_courses_by_course_id($course->getId());
        }
        
        $titular_id = (int) $course->get_titular_id();
        
        if ($titular_id)
        {
            $titular = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class_name(), 
                $course->get_titular_id());
            
            if ($course->is_subscribed_as_course_admin($titular))
            {
                return true;
            }
            
            $courseEntityRelation = DataManager::retrieve_course_user_relation_by_course_and_user(
                $course->get_id(), 
                $course->get_titular_id());
            
            if (! $courseEntityRelation)
            {
                $courseEntityRelation = new CourseEntityRelation();
                $courseEntityRelation->set_course_id($course->getId());
                $courseEntityRelation->setEntityId($titular->getId());
                $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_USER);
            }
            
            $courseEntityRelation->set_status(CourseEntityRelation::STATUS_TEACHER);
            
            return $courseEntityRelation->save();
        }
        else
        {
            return true;
        }
    }

    /**
     * Returns the redirect message with the given succes
     * 
     * @param boolean $succes
     */
    public function get_redirect_message($succes)
    {
        $message = $succes ? 'ObjectUpdated' : 'ObjectNotUpdated';
        
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
     * @param $breadcrumbtrail \libraries\format\BreadcrumbTrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_update');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_url(), Translation::get('CourseManagerBrowseComponent')));
    }

    /**
     * Returns the registered parameters for this component
     * 
     * @param string []
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_COURSE_ID);
    }
}
