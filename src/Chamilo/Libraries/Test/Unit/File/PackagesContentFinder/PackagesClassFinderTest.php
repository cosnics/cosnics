<?php

namespace Chamilo\Libraries\Test\Unit\File\PackagesContentFinder;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\PathBuilder;

/**
 * Tests the packages class finder
 *
 * @package common\libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesClassFinderTest extends ChamiloTestCase
{
    /**
     * The cache file
     *
     * @var string
     */
    private $cache_file;

    /**
     * @inheritDoc
     */
    protected  function setUp(): void
    {
        $this->cache_file = __DIR__ . '/cache.tmp';
    }

    /**
     * The cache file
     */
    protected  function tearDown(): void
    {
        unlink($this->cache_file);
    }

    /**
     * Tests the find classes in an existing example package (common/libraries)
     */
    public function testFindClassesInPackage()
    {
        $packages_class_finder = new PackagesClassFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'));
        $classes = $packages_class_finder->findClasses(
            'Console/Command/ClearCacheCommand.php', 'Console\Command\ClearCacheCommand'
        );

        $this->assertEquals('Chamilo\Libraries\Console\Command\ClearCacheCommand', $classes['Chamilo\Libraries']);
    }

    /**
     * Tests the find classes without packages, must return an empty array
     */
    public function testFindClassesWithoutPackages()
    {
        $packages_class_finder = new PackagesClassFinder(PathBuilder::getInstance());
        $classes = $packages_class_finder->findClasses(
            'Console/Command/ClearCacheCommand.php', 'Console\Command\ClearCacheCommand'
        );

        $this->assertEmpty($classes);
    }

    /**
     * Tests that the package class finder only returns existing classes
     */
    public function testFindInexistingClasses()
    {
        $packages_class_finder = new PackagesClassFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'));
        $classes = $packages_class_finder->findClasses(
            'Console/Command/ClearCacheCommand.php', 'Console\Command\ClearCacheCommandInexisting'
        );

        $this->assertEmpty($classes);
    }

    /**
     * Tests that the find classes writes it's list to an existing cache file
     */
    public function testFindClassesWithCacheFile()
    {
        $packages_class_finder = new PackagesClassFinder(
            PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file
        );

        $classes = $packages_class_finder->findClasses(
            'Console/Command/ClearCacheCommand.php', 'Console\Command\ClearCacheCommand'
        );

        $cached_classes = require $this->cache_file;

        $this->assertEquals($classes, $cached_classes);
    }

    /**
     * Tests that the find classes can use an existing cache file
     */
    public function testFindClassesWithExistingCacheFile()
    {
        $cached_classes = array('test\ClearCacheCommand');

        file_put_contents($this->cache_file, sprintf('<?php return %s;', var_export($cached_classes, true)));

        $packages_class_finder = new PackagesClassFinder(
            PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file
        );

        $classes = $packages_class_finder->findClasses(
            'Console/Command/ClearCacheCommand.php', 'Console\Command\ClearCacheCommand'
        );

        $this->assertEquals($classes[0], 'test\ClearCacheCommand');
        $this->assertEquals(count($classes), 1);
    }

    /**
     * This function test that an invalid cache file (cfr one that does not return an array) throws an exception
     *
     * @expectedException \Exception
     */
    public function testFindClassesWithInvalidCacheFile()
    {
        file_put_contents($this->cache_file, '<?php return 5;');

        $packages_class_finder = new PackagesClassFinder(
            PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file
        );

        $packages_class_finder->findClasses(
            'Console/Command/ClearCacheCommand.php', 'Console\Command\ClearCacheCommand'
        );
    }
}