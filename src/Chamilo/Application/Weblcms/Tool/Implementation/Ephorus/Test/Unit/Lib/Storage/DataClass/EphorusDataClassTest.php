<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Unit\Lib\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\DependencyContainer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\EphorusDataClass;

/**
 * Tests the functionality in the abstract ephorus data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusDataClassTest extends Test
{

    /**
     * **************************************************************************************************************
     * Test functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Tests the constructor without a dependency container
     */
    public function test_constructor_with_empty_dependency_container()
    {
        $ephorus_data_class = $this->get_ephorus_data_class();
        
        $this->assertInstanceOf(
            'application\weblcms\tool\ephorus\DependencyContainer', 
            $ephorus_data_class->get_dependency_container());
    }

    /**
     * Tests the constructor with a given dependency container
     */
    public function test_constructor_with_dependency_container()
    {
        $dependency_container = new DependencyContainer();
        $ephorus_data_class = $this->get_ephorus_data_class(array([], [], $dependency_container));
        
        $this->assertEquals($dependency_container, $ephorus_data_class->get_dependency_container());
    }

    /**
     * Tests that the initialize dependencies adds the datamanager class
     */
    public function test_intialize_dependencies_adds_data_manager()
    {
        $this->check_for_dependency('data_manager_class', 'application\weblcms\tool\ephorus\DataManager');
    }

    /**
     * Tests that the initialize dependencies adds the string utilities class
     */
    public function test_initialize_dependencies_adds_string_utilities()
    {
        $this->check_for_dependency('string_utilities_class', 'libraries\utilities\StringUtilities');
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the ephorus data class mock for testing
     * 
     * @param $parameters - [OPTIONAL] default array
     * @return EphorusDataClass
     */
    protected function get_ephorus_data_class($parameters = [])
    {
        return $this->getMockForAbstractClass('application\weblcms\tool\ephorus\EphorusDataClass', $parameters);
    }

    /**
     * Checks if this class has a given dependency
     * 
     * @param string $dependency_name
     * @param mixed $dependency
     */
    protected function check_for_dependency($dependency_name, $dependency)
    {
        $ephorus_data_class = $this->get_ephorus_data_class();
        $this->assertEquals($dependency, $ephorus_data_class->get_dependency($dependency_name));
    }
}
