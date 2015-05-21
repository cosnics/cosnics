<?php
namespace Chamilo\Application\Weblcms\Course\Component;

/**
 * Abstract class that extends the browse component and is used for the subcribed / unsubscribed browser
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
abstract class BrowseSubscriptionCoursesComponent extends BrowseComponent
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Checkes whether or not the current user can view this component
     * 
     * @return boolean
     */
    protected function can_view_component()
    {
        return true;
    }

    /**
     * Creates and returns the action bar
     * 
     * @return \libraries\format\ActionBarRenderer
     */
    protected function build_action_bar()
    {
        return $this->build_basic_action_bar();
    }
}
