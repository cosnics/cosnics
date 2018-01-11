<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Unit\Lib\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;

/**
 * This test case test the ephorus tool ephorus request component class
 * 
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusRequestTest extends Test
{

    /**
     * **************************************************************************************************************
     * Test functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Tests that a base request is built correctly.
     */
    public function test_get_base_request()
    {
        $mock_component = $this->build_mock_component();
        
        $request = $this->get_method($mock_component, 'get_base_request')->invoke($mock_component);
        
        $this->assertNotNull($request);
        $this->assertEquals('1', $request->get_content_object_id());
        $this->assertEquals('2', $request->get_author_id());
        $this->assertEquals('3', $request->get_course_id());
        $this->assertEquals('4', $request->get_request_user_id());
    }

    /**
     * Tests that get base request throws an exception when an invalid object id is given.
     * @expectedException
     * common\libraries\architecture\NoObjectSelectedException
     */
    public function test_get_base_request_with_invalid_id()
    {
        $mock_component = $this->build_mock_component(false);
        
        $this->get_method($mock_component, 'get_base_request')->invoke($mock_component);
    }

    /**
     * Tests that get base request throws an exception when an id references a non existing object.
     * @expectedException
     * common\libraries\architecture\ObjectNotExistException
     */
    public function test_get_base_request_with_invalid_object()
    {
        $mock_component = $this->build_mock_component(true, false);
        
        $this->get_method($mock_component, 'get_base_request')->invoke($mock_component);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Builds a mock ephorus request component to test.
     * 
     * @param bool $valid_id
     * @param bool $valid_object
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function build_mock_component($valid_id = true, $valid_object = true)
    {
        // build mock component
        $mock_component = $this->createMock(
            'application\weblcms\tool\implementation\ephorus\EphorusRequestComponent', 
            array('get_course_id', 'get_user_id'), 
            array(), 
            'EphorusRequestComponent_test_get_base_request_' . $valid_id . '_' . $valid_object, 
            false);
        
        $id = null;
        if ($valid_id)
        {
            $id = array('1');
            $this->build_mock_component_valid_data_manager($mock_component, $valid_object);
        }
        
        // build request mock object
        $mock_request = $this->createMock('libraries\platform\Request', array('get'));
        
        $mock_request::staticExpects($this->any())->method('get')->will($this->returnValue($id));
        
        $this->get_method($mock_component, 'set_request_class')->invoke($mock_component, $mock_request);
        
        return $mock_component;
    }

    /**
     * Adds further mock objects and methods to the mock component in case a data manager needs to be mocked.
     * 
     * @param bool $mock_component
     * @param bool $valid_object
     */
    protected function build_mock_component_valid_data_manager($mock_component, $valid_object)
    {
        // build datamanager mock object
        $mock_datamanager = $this->createMock(
            'core\repository\DataManager', 
            array('get_instance', 'retrieve_content_object'));
        
        $mock_datamanager::staticExpects($this->any())->method('get_instance')->will(
            $this->returnValue($mock_datamanager));
        
        $document = null;
        if ($valid_object)
        {
            $document = new File();
            $document->set_owner_id('2');
        }
        
        $mock_datamanager::staticExpects($this->once())->method('retrieve_content_object')->will(
            $this->returnValue($document));
        
        // add further methods to the mock component
        if ($valid_object)
        {
            $mock_component->expects($this->once())->method('get_course_id')->will($this->returnValue('3'));
            
            $mock_component->expects($this->once())->method('get_user_id')->will($this->returnValue('4'));
        }
        
        $this->get_method($mock_component, 'set_data_manager_class')->invoke($mock_component, $mock_datamanager);
    }
}
