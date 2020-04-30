<?php
namespace Chamilo\Configuration\Test\Php\Unit;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\ImplementationNotifierDataClassListener;
use Chamilo\Libraries\Test\Test;

require_once (__DIR__ . '/implementation_notifier/data_manager.class.php');

/**
 * This test case tests the ImplementationNotifierDataClassListener
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImplementationNotifierTest extends Test
{

    /**
     * Tests the constructor with valid parameters
     */
    public function test_constructor_with_valid_parameters()
    {
        $data_class_mock = $this->createMock(DataClass::class);
        $method_mapping = array(ImplementationNotifierDataClassListener::BEFORE_DELETE => 'delete_method');
        
        new ImplementationNotifierDataClassListener($data_class_mock, __NAMESPACE__, $method_mapping);
        
        $this->assertTrue(true);
    }

    /**
     * Tests the constructor with invalid dataclass @expectedException \InvalidArgumentException
     */
    public function test_constructor_with_invalid_dataclass()
    {
        $method_mapping = array(ImplementationNotifierDataClassListener::BEFORE_DELETE => 'delete_method');
        
        new ImplementationNotifierDataClassListener($this, __NAMESPACE__, $method_mapping);
        
        $this->assertTrue(true);
    }

    /**
     * Tests the constructor with invalid context @expectedException \InvalidArgumentException
     */
    public function test_constructor_with_invalid_context()
    {
        $data_class_mock = $this->createMock(DataClass::class);
        $method_mapping = array(ImplementationNotifierDataClassListener::BEFORE_DELETE => 'delete_method');
        
        new ImplementationNotifierDataClassListener($data_class_mock, null, $method_mapping);
        
        $this->assertTrue(true);
    }

    /**
     * Tests the constructor with empty method mapping @expectedException \InvalidArgumentException
     */
    public function test_constructor_with_empty_method_mapping()
    {
        $data_class_mock = $this->createMock(DataClass::class);
        
        new ImplementationNotifierDataClassListener($data_class_mock, __NAMESPACE__, array());
        
        $this->assertTrue(true);
    }

    /**
     * Tests the constructor with invalid method mapping @expectedException \InvalidArgumentException
     */
    public function test_constructor_with_invalid_method_mapping()
    {
        $data_class_mock = $this->createMock(DataClass::class);
        $method_mapping = array('test' => 'delete_method');
        
        new ImplementationNotifierDataClassListener($data_class_mock, __NAMESPACE__, $method_mapping);
        
        $this->assertTrue(true);
    }

    /**
     * Tests the event notification
     */
    public function test_event_notification()
    {
        $this->event_notification_helper('event_method', true);
    }

    /**
     * Tests the event notification
     */
    public function test_event_notification_with_failed_notification()
    {
        $this->event_notification_helper('failed_event_method', false);
    }

    /**
     * Tests the event notification with an unexisting method
     */
    public function test_event_notification_with_unexisting_method()
    {
        $this->event_notification_helper('unexisting_method', true);
    }

    /**
     * Tests the event notification with an unmapped method
     */
    public function test_event_notification_with_unmapped_method()
    {
        $data_class_mock = $this->createMock(DataClass::class);
        $method_mapping = array(ImplementationNotifierDataClassListener::BEFORE_DELETE => 'delete_method');
        
        $implementation_notifier = new ImplementationNotifierDataClassListener(
            $data_class_mock, 
            __NAMESPACE__, 
            $method_mapping);
        
        $this->assertTrue($implementation_notifier->on_before_create());
    }

    /**
     * Tests all the event notification methods @dataProvider event_notification_methods_data_provider
     */
    public function test_event_notification_methods($event)
    {
        $this->event_notification_helper('event_method', true, $event);
    }

    /**
     * The dataprovider for the events notification methods
     */
    public function event_notification_methods_data_provider()
    {
        return array(
            array(ImplementationNotifierDataClassListener::BEFORE_DELETE), 
            array(ImplementationNotifierDataClassListener::BEFORE_CREATE), 
            array(ImplementationNotifierDataClassListener::BEFORE_SET_PROPERTY), 
            array(ImplementationNotifierDataClassListener::BEFORE_UPDATE), 
            array(ImplementationNotifierDataClassListener::AFTER_DELETE), 
            array(ImplementationNotifierDataClassListener::AFTER_CREATE), 
            array(ImplementationNotifierDataClassListener::AFTER_SET_PROPERTY), 
            array(ImplementationNotifierDataClassListener::AFTER_UPDATE), 
            array(ImplementationNotifierDataClassListener::GET_DEPENDENCIES));
    }

    /**
     * Helper function for the tests of the event notification with given event method and expected value
     * 
     * @param string $event_method
     * @param bool $expected_value
     * @param string $event
     */
    protected function event_notification_helper($event_method, $expected_value, $event = 'on_before_delete')
    {
        $implementation_notifier_mock = $this->createMock(
            'libraries\storage\ImplementationNotifierDataClassListener', 
            array('get_implementation_packages'), 
            array(
                $this->createMock(DataClass::class),
                'libraries\test\implementation_notifier', 
                array($event => $event_method)));
        
        $implementation_notifier_mock->expects($this->once())->method('get_implementation_packages')->will(
            $this->returnValue(array('libraries\test\implementation_notifier')));
        
        $this->assertEquals($implementation_notifier_mock->$event(), $expected_value);
    }
}
