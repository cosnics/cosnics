<?php
namespace Chamilo\Configuration\Test\Php\Php\Acceptance\_Files;

class RequirerTestCase extends \PHPUnit_Framework_TestCase
{

    private $class_file;

    public function __construct($class_file)
    {
        parent::__construct("class_file_should_pass_compilation");
        $this->class_file = $class_file;
    }

    public function class_file_should_pass_compilation()
    {
        $requirer_file = __DIR__ . '/requirer.php';
        $command_string = "php {$requirer_file} {$this->class_file} 2>&1";
        $output_array = null;
        $output_result = null; // 255 is bad 0 is good
        exec($command_string, $output_array, $output_result);
        $this->assertEmpty($output_array, "Problem with file {$this->class_file} : \n" . implode($output_array, "\n"));
    }
}
