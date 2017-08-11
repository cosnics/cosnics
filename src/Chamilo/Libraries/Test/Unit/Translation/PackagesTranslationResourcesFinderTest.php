<?php

namespace Chamilo\Libraries\Test\Unit\Translation;

use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\Translation\PackagesTranslationResourcesFinder;

/**
 * Tests the PackagesTranslationResourcesFinder
 *
 * @package Chamilo\Libraries\test
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesTranslationResourcesFinderTest extends Test
{
    /**
     * The packages files finder mock
     *
     * @var PackagesFilesFinder | \PHPUnit_Framework_MockObject_MockObject
     */
    private $packages_files_finder;

    /**
     * The packages translation resources finder
     *
     * @var PackagesTranslationResourcesFinder
     */
    private $packages_translation_resources_finder;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->packages_files_finder = $this->createMock(PackagesFilesFinder::class);

        $this->packages_translation_resources_finder = new PackagesTranslationResourcesFinder(
            $this->packages_files_finder
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->packages_files_finder);
        unset($this->packages_translation_resources_finder);
    }

    /**
     * Test the find translation resources
     */
    public function test_find_translation_resources()
    {
        $return_array = array('Chamilo\Libraries' => array('Resources/i18n/en.i18n'));
        $this->mock_package_files_finder_return_value($return_array);

        $translation_resources = $this->packages_translation_resources_finder->findTranslationResources();
        $this->assertEquals($translation_resources['en_EN']['ini']['Chamilo\Libraries'], 'Resources/i18n/en.i18n');
    }

    /**
     * Test the find translation resources with xliff files
     */
    public function test_find_translation_resources_with_xliff_files()
    {
        $return_array = array('Chamilo\Libraries' => array('Resources/i18n/en.xliff'));
        $this->mock_package_files_finder_return_value($return_array);

        $translation_resources = $this->packages_translation_resources_finder->findTranslationResources();
        $this->assertEquals($translation_resources['en_EN']['xliff']['Chamilo\Libraries'], 'Resources/i18n/en.xliff');
    }

    /**
     * Tests the find translation resources with unkonwn files
     */
    public function test_find_translation_resources_with_unknown_files()
    {
        $return_array = array('Chamilo\Libraries' => array('Resources/i18n/en.temp'));
        $this->mock_package_files_finder_return_value($return_array);

        $translation_resources = $this->packages_translation_resources_finder->findTranslationResources();
        $this->assertEquals($translation_resources['en_EN']['unknown']['Chamilo\Libraries'], 'Resources/i18n/en.temp');
    }

    /**
     * Tests the find translation resources with no files
     */
    public function test_find_translation_resources_with_no_files()
    {
        $translation_resources = $this->packages_translation_resources_finder->findTranslationResources();
        $this->assertEmpty($translation_resources);
    }

    /**
     * Mocks the package files finder's return value
     *
     * @param array $return_value
     */
    protected function mock_package_files_finder_return_value($return_value)
    {
        $this->packages_files_finder->expects($this->once())
            ->method('findFiles')
            ->will($this->returnValue($return_value));
    }
}