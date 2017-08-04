<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Unit\Lib\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClassTest;

/**
 * Tests the result data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultTest extends DataClassTest
{

    /**
     * **************************************************************************************************************
     * Test functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Tests the happy path of the valid request method
     */
    public function test_is_valid_request()
    {
        $result = $this->prepare_result_object_with_mocked_string_utilities_and_data_manager(false, true);
        $this->assertTrue($this->get_method($result, 'is_valid_request')->invoke($result));
    }

    /**
     * Tests the valid request method with an empty request id
     */
    public function test_is_valid_request_with_empty_request_id()
    {
        $result = $this->prepare_result_object_with_mocked_string_utilities(true);
        $this->assertFalse($this->get_method($result, 'is_valid_request')->invoke($result));
    }

    /**
     * Tests the valid request method with an invalid request id
     */
    public function test_is_valid_request_with_invalid_request_id()
    {
        $result = $this->prepare_result_object_with_mocked_string_utilities_and_data_manager(false, false);
        $this->assertFalse($this->get_method($result, 'is_valid_request')->invoke($result));
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
        return new \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Result();
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Prepares the result object and returns the executable (protected) method for the is_valid_request tests with the
     * given result values
     * 
     * @param boolean $mocked_string_utilities_result
     * @param boolean $mocked_data_manager_result
     *
     * @return \application\weblcms\tool\ephorus\Result
     */
    protected function prepare_result_object_with_mocked_string_utilities_and_data_manager(
        $mocked_string_utilities_result, $mocked_data_manager_result)
    {
        $result = $this->prepare_result_object_with_mocked_string_utilities($mocked_string_utilities_result);
        
        $data_manager_mock = $this->createMock('\application\weblcms\tool\ephorus\DataManager');
        $data_manager_mock::staticExpects($this->once())->method('retrieve')->will(
            $this->returnValue($mocked_data_manager_result));
        
        $result->set_data_manager_class($data_manager_mock);
        
        return $result;
    }

    /**
     * Prepares the result object with a mocked string utilities dependency
     * 
     * @param boolean $mocked_string_utilities_result
     *
     * @return \application\weblcms\tool\ephorus\Result
     */
    protected function prepare_result_object_with_mocked_string_utilities($mocked_string_utilities_result)
    {
        $string_utilities_mock = $this->createMock('\libraries\utilities\StringUtilities');
        $string_utilities_mock::staticExpects($this->once())->method('is_null_or_empty')->will(
            $this->returnValue($mocked_string_utilities_result));
        
        $result = $this->get_data_class_object();
        
        $result->set_string_utilities_class($string_utilities_mock);
        
        return $result;
    }
}
