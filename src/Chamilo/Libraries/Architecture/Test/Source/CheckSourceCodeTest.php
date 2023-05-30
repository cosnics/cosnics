<?php
namespace Chamilo\Libraries\Architecture\Test\Source;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Architecture\Traits\DirectoryScanner;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;

/**
 * Abstract test case that checks the php syntax for the php files of a package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CheckSourceCodeTest extends ChamiloTestCase
{
    use DirectoryScanner;

    /**
     * **************************************************************************************************************
     * Caching variables *
     * **************************************************************************************************************
     */

    /**
     * Caches the source files for reuse in multiple tests
     *
     * @var string[]
     */
    private $source_files;

    /**
     * **************************************************************************************************************
     * Tests *
     * **************************************************************************************************************
     */

    /**
     * Determines the package namespace depending on the namespace of the test class
     *
     * @return string
     */
    protected function determinePackageNamespace()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(
            ClassnameUtilities::getInstance()->getNamespaceParent(
                ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_called_class())
            )
        );
    }

    //    /**
    //     * This test checks if the package uses the correct installer
    //     */
    //    public function test_package_uses_installer()
    //    {
    //        $namespace = $this->determinePackageNamespace();
    //        $class_name = $namespace . '\Package\Installer';
    //
    //        $this->assertTrue(class_exists($class_name));
    //    }

    /**
     * **************************************************************************************************************
     * Data Providers *
     * **************************************************************************************************************
     */

    /**
     * Returns the path to the source files
     *
     * @return string
     */
    protected function get_source_path()
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        return $systemPathBuilder->namespaceToFullPath($this->determinePackageNamespace());
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */

    /**
     * Provides the data for the check php syntax test
     *
     * @return string[][]
     */
    public function php_files_data_provider()
    {
        if (!isset($this->source_files))
        {
            $this->source_files = $this->scanFilesInDirectory($this->get_source_path(), '/^.+\.php$/i');
        }

        return $this->source_files;
    }

    /**
     * This test checks if the syntax for a given php file is correct (Uses php lint for the check)
     *
     * @param string $file @dataProvider php_files_data_provider
     */
    public function test_php_syntax($file)
    {
        $lint_result = exec('php -l ' . $file);
        $syntax_correct = (strpos($lint_result, 'No syntax errors detected') !== false);

        $this->assertTrue($syntax_correct);
    }
}
