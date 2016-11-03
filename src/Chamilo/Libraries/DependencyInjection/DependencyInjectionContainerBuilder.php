<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * Builds the default dependency injection container for Chamilo
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionContainerBuilder
{

    /**
     * The container builder
     *
     * @var ContainerBuilder
     */
    private $builder;

    /**
     * The container extension finder
     *
     * @var ContainerExtensionFinderInterface
     */
    private $containerExtensionFinder;

    /**
     * The path to the cache file
     *
     * @var string
     */
    private $cacheFile;

    /**
     * The classname of the cached container
     *
     * @var string
     */
    private $cacheClass;

    /**
     * Cache the container over requests due to issues with the container not being available everywhere
     *
     * @var ContainerInterface
     */
    private static $container;

    /**
     * Constructor
     *
     * @param ContainerBuilder $builder
     * @param ContainerExtensionFinderInterface $containerExtensionFinder
     * @param string $cacheFile
     * @param string $cacheClass
     */
    public function __construct(ContainerBuilder $builder = null,
        ContainerExtensionFinderInterface $containerExtensionFinder = null, $cacheFile = null, $cacheClass = 'ChamiloContainer')
    {
        $this->setBuilder($builder);
        $this->setContainerExtensionFinder($containerExtensionFinder);

        if (is_null($cacheFile))
        {
            $cacheFile = Path::getInstance()->getCachePath(__NAMESPACE__) . '/DependencyInjection.php';
        }

        $this->cacheFile = $cacheFile;
        $this->cacheClass = $cacheClass;
    }

    /**
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $builder
     */
    public function setBuilder(ContainerBuilder $builder = null)
    {
        $this->builder = $builder;
    }

    /**
     *
     * @param ContainerExtensionFinderInterface $containerExtensionFinder
     */
    public function setContainerExtensionFinder(ContainerExtensionFinderInterface $containerExtensionFinder = null)
    {
        if (is_null($containerExtensionFinder))
        {
            $packageNamespaces = Configuration::get_instance()->get_registration_contexts();

            $containerExtensionFinder = new PackagesContainerExtensionFinder(
                new PackagesClassFinder(Path::getInstance(), $packageNamespaces));
        }

        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    /**
     * Creates and returns the default dependency injection container for Chamilo
     *
     * @return ContainerInterface
     */
    public function createContainer()
    {
        if (self::$container instanceof ContainerInterface)
        {
            return self::$container;
        }

        if (file_exists($this->cacheFile))
        {
            require_once $this->cacheFile;
            $container = new $this->cacheClass();
        }
        else
        {
            $container = $this->builder ?: new ContainerBuilder();
            $this->loadContainerExtensions($container);
            $container->compile();

            $this->cacheContainer($container, $this->cacheFile);
        }

        self::$container = $container;

        return $container;
    }

    /**
     * Loads the extensions for the container
     *
     * @param ContainerBuilder $container;
     */
    protected function loadContainerExtensions(ContainerBuilder $container)
    {
        $extensionClasses = $this->containerExtensionFinder->findContainerExtensions();
        $extensions = array();

        foreach ($extensionClasses as $extensionClass)
        {
            /** @var \Symfony\Component\DependencyInjection\Extension\ExtensionInterface $extension */
            $extension = new $extensionClass();

            $container->registerExtension($extension);
            $container->loadFromExtension($extension->getAlias());

            $extensions[] = $extension;
        }

        foreach ($extensions as $extension)
        {
            if ($extension instanceof IConfigurableExtension)
            {
                /** @var IConfigurableExtension $extension */
                $extension->loadContainerConfiguration($container);
            }

            if ($extension instanceof ICompilerPassExtension)
            {
                /** @var ICompilerPassExtension $extension */
                $extension->registerCompilerPasses($container);
            }
        }
    }

    /**
     * Caches the container into a given cache file
     *
     * @param ContainerBuilder $container
     * @param string $cache_file
     */
    protected function cacheContainer(ContainerBuilder $container, $cache_file)
    {
        if (! is_dir(dirname($cache_file)))
        {
            Filesystem::create_dir(dirname($cache_file));
        }

        $dumper = new PhpDumper($container);
        file_put_contents($cache_file, $dumper->dump(array('class' => $this->cacheClass)));
    }

    /**
     * Clears the container instance
     */
    public function clearContainerInstance()
    {
        self::$container = null;
    }
}