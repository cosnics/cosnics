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
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $builder;

    /**
     * The container extension finder
     *
     * @var \Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface
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
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private static $container;

    /**
     *
     * @var \Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    private $fileConfigurationLocator;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $fileConfigurationConsulter;

    /**
     *
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    private $configurablePathBuilder;

    /**
     *
     * @var \Chamilo\Configuration\Service\RegistrationConsulter
     */
    private $registrationConsulter;

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
     * Constructor
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $builder
     * @param \Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface $containerExtensionFinder
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
            self::$instance = new static();
        }

        return self::$instance;
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
        if (!isset($this->pathBuilder))
        {
            $this->pathBuilder = new PathBuilder(new ClassnameUtilities($this->getStringUtilities()));
        }

        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface $containerExtensionFinder
     */
    public function setContainerExtensionFinder(ContainerExtensionFinderInterface $containerExtensionFinder = null)
    {
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    /**
     *
     * @return \Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface
     */
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
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
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
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
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
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $cacheFile
     */
    protected function cacheContainer(ContainerBuilder $container, $cacheFile)
    {
        if (!is_dir(dirname($cacheFile)))
        {
            Filesystem::create_dir(dirname($cacheFile));
        }

        $dumper = new PhpDumper($container);
        file_put_contents($cacheFile, $dumper->dump(array('class' => $this->cacheClass)));
    }

    /**
     * Clears the container instance
     */
    public function clearContainerInstance()
    {
        self::$container = null;
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

        $this->clearContainerInstance();
    }

    /**
     * Resets and rebuilds the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $builder
     * @param \Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface $containerExtensionFinder
     * @param string $cacheFile
     * @param string $cacheClass
     *
     * @return \Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder
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
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    protected function getFileConfigurationLocator()
    {
        if (!isset($this->fileConfigurationLocator))
        {
            $this->fileConfigurationLocator = new FileConfigurationLocator($this->getPathBuilder());
        }

        return $this->fileConfigurationLocator;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected function getFileConfigurationConsulter()
    {
        if (!isset($this->fileConfigurationConsulter))
        {
            $this->fileConfigurationConsulter = new ConfigurationConsulter(
                new FileConfigurationLoader($this->getFileConfigurationLocator())
            );
        }

        return $this->fileConfigurationConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected function getConfigurablePathBuilder()
    {
        if (!isset($this->configurablePathBuilder))
        {
            $fileConfigurationConsulter = $this->getFileConfigurationConsulter();

            $this->configurablePathBuilder = new ConfigurablePathBuilder(
                $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'storage'))
            );
        }

        return $this->configurablePathBuilder;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    protected function getRegistrationConsulter()
    {
        if (!isset($this->registrationConsulter))
        {
            $connectionFactory = new ConnectionFactory(
                new DataSourceName(
                    $this->getFileConfigurationConsulter()->getSetting(array('Chamilo\Configuration', 'database'))
                )
            );

            $exceptionLoggerFactory = new ExceptionLoggerFactory($this->getFileConfigurationConsulter());

            $this->registrationConsulter = new RegistrationConsulter(
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
                                            array('Chamilo\Configuration', 'debug', 'enable_query_cache')
                                        )
                                    )
                                ),
                                new DataClassFactory()
                            )
                        )
                    )
                )
            );
        }

        return $this->registrationConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    protected function getClassnameUtilities()
    {
        if (!isset($this->classnameUtilities))
        {
            $this->classnameUtilities = new ClassnameUtilities($this->getStringUtilities());
        }

        return $this->classnameUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    protected function getStringUtilities()
    {
        if (!isset($this->stringUtilities))
        {
            $this->stringUtilities = new StringUtilities();
        }

        return $this->stringUtilities;
    }
}
