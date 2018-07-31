<?php

namespace Chamilo\Libraries\Test\Integration\Format\Twig;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Twig\TwigLoaderChamiloFilesystem;

/**
 * Tests the TwigLoaderChamiloFilesystem unit
 *
 * Class TwigLoaderChamiloFilesystemTest
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TwigLoaderChamiloFilesystemTest extends ChamiloTestCase
{
    /**
     * Tests the getCacheKey function, which relies on the protected method find_template
     */
    public function test_get_cache_key()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();
        $this->assertNotEmpty($twig_loader_chamilo_filesystem->getCacheKey('Chamilo\Libraries:Test/test.html.twig'));
    }

    /**
     * Tests the getCacheKey function with an invalid template name which does not include a namespace
     *
     * @expectedException \Twig_Error_Loader
     */
    public function test_get_cache_key_without_namespace_in_template_name()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();
        $twig_loader_chamilo_filesystem->getCacheKey('Test/test.html.twig');
    }

    /**
     * Tests the getCacheKey function with an invalid template name which does not include a valid namespace
     *
     * @expectedException \Twig_Error_Loader
     */
    public function test_get_cache_key_with_invalid_namespace_in_template_name()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();
        $twig_loader_chamilo_filesystem->getCacheKey('invalid_namespace:Test/test.html.twig');
    }

    /**
     * Tests the getCacheKey function with an invalid template name which includes an empty namespace
     *
     * @expectedException \Twig_Error_Loader
     */
    public function test_get_cache_key_with_empty_namespace_in_template_name()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();
        $twig_loader_chamilo_filesystem->getCacheKey(':Test/test.html.twig');
    }

    /**
     * Tests the getCacheKey function with valid namespace but an invalid template path
     *
     * @expectedException \Twig_Error_Loader
     */
    public function test_get_cache_key_with_invalid_template_path_in_template_name()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();
        $twig_loader_chamilo_filesystem->getCacheKey('Chamilo\Libraries:test/invalid.html.twig');
    }

    /**
     * Tests the getCacheKey function with valid namespace but an empty template path
     *
     * @expectedException \Twig_Error_Loader
     */
    public function test_get_cache_key_with_empty_template_path_in_template_name()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();
        $twig_loader_chamilo_filesystem->getCacheKey('Chamilo\Libraries:');
    }

    /**
     * Tests the isFresh method
     */
    public function test_is_fresh()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();
        $this->assertTrue($twig_loader_chamilo_filesystem->isFresh('Chamilo\Libraries:Test/test.html.twig', time()));
    }

    /**
     * Tests that the getSource method returns the correct content
     */
    public function test_get_source()
    {
        $twig_loader_chamilo_filesystem = new TwigLoaderChamiloFilesystem();

        $this->assertEquals(
            file_get_contents(
                Path::getInstance()->getBasePath() . 'Chamilo/Libraries/Resources/Templates/Test/test.html.twig'
            ),
            $twig_loader_chamilo_filesystem->getSourceContext('Chamilo\Libraries:Test/test.html.twig')->getCode()
        );
    }
}