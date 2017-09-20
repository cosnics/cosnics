<?php
namespace Chamilo\Libraries\Test\Unit\Translation;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Translation\PackagesTranslationResourcesFinder;
use Chamilo\Libraries\Translation\TranslationResourcesFinderInterface;
use Chamilo\Libraries\Translation\TranslationResourcesOptimizer;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Tests the TranslationResourcesOptimizer class
 *
 * @package Chamilo\Libraries\test
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TranslationResourcesOptimizerTest extends ChamiloTestCase
{

    /**
     * The mocks of the translation loaders
     *
     * @var LoaderInterface[] | \PHPUnit_Framework_MockObject_MockObject[]
     */
    private $translationLoadersMocks;

    /**
     * The mock for the translation resources finder
     *
     * @var TranslationResourcesFinderInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $translationResourcesFinderMock;

    /**
     * The cache directory
     *
     * @var string
     */
    private $cache_path;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->translationLoadersMocks = array(
            'ini' => $this->createMock('Symfony\Component\Translation\Loader\IniFileLoader'));

        $this->translationResourcesFinderMock = $this->createMock(PackagesTranslationResourcesFinder::class);

        $this->cache_path = __DIR__ . '/cache';

        mkdir($this->cache_path);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->translationLoadersMocks);
        unset($this->translationResourcesFinderMock);

        Filesystem::remove($this->cache_path);
        unset($this->cache_path);
    }

    /**
     * Tests to create the class
     */
    public function test_create_class()
    {
        new TranslationResourcesOptimizer(
            $this->translationLoadersMocks,
            $this->translationResourcesFinderMock,
            $this->cache_path);

        $this->assertTrue(true);
    }

    /**
     * Tests to create the class without translation loaders
     * @expectedException \InvalidArgumentException
     */
    public function test_create_class_without_translation_loaders()
    {
        new TranslationResourcesOptimizer(array(), $this->translationResourcesFinderMock, $this->cache_path);
    }

    /**
     * Tests to create the class with invalid translation loaders
     * @expectedException \InvalidArgumentException
     */
    public function test_create_class_with_invalid_translation_loaders()
    {
        new TranslationResourcesOptimizer(
            array(new \stdClass()),
            $this->translationResourcesFinderMock,
            $this->cache_path);
    }

    /**
     * Tests to create the class with an invalid cache path
     * @expectedException \InvalidArgumentException
     */
    public function test_create_class_with_invalid_cache_path()
    {
        new TranslationResourcesOptimizer($this->translationLoadersMocks, $this->translationResourcesFinderMock, null);
    }

    /**
     * Test the get_optimized_translation_resources function
     */
    public function test_get_optimized_translation_resources()
    {
        $return_value['en_EN']['ini']['Chamilo\Libraries'] = 'en_EN.php';

        $this->translationResourcesFinderMock->expects($this->once())->method('findTranslationResources')->will(
            $this->returnValue($return_value));

        $messages = array('Hello' => 'Welkom', 'HowAreYou' => 'Hoe gaat het met je');
        $message_catalogue = new MessageCatalogue('en_EN');
        $message_catalogue->add($messages, 'Chamilo\Libraries');

        $this->translationLoadersMocks['ini']->expects($this->once())->method('load')->will(
            $this->returnValue($message_catalogue));

        $translation_resources_optimizer = new TranslationResourcesOptimizer(
            $this->translationLoadersMocks,
            $this->translationResourcesFinderMock,
            $this->cache_path);

        $resources = $translation_resources_optimizer->getOptimizedTranslationResources();

        $this->assertEquals($resources['en_EN'], $this->cache_path . '/en_EN.php');

        $cached_messages = require ($this->cache_path . '/en_EN.php');
        $this->assertEquals($messages, $cached_messages['Chamilo\Libraries']);
    }

    /**
     * Test the get_optimized_translation_resources function with cached resources
     */
    public function test_get_optimized_translation_resources_with_cached_resources()
    {
        $cache_file = $this->cache_path . '/locale.php';
        $resources = array('en_EN', 'nl_NL', 'de_DE');

        file_put_contents($cache_file, "<?php\n\nreturn " . var_export($resources, true) . ";\n");

        $translation_resources_optimizer = new TranslationResourcesOptimizer(
            $this->translationLoadersMocks,
            $this->translationResourcesFinderMock,
            $this->cache_path);

        $resources = $translation_resources_optimizer->getOptimizedTranslationResources();

        $this->assertEquals($resources['de_DE'], $this->cache_path . '/de_DE.php');
    }

    /**
     * Tests the get_optimized_translation_resources function with invalid resources that can not be loaded by
     * the given loaders
     * @expectedException \InvalidArgumentException
     */
    public function test_get_optimized_translation_resources_with_invalid_resources()
    {
        $return_value['en_EN']['xml']['Chamilo\Libraries'] = 'en_EN.xml';

        $this->translationResourcesFinderMock->expects($this->once())->method('findTranslationResources')->will(
            $this->returnValue($return_value));

        $translation_resources_optimizer = new TranslationResourcesOptimizer(
            $this->translationLoadersMocks,
            $this->translationResourcesFinderMock,
            $this->cache_path);

        $translation_resources_optimizer->getOptimizedTranslationResources();
    }
}
