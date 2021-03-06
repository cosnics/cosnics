<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Component;

use Chamilo\Application\Weblcms\Course\OpenCourse\Form\OpenCourseForm;
use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component to define existing courses as open by adding roles to them
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreateComponent extends Manager
{

    /**
     * Runs this component and returns it's output
     */
    function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageOpenCourses');
        
        $form = new OpenCourseForm(OpenCourseForm::FORM_TYPE_ADD, $this->get_url(), Translation::getInstance());
        
        if ($form->validate())
        {
            $exportValues = $form->exportValues();
            
            try
            {
                $this->getOpenCourseService()->attachRolesToCoursesByIds(
                    $this->getUser(), $exportValues['courses']['course'], $exportValues['roles']['role']
                );
                
                $success = true;
                $messageVariable = 'OpenCoursesAdded';
            }
            catch (\Exception $ex)
            {
                $success = false;
                $messageVariable = 'OpenCoursesNotAdded';
            }
            
            $this->redirect(
                Translation::getInstance()->getTranslation($messageVariable, null, Manager::context()), 
                ! $success, 
                array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }
}