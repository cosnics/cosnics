<?php

namespace Chamilo\Libraries\Test\Unit\File\PackagesContentFinder;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;

/**
 * Tests the packages files finder
 *
 * @package Chamilo\Libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesFilesFinderTest extends ChamiloTestCase
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
     * Tests the find files in an existing example package (common/libraries)
     */
    public function testFindFilesInPackage()
    {
        $packages_files_finder = new PackagesFilesFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'));
        $files = $packages_files_finder->findFiles('Resources/Test/PackagesContentFinder');

        $this->assertEquals(count($files['Chamilo\Libraries']), 3);
    }

    /**
     * Tests the find files without packages, must return an empty array
     */
    public function testFindFilesWithoutPackages()
    {
        $packages_files_finder = new PackagesFilesFinder(PathBuilder::getInstance());
        $files = $packages_files_finder->findFiles('Resources/Test/PackagesContentFinder');

        $this->assertEmpty($files);
    }

    /**
     * Tests that the package files finder only returns existing files
     */
    public function testFindInexistingFiles()
    {
        $packages_files_finder = new PackagesFilesFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'));
        $files = $packages_files_finder->findFiles('Resources/Test/PackagesContentFinder', '*.php');

        $this->assertEmpty($files);
    }

    /**
     * Tests that the package files finder only returns files in existing folders
     */
    public function testFindFilesWithInexistingDirectory()
    {
        $packages_files_finder = new PackagesFilesFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'));
        $files = $packages_files_finder->findFiles('Resources/Test/PackagesContentFinderInexisting');

        $this->assertEmpty($files);
    }

    /**
     * Tests that the find files writes it's list to an existing cache file
     */
    public function testFindFilesWithCacheFile()
    {
        $packages_files_finder =
            new PackagesFilesFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file);
        $files = $packages_files_finder->findFiles('Resources/Test/PackagesContentFinder');

        $cached_files = require $this->cache_file;

        $this->assertEquals($files, $cached_files);
    }

    /**
     * Tests that the find files can use an existing cache file
     */
    public function testFindFilesWithExistingCacheFile()
    {
        $cached_files = array('test.txt');

        file_put_contents($this->cache_file, sprintf('<?php return %s;', var_export($cached_files, true)));

        $packages_files_finder =
            new PackagesFilesFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file);
        $files = $packages_files_finder->findFiles('Resources/Test/PackagesContentFinder');

        $this->assertEquals($files[0], 'test.txt');
        $this->assertEquals(count($files), 1);
    }

    /**
     * This function test that an invalid cache file (cfr one that does not return an array) throws an exception
     *
     * @expectedException \Exception
     */
    public function testFindFilesWithInvalidCacheFile()
    {
        file_put_contents($this->cache_file, '<?php return 5;');
        $packages_files_finder =
            new PackagesFilesFinder(PathBuilder::getInstance(), array('Chamilo\Libraries'), $this->cache_file);
        $packages_files_finder->findFiles('Resources/Test/PackagesContentFinder');
    }
}