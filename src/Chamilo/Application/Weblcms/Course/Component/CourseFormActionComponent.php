<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Form\CourseForm;
use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes an form action for the course
 * 
 * @package \application\weblcms\course
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CourseFormActionComponent extends Manager
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
        
        $course = $this->get_course();
        
        $possible_course_type_id = Request::post(Course::PROPERTY_COURSE_TYPE_ID);
        
        if (isset($possible_course_type_id) && $possible_course_type_id != - 1)
        {
            $course->set_course_type_id($possible_course_type_id);
        }

        $this->checkComponentAuthorization($course);
        
        $form = new CourseForm($this->get_url(), $course);
        
        $submit_request = Request::post('submit');
        
        if (! is_null($submit_request) && $form->validate())
        {
            $form_values = $form->exportValues();
            
            $this->set_course_properties_from_form_values($course, $form_values);
            
            $succes = $this->handle_form($course, $form_values) &&
                 $this->create_rights_from_form_values($course, $form_values);
            
            $message = $succes ? 'ObjectUpdated' : 'ObjectNotUpdated';
            $message = Translation::get(
                $message, 
                array('OBJECT' => Translation::get('Course')), 
                StringUtilities::LIBRARIES);
            
            $this->redirect_after_form_handling($succes, $message);
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
     * Redirects this component after the creation
     * 
     * @param boolean $succes
     * @param String $message
     */
    protected function redirect_after_form_handling($succes, $message)
    {
        $this->redirect($message, ! $succes, array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }

    /**
     * Sets the properties for a given course with the given form values
     * 
     * @param Course $course
     * @param String[] $form_values
     */
    protected function set_course_properties_from_form_values(Course $course, $form_values)
    {
        $course_settings = $form_values[CourseSettingsController::SETTING_PARAM_COURSE_SETTINGS];
        
        $course->set_titular_id($course_settings[CourseSettingsConnector::TITULAR]);
        $course->set_category_id($course_settings[CourseSettingsConnector::CATEGORY]);
        $course->set_language($course_settings[CourseSettingsConnector::LANGUAGE]);
        
        $course->set_title($form_values[Course::PROPERTY_TITLE]);
        $course->set_visual_code($form_values[Course::PROPERTY_VISUAL_CODE]);
        $course->set_course_type_id($form_values[Course::PROPERTY_COURSE_TYPE_ID]);
    }

    /**
     * Create the rights for given form values
     * 
     * @param Course $course
     *
     * @param string[string] $form_values
     */
    protected function create_rights_from_form_values(Course $course, $form_values)
    {
        return CourseManagementRights::getInstance()->create_rights_from_values($course, $form_values);
    }

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course for the given component (a new or a selected)
     * 
     * @return Course
     */
    abstract public function get_course();

    /**
     * Handles the course form
     * 
     * @param Course $course
     * @param string[string]
     * @return boolean
     */
    abstract public function handle_form(Course $course_type, $form_values);

    /**
     * Checks the authorization for the current component
     *
     * @param Course $course
     */
    abstract protected function checkComponentAuthorization(Course $course);
}
