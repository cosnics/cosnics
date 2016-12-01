<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Form\CourseTypeForm;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class describes an action to update a course type
 * 
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class UpdateComponent extends CourseTypeFormActionComponent
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
        return $this->get_selected_course_type();
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
        if (! $course_type->update() || ! $course_type->update_course_settings_from_values($form_values))
        {
            return false;
        }
        
        if ($form_values[CourseTypeForm::PROPERTY_FORCE_UPDATE] == 1)
        {
            if (! $course_type->force_course_settings_to_courses())
            {
                return false;
            }
            
            return $course_type->force_rights_to_courses();
        }
        
        return true;
    }

    /**
     * Create the rights for given form values
     * 
     * @param $course_type CourseType
     *
     * @param $form_values string[string]
     */
    protected function create_rights_from_form_values(CourseType $course_type, $form_values)
    {
        if (! parent::create_rights_from_form_values($course_type, $form_values))
        {
            return false;
        }
        
        if ($form_values[CourseTypeForm::PROPERTY_FORCE_UPDATE] == 1)
        {
            return $course_type->force_rights_to_courses();
        }
        
        return true;
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
        $breadcrumbtrail->add_help('weblcms_course_type_update');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_type_url(), Translation::get('CourseTypeManagerBrowseComponent')));
    }

    /**
     * Returns the registered parameters for this component
     * 
     * @param string[]
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_COURSE_TYPE_ID);
    }
}
