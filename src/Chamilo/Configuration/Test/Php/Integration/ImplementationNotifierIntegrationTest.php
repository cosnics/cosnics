<?php
namespace Chamilo\Configuration\Test\Php\Integration;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\ImplementationNotifierDataClassListener;
use Chamilo\Libraries\Test\Test;

/**
 * Integration Test for the ImplementationNotifierDataClassListener class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImplementationNotifierIntegrationTest extends Test
{

    /**
     * Tests the integration of the implementation packages
     */
    public function test_get_implementation_packages()
    {
        $implementation_notifier = new ImplementationNotifierDataClassListener(
            $this->getMock(DataClass::class_name()), 
            'core\metadata', 
            array(ImplementationNotifierDataClassListener::BEFORE_DELETE => 'delete_event'));
        
        $method = $this->get_method($implementation_notifier, 'get_implementation_packages');
        
        $this->assertTrue(count($method->invoke($implementation_notifier)) > 0);
    }
}