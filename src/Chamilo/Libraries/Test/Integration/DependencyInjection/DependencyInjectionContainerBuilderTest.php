<?php
namespace Chamilo\Libraries\Test\Integration\DependencyInjection;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Builds the default dependency injection container for Chamilo
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionContainerBuilderTest extends ChamiloTestCase
{
    /**
     * Tests the createContainer function
     */
    public function testCreateContainer()
    {
        $containerBuilder = new DependencyInjectionContainerBuilder();
        $container = $containerBuilder->createContainer();

        $this->assertInstanceOf('\Symfony\Component\DependencyInjection\ContainerInterface', $container);
    }

    /**
     * Tests the createContainer function with a new cache file
     */
    public function testCreateContainerWithNewCacheFile()
    {
        $cacheFile = sys_get_temp_dir() . '/dependency_injection.php';

        $containerBuilder = new DependencyInjectionContainerBuilder(null, null, $cacheFile);
        $containerBuilder->clearContainerInstance();
        $container = $containerBuilder->createContainer();

        $this->assertInstanceOf('\Symfony\Component\DependencyInjection\ContainerInterface', $container);

        $filesystem = new Filesystem();
        $filesystem->remove($cacheFile);
    }
}