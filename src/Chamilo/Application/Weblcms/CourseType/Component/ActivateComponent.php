<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

/**
 * This class describes an action to activate a course type
 * 
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class ActivateComponent extends ChangeActivationComponent
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the active status.
     * Since this is a specific implementation of the function to activate the course types
     * we always return true
     * 
     * @return true
     */
    protected function get_active_status()
    {
        return true;
    }
}
