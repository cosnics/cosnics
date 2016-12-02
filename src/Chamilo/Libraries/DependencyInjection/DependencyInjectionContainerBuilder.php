<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\DataCacheLoader;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Service\RegistrationLoader;
use Chamilo\Configuration\Storage\Repository\RegistrationRepository;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerFactory;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\ConnectionException;
use Chamilo\Libraries\Utilities\StringUtilities;
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
    protected static $container;

    /**
     *
     * @var \Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder
     */
    protected static $instance;

    /**
     * Constructor
     *
     * @param ContainerBuilder $builder
     * @param ContainerExtensionFinderInterface $containerExtensionFinder
     * @param string $cacheFile
     * @param string $cacheClass
     */
    public function __construct(
        ContainerBuilder $builder = null,
        ContainerExtensionFinderInterface $containerExtensionFinder = null, $cacheFile = null,
        $cacheClass = 'ChamiloContainer'
    )
    {
        $this->setBuilder($builder);

        if (is_null($cacheFile))
        {
            $cacheFile = $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__) . '/DependencyInjection.php';
        }

        $this->cacheFile = $cacheFile;
        $this->cacheClass = $cacheClass;
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    /**
     *
     * @return \Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            static::$instance = new static();
        }

        return static::$instance;
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
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    protected function getPathBuilder()
    {
        return new PathBuilder(new ClassnameUtilities($this->getStringUtilities()));
    }

    /**
     *
     * @param ContainerExtensionFinderInterface $containerExtensionFinder
     */
    public function setContainerExtensionFinder(ContainerExtensionFinderInterface $containerExtensionFinder = null)
    {
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    public function getContainerExtensionFinder()
    {
        if (!isset($this->containerExtensionFinder))
        {
            $packageNamespaces = $this->getPackageNamespaces();

            $this->containerExtensionFinder = new PackagesContainerExtensionFinder(
                new PackagesClassFinder($this->getPathBuilder(), $packageNamespaces)
            );
        }

        return $this->containerExtensionFinder;
    }

    /**
     *
     * @return string[]
     */
    protected function getPackageNamespaces()
    {
        $fileConfigurationLocator = $this->getFileConfigurationLocator();

        if ($fileConfigurationLocator->isAvailable())
        {
            try
            {
                return $this->getRegistrationConsulter()->getRegistrationContexts();
            }
            catch (ConnectionException $exception)
            {
                return $this->getPackageNamespacesFromFilesystem();
            }
        }
        else
        {
            return $this->getPackageNamespacesFromFilesystem();
        }
    }

    /**
     *
     * @return string[]
     */
    protected function getPackageNamespacesFromFilesystem()
    {
        $platformPackageBundles = new PlatformPackageBundles();

        return array_keys($platformPackageBundles->get_packages());
    }

    /**
     * Creates and returns the default dependency injection container for Chamilo
     *
     * @return ContainerInterface
     */
    public function createContainer()
    {
        if (static::$container instanceof ContainerInterface)
        {
            return static::$container;
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

        static::$container = $container;

        return $container;
    }

    /**
     * Loads the extensions for the container
     *
     * @param ContainerBuilder $container ;
     */
    protected function loadContainerExtensions(ContainerBuilder $container)
    {
        $extensionClasses = $this->getContainerExtensionFinder()->findContainerExtensions();
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
        if (!is_dir(dirname($cache_file)))
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
        static::$container = null;
    }

    /**
     * Removes the container's cache file from the system
     */
    public function removeContainerCache()
    {
        if (file_exists($this->cacheFile))
        {
            Filesystem::remove($this->cacheFile);

            if (function_exists('opcache_invalidate'))
            {
                opcache_invalidate($this->cacheFile);
            }
        }
    }

    /**
     * Resets and rebuilds the container
     *
     * @param ContainerBuilder $builder
     * @param ContainerExtensionFinderInterface $containerExtensionFinder
     * @param string $cacheFile
     * @param string $cacheClass
     *
     * @return DependencyInjectionContainerBuilder
     */
    public function rebuildContainer(
        ContainerBuilder $builder = null,
        ContainerExtensionFinderInterface $containerExtensionFinder = null, $cacheFile = null,
        $cacheClass = 'ChamiloContainer'
    )
    {
        $this->removeContainerCache();
        $this->clearContainerInstance();

        self::$instance = $newContainer = new self($builder, $containerExtensionFinder, $cacheFile, $cacheClass);
        $newContainer->createContainer();

        return $newContainer;
    }

    /**
     * DEPENDENCIES
     */

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    private $fileConfigurationLocator;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $fileConfigurationConsulter;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    protected function getFileConfigurationLocator()
    {
        return new FileConfigurationLocator($this->getPathBuilder());
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected function getFileConfigurationConsulter()
    {
        return new ConfigurationConsulter(new FileConfigurationLoader($this->getFileConfigurationLocator()));
            );
    }

    /**
     *
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected function getConfigurablePathBuilder()
    {
        $fileConfigurationConsulter = $this->getFileConfigurationConsulter();

        $configurablePathBuilder = new ConfigurablePathBuilder(
            $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'storage')));
            );

        return $configurablePathBuilder;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    protected function getRegistrationConsulter()
    {
        $connectionFactory = new ConnectionFactory(
            new DataSourceName(
                $this->getFileConfigurationConsulter()->getSetting(array('Chamilo\Configuration', 'database'))));
                )
            );

        $exceptionLoggerFactory = new ExceptionLoggerFactory($this->getFileConfigurationConsulter());

        $registrationConsulter = new RegistrationConsulter(
            $this->getStringUtilities(),
            new DataCacheLoader(
                new RegistrationLoader(
                    $this->getStringUtilities(),
                    new RegistrationRepository(
                        new DataClassRepository(
                            new DataClassRepositoryCache(),
                            new DataClassDatabase(
                                $connectionFactory->getConnection(),
                                new StorageAliasGenerator($this->getClassnameUtilities()),
                                $exceptionLoggerFactory->createExceptionLogger(),
                                new ConditionPartTranslatorService(
                                    new ConditionPartTranslatorFactory($this->getClassnameUtilities()),
                                    new ConditionPartCache(),
                                    $this->getFileConfigurationConsulter()->getSetting(
                                        array('Chamilo\Configuration', 'debug', 'enable_query_cache')))),
                            new DataClassFactory())))));
                                    )
                                ),
                                new DataClassFactory()
                            )
                        )
                    )
                )
            );

        return $registrationConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    protected function getClassnameUtilities()
    {
        return new ClassnameUtilities($this->getStringUtilities());
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    protected function getStringUtilities()
    {
        return new StringUtilities();
    }
}