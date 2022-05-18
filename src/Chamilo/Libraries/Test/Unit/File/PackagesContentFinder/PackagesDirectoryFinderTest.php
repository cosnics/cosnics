<?php

namespace Chamilo\Libraries\Test\Unit\File\PackagesContentFinder;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesDirectoryFinder;

/**
 * Tests the packages directory finder
 *
 * @package Chamilo\Libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesDirectoryFinderTest extends ChamiloTestCase
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
    public function setUp(): void
    {
        $this->cache_file = __DIR__ . '/cache.tmp';
    }

    /**
     * The cache file
     */
    public function tearDown(): void
    {
        unlink($this->cache_file);
    }

    /**
     * Tests the find directories in an existing example package (Chamilo/Libraries)
     */
    public function testFindDirectoriesInPackage()
    {
        $packages_directories_finder = new PackagesDirectoryFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'));
        $directories = $packages_directories_finder->findDirectories('Resources/Configuration');

        $this->assertContains('Chamilo/Libraries/Resources/Configuration', $directories['Chamilo\Libraries']);
    }

    /**
     * Tests the find directories without packages, must return an empty array
     */
    public function testFindDirectoriesWithoutPackages()
    {
        $packages_directories_finder = new PackagesDirectoryFinder(PathBuilder::getInstance());
        $directories = $packages_directories_finder->findDirectories('Resources/Configuration');

        $this->assertEmpty($directories);
    }

    /**
     * Tests that the package directory finder only returns existing classes
     */
    public function testFindInexistingDirectories()
    {
        $packages_directories_finder = new PackagesDirectoryFinder(PathBuilder::getInstance());
        $directories = $packages_directories_finder->findDirectories('Resources/Inexisting');

        $this->assertEmpty($directories);
    }

    /**
     * Tests that the find directories writes it's list to an existing cache file
     */
    public function testFindDirectoriesWithCacheFile()
    {
        $packages_directories_finder =
            new PackagesDirectoryFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file);
        $directories = $packages_directories_finder->findDirectories('Resources/Configuration');

        $cached_directories = require $this->cache_file;

        $this->assertEquals($directories, $cached_directories);
    }

    /**
     * Tests that the find directories can use an existing cache file
     */
    public function testFindDirectoriesWithExistingCacheFile()
    {
        $cached_directories = array('Chamilo/Libraries/Resources/Configuration');

        file_put_contents($this->cache_file, sprintf('<?php return %s;', var_export($cached_directories, true)));

        $packages_directories_finder =
            new PackagesDirectoryFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file);
        $directories = $packages_directories_finder->findDirectories('Resources/Configuration');

        $this->assertEquals($directories[0], 'Chamilo/Libraries/Resources/Configuration');
        $this->assertEquals(count($directories), 1);
    }

    /**
     * This function test that an invalid cache file (cfr one that does not return an array) throws an exception
     *
     * @expectedException \Exception
     */
    public function testFindDirectoriesWithInvalidCacheFile()
    {
        file_put_contents($this->cache_file, '<?php return 5;');
        $packages_directories_finder =
            new PackagesDirectoryFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file);
        $packages_directories_finder->findDirectories('Resources/Configuration');
    }
}