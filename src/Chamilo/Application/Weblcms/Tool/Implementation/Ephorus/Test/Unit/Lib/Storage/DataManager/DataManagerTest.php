<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Unit\Lib\Storage\DataManager;

/**
 * Tests the data_manager class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManagerTest extends Test
{

    /**
     * **************************************************************************************************************
     * Test functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Tests the retrieve_request_by_guid function
     */
    public function test_retrieve_request_by_guid()
    {
        $this->invoke_retrieve_request_by_guid($this->once(), '12345');
    }

    /**
     * Tests the retrieve_request_by_guid function with an invalid guid @expectedException InvalidArgumentException
     */
    public function test_retrieve_request_by_guid_with_invalid_guid()
    {
        $this->invoke_retrieve_request_by_guid($this->never());
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Invokes the retrieve_request_by_guid function with the given retrieves function expectation and guid
     * 
     * @param PHPUnit_Framework_MockObject_Matcher_InvokedCount $retrieve_expect
     * @param string $guid
     */
    protected function invoke_retrieve_request_by_guid($retrieve_expect, $guid = null)
    {
        $mocked_object = $this->createMock('\application\weblcms\tool\ephorus\DataManager', array('retrieve'));
        
        $mocked_object::staticExpects($retrieve_expect)->method('retrieve');
        
        $mocked_object::retrieve_request_by_guid($guid);
    }
}
