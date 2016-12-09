<?php
namespace Chamilo\Libraries\Architecture\Test;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassFile;
use Chamilo\Libraries\Architecture\Traits\ClassSubClass;
use Chamilo\Libraries\Utilities\StringUtilities;

trait TestApplication
{
    use \Chamilo\Libraries\Architecture\Traits\DirectoryScanner, \Chamilo\Libraries\Architecture\Traits\ClassFile, \Chamilo\Libraries\Architecture\Traits\ClassSubClass
    {
        ClassFile::getClassNameFromPHPFile insteadof ClassSubClass;
    }

    /**
     * Returns the path to the source files
     *
     * @return string
     */
    abstract protected function get_source_path();

//    /**
//     * Asserts that a condition is true.
//     *
//     * @param boolean $condition
//     * @param string $message
//     *
//     * @throws \PHPUnit_Framework_AssertionFailedError
//     */
//    abstract public static function assertTrue($condition, $message = '');
//
//    /**
//     * Asserts that a condition is false.
//     *
//     * @param boolean $condition
//     * @param string $message
//     *
//     * @throws \PHPUnit_Framework_AssertionFailedError
//     */
//    abstract public static function assertFalse($condition, $message = '');
//
//    /**
//     * Asserts that two variables are equal.
//     *
//     * @param mixed $expected
//     * @param mixed $actual
//     * @param string $message
//     * @param float $delta
//     * @param integer $maxDepth
//     * @param boolean $canonicalize
//     * @param boolean $ignoreCase
//     */
//    abstract public static function assertEquals(
//        $expected, $actual, $message = '', $delta = 0, $maxDepth = 10,
//        $canonicalize = false, $ignoreCase = false
//    );

    /**
     * This test checks if the manager.class.php file can be found
     */
    public function test_package_contains_manager_file()
    {
        $source_folder = $this->get_source_path();

        $manager_file = $source_folder . DIRECTORY_SEPARATOR . 'Manager.php';

        $this->assertTrue(file_exists($manager_file));
    }

    /**
     * This test checks if the package uses the correct manager
     */
    public function test_package_uses_manager()
    {
        $namespace = $this->determine_package_namespace();
        $class_name = $namespace . '\\' . 'Manager';

        $this->assertTrue(class_exists($class_name));
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

        $base_component_name =
            (string) StringUtilities::getInstance()->createString(basename($component_file, '.php'))->upperCamelize();

        $expected_component_name = $base_component_name . 'Component';

        $this->assertEquals($expected_component_name, $this->getClassNameFromPHPFile($component_file));
    }

    /**
     * Returns the path to the component folder
     *
     * @return string
     */
    protected function get_component_folder_path()
    {
        return $this->get_source_path() . DIRECTORY_SEPARATOR . 'Component';
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

        $correct_path = 'Storage' . DIRECTORY_SEPARATOR . 'DataManager.php';
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

        $class_name = $this->getClassNameFromPHPFile($file);

        $this->assertEquals($class_name, $this->determine_package_namespace() . '\Storage\DataManager');
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
            $this->check_if_class_in_file_is_subclass_of(
                $file, array('Chamilo\Libraries\Storage\DataManager\DataManager')
            )
        );
    }

    /**
     * Provides the data for the datamanager files
     *
     * @return string[][]
     */
    public function data_manager_files_data_provider()
    {
        return $this->scan_files_in_directory($this->get_source_path(), '/^.+DataManager\.php$/i');
    }
}