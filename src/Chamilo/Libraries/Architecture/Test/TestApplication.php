<?php
namespace Chamilo\Libraries\Architecture\Test;

use Chamilo\Libraries\Utilities\Utilities;

trait TestApplication
{
    use Chamilo\DirectoryScanner, Chamilo\ClassFile, Chamilo\ClassSubClass {
        ClassFile::get_classname_from_php_file insteadof ClassSubClass;
    }

    /**
     * Returns the path to the source files
     * 
     * @return string
     */
    abstract protected function get_source_path();

    /**
     * Determines the package namespace depending on the namespace of the test class
     * 
     * @return string
     */
    abstract protected function determine_package_namespace();

    /**
     * Asserts that a condition is true.
     * 
     * @param boolean $condition
     * @param string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    abstract public static function assertTrue($condition, $message = '');

    /**
     * Asserts that a condition is false.
     * 
     * @param boolean $condition
     * @param string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    abstract public static function assertFalse($condition, $message = '');

    /**
     * Asserts that two variables are equal.
     * 
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @param float $delta
     * @param integer $maxDepth
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     */
    abstract public static function assertEquals($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, 
        $canonicalize = false, $ignoreCase = false);

    /**
     * This test checks if the manager.class.php file can be found
     */
    public function test_package_contains_manager_file()
    {
        $source_folder = $this->get_source_path();
        
        $manager_file = $source_folder . 'lib' . DIRECTORY_SEPARATOR . 'manager' . DIRECTORY_SEPARATOR .
             'manager.class.php';
        
        $this->assertTrue(file_exists($manager_file));
    }

    /**
     * This test checks if the package uses the correct manager
     */
    public function test_package_uses_manager()
    {
        $namespace = $this->determine_package_namespace();
        $package_name = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace, true);
        
        $class_name = $namespace . '\\' . $package_name . 'Manager';
        
        $this->assertFalse(class_exists($class_name));
    }

    /**
     * This test checks if the component folder can be found
     */
    public function test_package_contains_component_folder()
    {
        $component_folder = $this->get_component_folder_path();
        
        $this->assertTrue(file_exists($component_folder) && is_dir($component_folder));
    }

    /**
     * This test checks if the given component uses the correct class name
     * 
     * @param string $component_file @dataProvider component_files_data_provider
     */
    public function test_component_uses_correct_class_name($component_file)
    {
        if (empty($component_file))
        {
            return;
        }
        
        $base_component_name = (string) StringUtilities::getInstance()->createString(basename($component_file, '.php'))->upperCamelize();
        $expected_component_name = $base_component_name . 'Component';
        
        $this->assertEquals($expected_component_name, $this->get_classname_from_php_file($component_file));
    }

    /**
     * Returns the path to the component folder
     * 
     * @return string
     */
    protected function get_component_folder_path()
    {
        return $this->get_source_path() . 'lib' . DIRECTORY_SEPARATOR . 'manager' . DIRECTORY_SEPARATOR . 'component';
    }

    /**
     * Provides the data for the component files
     * 
     * @return string[][]
     */
    public function component_files_data_provider()
    {
        if (file_exists($this->get_component_folder_path()))
        {
            return $this->scan_files_in_directory($this->get_component_folder_path(), '/^.+\.class\.php$/i', 0);
        }
        
        return array(array(''));
    }

    /**
     * Tests if the data manager file is correct
     * @dataProvider data_manager_files_data_provider
     */
    public function test_data_manager_file_is_correct($file)
    {
        if (empty($file))
        {
            return;
        }
        
        $correct_path = '/php/lib/storage/data_manager/data_manager.class.php';
        $this->assertTrue(strpos($file, $correct_path) !== false);
    }

    /**
     * This test checks if the package uses the correct data_manager
     * @dataProvider data_manager_files_data_provider
     */
    public function test_package_uses_data_manager($file)
    {
        if (empty($file))
        {
            return;
        }
        
        $class_name = $this->get_classname_from_php_file($file);
        
        $this->assertEquals($class_name, 'DataManager');
    }

    /**
     * This test checks if the package uses the correct data_manager
     * @dataProvider data_manager_files_data_provider
     */
    public function test_data_manager_extends_from_core($file)
    {
        if (empty($file))
        {
            return;
        }
        
        $this->assertTrue(
            $this->check_if_class_in_file_is_subclass_of($file, array('\libraries\storage\data_manager\DataManager')));
    }

    /**
     * Provides the data for the datamanager files
     * 
     * @return string[][]
     */
    public function data_manager_files_data_provider()
    {
        return $this->scan_files_in_directory($this->get_source_path(), '/^.+data_manager\.class\.php$/i');
    }
}