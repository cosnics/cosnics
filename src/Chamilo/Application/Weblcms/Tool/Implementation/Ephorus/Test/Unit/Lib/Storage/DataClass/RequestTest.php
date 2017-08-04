<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Unit\Lib\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Storage\DataClassTest;

/**
 * Tests the request data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestTest extends DataClassTest
{

    /**
     * **************************************************************************************************************
     * Test functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Tests that the check before save function works
     */
    public function test_check_before_save()
    {
        $mock_object = $this->prepare_mock_object_for_check_before_save_tests(true, true, false);
        
        $this->assertTrue($this->invoke_check_before_save($mock_object));
    }

    /**
     * Tests that the check before save function works with invalid status and that the status is set to OK
     */
    public function test_check_before_save_with_invalid_status()
    {
        $mock_object = $this->prepare_mock_object_for_check_before_save_tests(false, true, false);
        
        $this->assertTrue($this->invoke_check_before_save($mock_object));
        $this->assertTrue($mock_object->get_status() == Request::STATUS_IN_PROGRESS);
    }

    /**
     * Tests that the check before save function works with invalid process type and that the process type is set to
     * check and index
     */
    public function test_check_before_save_with_invalid_process_type()
    {
        $mock_object = $this->prepare_mock_object_for_check_before_save_tests(true, false, false);
        
        $this->assertTrue($this->invoke_check_before_save($mock_object));
        $this->assertTrue($mock_object->get_process_type() == Request::PROCESS_TYPE_CHECK_AND_VISIBLE);
    }

    /**
     * Tests that the check before save function fails when the required fields are not filled in.
     */
    public function test_check_before_save_without_required_fields()
    {
        $mock_object = $this->prepare_mock_object_for_check_before_save_tests(true, true, true);
        
        $this->assertFalse($this->invoke_check_before_save($mock_object));
    }

    /**
     * Tests the truncate results
     */
    public function test_truncate_results()
    {
        $request = new Request();
        
        $data_manager_mock = $this->createMock('\application\weblcms\tool\ephorus\DataManager');
        $data_manager_mock::staticExpects($this->once())->method('deletes')->will($this->returnValue(true));
        
        $request->set_data_manager_class($data_manager_mock);
        $request->set_id(1);
        
        $this->assertTrue($request->truncate_results());
    }

    /**
     * Tests the truncate results with an invalid id @expectedException InvalidArgumentException
     */
    public function test_truncate_results_with_invalid_id()
    {
        $request = new Request();
        $request->truncate_results();
    }

    /**
     * Tests the delete function when the truncate results function fails
     */
    public function test_delete_with_failing_truncate_results()
    {
        $request_mock = $this->get_request_mock(array('truncate_results'));
        $request_mock->expects($this->once())->method('truncate_results')->will($this->returnValue(false));
        
        $this->assertFalse($request_mock->delete());
    }

    /**
     * Tests the is_status_valid method
     */
    public function test_is_status_valid()
    {
        $this->assertTrue($this->invoke_is_status_valid(Request::STATUS_OK));
    }

    /**
     * Tests the is_status_valid method with an invalid status
     */
    public function test_is_status_valid_with_invalid_status()
    {
        $this->assertFalse($this->invoke_is_status_valid(99999));
    }

    /**
     * Tests the is_process_type_valid method
     */
    public function test_is_process_type_valid()
    {
        $this->assertTrue($this->invoke_is_process_type_valid(Request::PROCESS_TYPE_CHECK_AND_VISIBLE));
    }

    /**
     * Tests the is_process_type_valid method with an invalid process_type
     */
    public function test_is_process_type_valid_with_invalid_process_type()
    {
        $this->assertFalse($this->invoke_is_process_type_valid(99999));
    }

    /**
     * Tests the is_content_object_valid
     */
    public function test_is_content_object_valid()
    {
        $this->assertTrue($this->invoke_is_content_object_valid(false, 'document', 'txt', 20000));
    }

    /**
     * Tests the is_content_object_valid with invalid content_object_id
     */
    public function test_is_content_object_valid_with_invalid_content_object_id()
    {
        $mock_object = $this->get_request_mock(array('get_content_object'));
        $mock_object->set_string_utilities_class($this->get_string_utilities_mock_with_return_value(true));
        
        $this->assertFalse($this->get_method($mock_object, 'is_content_object_valid')->invoke($mock_object));
    }

    /**
     * Tests the is_content_object_valid with invalid content_object
     */
    public function test_is_content_object_valid_with_invalid_content_object()
    {
        $mock_object = $this->get_request_mock(array('get_content_object'));
        $mock_object->set_string_utilities_class($this->get_string_utilities_mock_with_return_value(false));
        
        $mock_object->expects($this->once())->method('get_content_object');
        
        $this->assertFalse($this->get_method($mock_object, 'is_content_object_valid')->invoke($mock_object));
    }

    /**
     * Tests the is_content_object_valid with different type of content_object
     */
    public function test_is_content_object_valid_with_different_content_object_type()
    {
        $this->assertFalse($this->invoke_is_content_object_valid(false, 'announcement'));
    }

    /**
     * Tests the is_content_object_valid with unsupported extension
     */
    public function test_is_content_object_valid_with_unsupported_extension()
    {
        $this->assertFalse($this->invoke_is_content_object_valid(false, 'document', 'unsupported'));
    }

    /**
     * Tests the is_content_object_valid
     */
    public function test_is_content_object_valid_with_too_large_file()
    {
        $this->assertFalse($this->invoke_is_content_object_valid(false, 'document', 'txt', 9999999999999));
    }

    /**
     * Tests the is_author_valid function
     */
    public function test_is_author_valid()
    {
        $this->assertTrue($this->invoke_test_is_author_valid(false, true));
    }

    /**
     * Tests the is_author_valid function with an invalid author id
     */
    public function test_is_author_valid_with_invalid_author_id()
    {
        $this->assertFalse($this->invoke_test_is_author_valid(true));
    }

    /**
     * Tests the is_author_valid function with an invalid author
     */
    public function test_is_author_valid_with_invalid_author()
    {
        $this->assertFalse($this->invoke_test_is_author_valid(false, false));
    }

    /**
     * Tests the get_available_statusses function
     */
    public function test_get_available_statusses()
    {
        $object = $this->get_data_class_object();
        
        $this->assertEquals($this->count_constants($object, 'STATUS'), count($object->get_available_statusses()));
    }

    /**
     * Tests the get_available_process_types function
     */
    public function test_get_available_process_types()
    {
        $object = $this->get_data_class_object();
        
        $this->assertEquals(
            $this->count_constants($object, 'PROCESS_TYPE'), 
            count($object->get_available_process_types()));
    }

    /**
     * **************************************************************************************************************
     * Inherited functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the object for the current tested dataclass
     * 
     * @return DataClass
     */
    protected function get_data_class_object()
    {
        return new \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request();
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Prepares a fully mocked object for the check before save tests
     * 
     * @param boolean $is_status_valid_return
     * @param boolean $is_process_type_valid_return
     * @param boolean $is_null_or_empty_return
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepare_mock_object_for_check_before_save_tests($is_status_valid_return, 
        $is_process_type_valid_return, $is_null_or_empty_return)
    {
        $mock_object = $this->get_request_mock(
            array('is_status_valid', 'is_process_type_valid', 'is_content_object_valid', 'is_author_valid'));
        
        $mock_object->expects($this->once())->method('is_status_valid')->will(
            $this->returnValue($is_status_valid_return));
        
        $mock_object->expects($this->once())->method('is_process_type_valid')->will(
            $this->returnValue($is_process_type_valid_return));
        
        $mock_object->expects($this->once())->method('is_content_object_valid');
        
        $mock_object->expects($this->once())->method('is_author_valid');
        
        $mock_object->set_string_utilities_class(
            $this->get_string_utilities_mock_with_return_value($is_null_or_empty_return));
        
        return $mock_object;
    }

    /**
     * Invokes the check_before_save function on a given mock object
     * 
     * @param PHPUnit_Framework_MockObject_MockObject $mock_object
     *
     * @return boolean
     */
    protected function invoke_check_before_save($mock_object)
    {
        return $this->get_method($mock_object, 'check_before_save')->invoke($mock_object);
    }

    /**
     * Prepares and invokes a fully mocked object for the is_content_object_valid_tests
     * 
     * @param boolean $is_null_or_empty_return
     * @param string $get_type_result - [OPTIONAL]
     * @param string $get_extension_result - [OPTIONAL]
     * @param int $get_filesize_result - [OPTIONAL]
     */
    protected function invoke_is_content_object_valid($is_null_or_empty_return, $get_type_result = null, 
        $get_extension_result = null, $get_filesize_result = null)
    {
        $mock_object = $this->get_request_mock(array('get_content_object'));
        $mock_object->set_string_utilities_class(
            $this->get_string_utilities_mock_with_return_value($is_null_or_empty_return));
        
        $document_mock = $this->createMock('\core\repository\content_object\document\Document');
        
        if ($get_type_result)
        {
            $document_mock->expects($this->once())->method('get_type')->will($this->returnValue($get_type_result));
        }
        
        if ($get_extension_result)
        {
            $document_mock->expects($this->once())->method('get_extension')->will(
                $this->returnValue($get_extension_result));
        }
        
        if ($get_filesize_result)
        {
            $document_mock->expects($this->once())->method('get_filesize')->will(
                $this->returnValue($get_filesize_result));
        }
        
        $mock_object->expects($this->once())->method('get_content_object')->will($this->returnValue($document_mock));
        
        return $this->get_method($mock_object, 'is_content_object_valid')->invoke($mock_object);
    }

    /**
     * Prepares and invokes a fully mocked object for the is_author_valid tests
     * 
     * @param boolean $is_null_or_empty_return
     * @param string $get_author_return - [OPTIONAL]
     */
    protected function invoke_test_is_author_valid($is_null_or_empty_return, $get_author_return = null)
    {
        $mock_object = $this->get_request_mock(array('get_author'));
        $mock_object->set_string_utilities_class(
            $this->get_string_utilities_mock_with_return_value($is_null_or_empty_return));
        
        if ($get_author_return)
        {
            $mock_object->expects($this->once())->method('get_author')->will($this->returnValue($get_author_return));
        }
        
        return $this->get_method($mock_object, 'is_author_valid')->invoke($mock_object);
    }

    /**
     * Returns a mock for the string utilities class with a given return value for the function is_null_or_empty
     * 
     * @param boolean $is_null_or_empty_return
     *
     * @return MockObject
     */
    protected function get_string_utilities_mock_with_return_value($is_null_or_empty_return)
    {
        $string_utilities_mock = $this->createMock('\libraries\utilities\StringUtilities');
        $string_utilities_mock::staticExpects($this->any())->method('is_null_or_empty')->will(
            $this->returnValue($is_null_or_empty_return));
        
        return $string_utilities_mock;
    }

    /**
     * Returns the mock for the request class with a given set of mocked functions
     * 
     * @param string[] $mocked_functions - [OPTIONAL]
     */
    protected function get_request_mock($mocked_functions = array())
    {
        return $mock_object = $this->createMock('\application\weblcms\tool\ephorus\Request', $mocked_functions);
    }

    /**
     * Invokes the is_status_valid function with a given status
     * 
     * @param int $default_status
     *
     * @return boolean
     */
    protected function invoke_is_status_valid($status = null)
    {
        $request = new Request();
        $request->set_status($status);
        
        return $this->get_method($request, 'is_status_valid')->invoke($request);
    }

    /**
     * Invokes the is_process_type_valid function with a given process_type
     * 
     * @param int $default_status
     *
     * @return boolean
     */
    protected function invoke_is_process_type_valid($process_type = null)
    {
        $request = new Request();
        $request->set_process_type($process_type);
        
        return $this->get_method($request, 'is_process_type_valid')->invoke($request);
    }
}
