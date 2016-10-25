<?php
namespace Chamilo\Configuration\Test\Php\Php\Acceptance;

require_once __DIR__ . '/__files/RequirerTestCase.php';
class TheCodeIsLoadableTest extends \PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        return new self();
    }

    public function __construct()
    {
        parent :: __construct();
        $this->addTestForAllClassesInside(__DIR__ . '/../../php');
    }

    private function addTestForAllClassesInside($path)
    {
        $directory = new \Chamilo\RecursiveDirectoryIterator($path);
        $iterator = new \Chamilo\RecursiveIteratorIterator($directory);
        $regex = new \Chamilo\RegexIterator(
            $iterator, 
            '/^.+\.class\.php$/i', 
            \Chamilo\RecursiveRegexIterator :: GET_MATCH);
        
        foreach ($regex as $matches)
        {
            $class_file = $matches[0];
            $test = new RequirerTestCase($class_file);
            $this->addTest($test);
        }
    }
}
