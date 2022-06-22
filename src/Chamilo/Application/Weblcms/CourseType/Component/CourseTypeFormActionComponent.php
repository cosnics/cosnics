<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Form\CourseTypeForm;
use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes an form action for the course type
 * 
 * @package \application\weblcms\course_type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CourseTypeFormActionComponent extends Manager
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
        set_time_limit(0);
        ini_set('memory_limit', - 1);
        
        $course_type = $this->get_course_type();
        $form = new CourseTypeForm($this->get_url(), $course_type);
        
        if ($form->validate())
        {
            $form_values = $form->exportValues();
            
            $this->set_course_type_properties_from_form_values($course_type, $form_values);
            
            $succes = $this->handle_form($course_type, $form_values) &&
                 $this->create_rights_from_form_values($course_type, $form_values);
            
            $message = $succes ? 'ObjectCreated' : 'ObjectNotCreated';
            $message = Translation::get(
                $message, 
                array('OBJECT' => Translation::get('CourseType')), 
                StringUtilities::LIBRARIES);
            
            $this->redirectWithMessage($message, ! $succes, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Sets the properties for a given course type with the given form values
     * 
     * @param $course_type CourseType
     * @param $form_values String[]
     */
    protected function set_course_type_properties_from_form_values(CourseType $course_type, $form_values)
    {
        $course_type->set_title($form_values[CourseType::PROPERTY_TITLE]);
        $course_type->set_description($form_values[CourseType::PROPERTY_DESCRIPTION]);
        $course_type->set_active($form_values[CourseType::PROPERTY_ACTIVE]);
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
        return CourseManagementRights::getInstance()->create_rights_from_values($course_type, $form_values);
    }

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course type for the given component (a new or a selected)
     * 
     * @return CourseType
     */
    abstract public function get_course_type();

    /**
     * Handles the course type form
     * 
     * @param $course_type CourseType
     * @param string[string]
     * @return boolean
     */
    abstract public function handle_form(CourseType $course_type, $form_values);
}
