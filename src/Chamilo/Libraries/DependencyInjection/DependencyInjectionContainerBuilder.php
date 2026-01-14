<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Service\DataLoader\FileConfigurationCacheDataPreLoader;
use Chamilo\Configuration\Service\DataLoader\RegistrationCacheDataPreLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Configuration\Service\Finder\PackageBundlesGenerator;
use Chamilo\Configuration\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Service\PackageFactory;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Configuration\Storage\Repository\RegistrationRepository;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\FileExceptionLogger;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\Implementations\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\ConnectionFactory;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\AndConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\ComparisonConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\EqualityConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\InConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\NotConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\OrConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\PatternMatchConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\RegularExpressionConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Condition\SubselectConditionTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\CaseConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\CaseElementConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\DateFormatConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\DistinctConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\FunctionConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\OperationConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\PropertiesConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\PropertyConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\Query\Variable\StaticConditionVariableTranslator;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\QueryBuilderConfigurator;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Service\StorageAliasGenerator;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Builds the default dependency injection container for Chamilo
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionContainerBuilder
{

    private static ?ContainerInterface $container = null;

    private static ?DependencyInjectionContainerBuilder $instance = null;

    protected ConnectionFactory $connectionFactory;

    protected Filesystem $filesystem;

    protected ChamiloRequest $request;

    protected SystemPathBuilder $systemPathBuilder;

    protected WebPathBuilder $webPathBuilder;

    private ?ContainerBuilder $builder;

    private ?string $cacheClass;

    private ?string $cacheFile;

    private ClassnameUtilities $classnameUtilities;

    private ?ContainerExtensionFinderInterface $containerExtensionFinder;

    private ConfigurationConsulter $fileConfigurationConsulter;

    private FileConfigurationLocator $fileConfigurationLocator;

    private RegistrationConsulter $registrationConsulter;

    private StringUtilities $stringUtilities;

    public function __construct(
        ?ContainerBuilder $builder = null, ?ContainerExtensionFinderInterface $containerExtensionFinder = null,
        ?string $cacheFile = null, string $cacheClass = 'ChamiloContainer'
    )
    {
        $this->setBuilder($builder);

        if (is_null($cacheFile))
        {
            $cacheFile = $this->getDefaultCacheFilePath();
        }

        $this->cacheFile = $cacheFile;
        $this->cacheClass = $cacheClass;
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    protected function cacheContainer(ContainerBuilder $container, string $cacheFile): void
    {
        if (!is_dir(dirname($cacheFile)))
        {
            $this->getFilesystem()->mkdir(dirname($cacheFile));
        }

        $dumper = new PhpDumper($container);
        file_put_contents($cacheFile, $dumper->dump(['class' => $this->cacheClass]));
    }

    public function clearContainerInstance(): void
    {
        self::$container = null;
    }

    /**
     * Creates and returns the default dependency injection container for Chamilo
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     */
    public function createContainer(): ContainerInterface
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

    protected function getClassnameUtilities(): ClassnameUtilities
    {
        if (!isset($this->classnameUtilities))
        {
            $this->classnameUtilities = new ClassnameUtilities($this->getStringUtilities());
        }

        return $this->classnameUtilities;
    }

    protected function getConnectionFactory(): ConnectionFactory
    {
        if (!isset($this->connectionFactory))
        {
            $this->connectionFactory = new ConnectionFactory(
                new DataSourceName(
                    $this->getFileConfigurationConsulter()->getSetting(['Chamilo\Configuration', 'database'])
                )
            );
        }

        return $this->connectionFactory;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     */
    public function getContainerExtensionFinder(): ContainerExtensionFinderInterface
    {
        if (!isset($this->containerExtensionFinder))
        {
            $packageNamespaces = $this->getPackageNamespaces();

            $this->containerExtensionFinder = new PackagesContainerExtensionFinder(
                new PackagesClassFinder($this->getSystemPathBuilder(), $packageNamespaces)
            );
        }

        return $this->containerExtensionFinder;
    }

    public function setContainerExtensionFinder(?ContainerExtensionFinderInterface $containerExtensionFinder = null
    ): void
    {
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    protected function getDefaultCacheFilePath(): string
    {
        return realpath(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'symfony' . DIRECTORY_SEPARATOR .
            'DependencyInjection.php';
    }

    protected function getDefaultLogsPath(): string
    {
        return realpath(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
    }

    protected function getFileConfigurationConsulter(): ConfigurationConsulter
    {
        if (!isset($this->fileConfigurationConsulter))
        {
            $this->fileConfigurationConsulter = new ConfigurationConsulter(
                new FileConfigurationCacheDataPreLoader(new ArrayAdapter(), $this->getFileConfigurationLocator())
            );
        }

        return $this->fileConfigurationConsulter;
    }

    protected function getFileConfigurationLocator(): FileConfigurationLocator
    {
        if (!isset($this->fileConfigurationLocator))
        {
            $this->fileConfigurationLocator = new FileConfigurationLocator($this->getSystemPathBuilder());
        }

        return $this->fileConfigurationLocator;
    }

    protected function getFilesystem(): Filesystem
    {
        if (!isset($this->filesystem))
        {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    public static function getInstance(): DependencyInjectionContainerBuilder
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     */
    protected function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        $packageFactory = new PackageFactory($this->getSystemPathBuilder(), $this->getFilesystem());

        $packageBundlesGenerator = new PackageBundlesGenerator(
            $this->getSystemPathBuilder(), $this->getClassnameUtilities(), $packageFactory,
            $this->getRegistrationConsulter()
        );

        return new PackageBundlesCacheService(new ArrayAdapter(), $packageBundlesGenerator);
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     */
    protected function getPackageNamespaces(): array
    {
        $fileConfigurationLocator = $this->getFileConfigurationLocator();

        if ($fileConfigurationLocator->isAvailable())
        {
            try
            {
                return $this->getRegistrationConsulter()->getRegistrationContexts();
            }
            catch (Exception)
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
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     */
    protected function getPackageNamespacesFromFilesystem(): array
    {
        $nestedPackages = $this->getPackageBundlesCacheService()->getAllPackages()->getNestedPackages();

        return array_keys($nestedPackages);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     * @throws \Exception
     */
    protected function getRegistrationConsulter(): RegistrationConsulter
    {
        if (!isset($this->registrationConsulter))
        {
            $connectionFactory = $this->getConnectionFactory();

            $storageAliasGenerator = new StorageAliasGenerator($this->getClassnameUtilities());

            $conditionPartTranslatorService = new ConditionPartTranslatorService(new ConditionPartCache(), false);

            $conditionPartTranslatorService->addConditionPartTranslator(
                new CaseConditionVariableTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new CaseElementConditionVariableTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new ComparisonConditionTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new EqualityConditionTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new DateFormatConditionVariableTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new DistinctConditionVariableTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new FunctionConditionVariableTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new InConditionTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new AndConditionTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new OrConditionTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new NotConditionTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new OperationConditionVariableTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new PatternMatchConditionTranslator($conditionPartTranslatorService, $storageAliasGenerator)
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new PropertiesConditionVariableTranslator(
                    $conditionPartTranslatorService, $storageAliasGenerator
                )
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new PropertyConditionVariableTranslator(
                    $conditionPartTranslatorService, $storageAliasGenerator
                )
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new RegularExpressionConditionTranslator(
                    $conditionPartTranslatorService, $storageAliasGenerator
                )
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new StaticConditionVariableTranslator(
                    $conditionPartTranslatorService, $storageAliasGenerator
                )
            );
            $conditionPartTranslatorService->addConditionPartTranslator(
                new SubselectConditionTranslator(
                    $conditionPartTranslatorService, $storageAliasGenerator
                )
            );

            $dataClassRepositoryCache = new DataClassRepositoryCache();

            $dataClassRepository = new DataClassRepository(
                $dataClassRepositoryCache, new DataClassDatabase(
                $connectionFactory->getConnection(), $storageAliasGenerator,
                new FileExceptionLogger($this->getDefaultLogsPath()), $conditionPartTranslatorService,
                new QueryBuilderConfigurator($conditionPartTranslatorService, $storageAliasGenerator)
            ), new DataClassFactory()
            );

            $this->registrationConsulter = new RegistrationConsulter(
                new RegistrationCacheDataPreLoader(
                    new ArrayAdapter(), $this->getStringUtilities(), new RegistrationService(
                        new RegistrationRepository($dataClassRepository), new ArrayAdapter()
                    )
                ), $this->getStringUtilities()
            );
        }

        return $this->registrationConsulter;
    }

    protected function getRequest(): ChamiloRequest
    {
        if (!isset($this->request))
        {
            $this->request = ChamiloRequest::createFromGlobals();
        }

        return $this->request;
    }

    protected function getStringUtilities(): StringUtilities
    {
        if (!isset($this->stringUtilities))
        {
            $this->stringUtilities = new StringUtilities();
        }

        return $this->stringUtilities;
    }

    protected function getSystemPathBuilder(): SystemPathBuilder
    {
        if (!isset($this->systemPathBuilder))
        {
            $this->systemPathBuilder = new SystemPathBuilder(
                new ClassnameUtilities($this->getStringUtilities())
            );
        }

        return $this->systemPathBuilder;
    }

    protected function getWebPathBuilder(): WebPathBuilder
    {
        if (!isset($this->webPathBuilder))
        {
            $this->webPathBuilder = new WebPathBuilder(
                new ClassnameUtilities($this->getStringUtilities()), $this->getRequest()
            );
        }

        return $this->webPathBuilder;
    }

    /**
     * Loads the extensions for the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     */
    protected function loadContainerExtensions(ContainerBuilder $container): void
    {
        $extensionClasses = $this->getContainerExtensionFinder()->findContainerExtensions();
        $extensions = [];

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
            if ($extension instanceof ICompilerPassExtension)
            {
                /** @var ICompilerPassExtension $extension */
                $extension->registerCompilerPasses($container);
            }
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function rebuildContainer(
        ?ContainerBuilder $builder = null, ?ContainerExtensionFinderInterface $containerExtensionFinder = null,
        ?string $cacheFile = null, string $cacheClass = 'ChamiloContainer'
    ): DependencyInjectionContainerBuilder
    {
        $this->removeContainerCache();
        $this->clearContainerInstance();

        self::$instance = $newContainer = new self($builder, $containerExtensionFinder, $cacheFile, $cacheClass);
        $newContainer->createContainer();

        return $newContainer;
    }

    public function removeContainerCache(): void
    {
        if (file_exists($this->cacheFile))
        {
            $this->getFilesystem()->remove($this->cacheFile);

            if (function_exists('opcache_invalidate'))
            {
                opcache_invalidate($this->cacheFile);
            }
        }
    }

    public function setBuilder(?ContainerBuilder $builder = null): void
    {
        $this->builder = $builder;
    }
}