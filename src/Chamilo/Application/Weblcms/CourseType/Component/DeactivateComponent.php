<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

/**
 * This class describes an action to deactivate a course type
 * 
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class DeactivateComponent extends ChangeActivationComponent
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the active status.
     * Since this is a specific implementation of the function to deactivate the course types
     * we always return false
     * 
     * @return true
     */
    protected function get_active_status()
    {
        return false;
    }
}
