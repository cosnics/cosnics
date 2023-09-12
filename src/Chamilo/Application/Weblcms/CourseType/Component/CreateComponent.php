<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class describes an action to create a course type
 * 
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CreateComponent extends CourseTypeFormActionComponent
{

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course type for this form action
     * 
     * @return CourseType
     */
    public function get_course_type()
    {
        return new CourseType();
    }

    /**
     * Handles the course type form
     * 
     * @param $course_type CourseType
     * @param string[string]
     * @return boolean
     */
    public function handle_form(CourseType $course_type, $form_values)
    {
        return $course_type->create() && $course_type->create_course_settings_from_values($form_values);
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
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_type_url(), Translation::get('CourseTypeManagerBrowseComponent')));
    }
}
