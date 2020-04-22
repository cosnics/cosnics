<?php
namespace Chamilo\Configuration\Test\Php\Php\Acceptance;

use Chamilo\RecursiveDirectoryIterator;
use Chamilo\RecursiveIteratorIterator;
use Chamilo\RecursiveRegexIterator;
use Chamilo\RegexIterator;
use PHPUnit_Framework_TestSuite;

require_once __DIR__ . '/__files/RequirerTestCase.php';
class TheCodeIsLoadableTest extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        return new self();
    }

    public function __construct()
    {
        parent::__construct();
        $this->addTestForAllClassesInside(__DIR__ . '/../../php');
    }

    private function addTestForAllClassesInside($path)
    {
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator(
            $iterator, 
            '/^.+\.class\.php$/i', 
            RecursiveRegexIterator::GET_MATCH);
        
        foreach ($regex as $matches)
        {
            $class_file = $matches[0];
            $test = new RequirerTestCase($class_file);
            $this->addTest($test);
        }
    }
}
