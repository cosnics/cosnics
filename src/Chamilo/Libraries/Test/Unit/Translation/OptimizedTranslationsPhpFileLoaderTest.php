<?php

namespace Chamilo\Libraries\Test\Unit\Translation;

use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\Translation\OptimizedTranslationsPhpFileLoader;

/**
 * Tests the OptimizedTranslationsPhpFileLoader class
 *
 * Class OptimizedTranslationsPhpFileLoaderTest
 *
 * @package common\libraries\test
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OptimizedTranslationsPhpFileLoaderTest extends Test
{
    /**
     * Tests the load function
     */
    public function testLoad()
    {
        $resource_file = __DIR__ . '/en_EN.php';
        $messages_array = array('Hello' => 'Welkom', 'HowAreYou' => 'Hoe gaat het met je');
        file_put_contents($resource_file, sprintf('<?php return %s;', var_export($messages_array, true)));

        $loader = new OptimizedTranslationsPhpFileLoader();
        $message_catalogue = $loader->load($resource_file, 'en_EN');

        $this->assertEquals($message_catalogue->all(), $messages_array);

        unlink($resource_file);
    }

    /**
     * Tests the load function with an inexisting resource file
     *
     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
     */
    public function test_load_with_inexisting_resource_file()
    {
        $loader = new OptimizedTranslationsPhpFileLoader();
        $loader->load('test.php', 'en_EN');
    }

    /**
     * Tests the load function with an external stream ('url')
     *
     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
     */
    public function test_load_with_invalid_resource_file()
    {
        $loader = new OptimizedTranslationsPhpFileLoader();
        $loader->load('http://www.chamilo.org', 'en_EN');
    }
}