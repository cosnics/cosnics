<?php
namespace Chamilo\Configuration\Test\Php\Unit\ImplementationNotifier;

/**
 * Stub class with the name DataManager to test the ImplementationNotifier class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager
{

    /**
     * Mock of the event method function
     * 
     * @return bool
     */
    public static function event_method()
    {
        return true;
    }

    /**
     * Mock of the failed_event_method function, changes the result to false
     * 
     * @return bool
     */
    public static function failed_event_method()
    {
        return false;
    }
}