<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Service\DataLoader\FileConfigurationCacheDataPreLoader;
use Chamilo\Configuration\Service\DataLoader\RegistrationCacheDataPreLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Configuration\Storage\Repository\RegistrationRepository;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerFactory;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\IConfigurableExtension;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\PdoSessionHandlerFactory;
use Chamilo\Libraries\Platform\Session\SessionFactory;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\CaseConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\CaseElementConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\ComparisonConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\DateFormatConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\DistinctConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\FunctionConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\InConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\MultipleAggregateConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\NotConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\OperationConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\PatternMatchConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\PropertiesConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\PropertyConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\RegularExpressionConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\StaticConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ConditionPart\SubselectConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\RecordProcessor;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\ConnectionException;
use Chamilo\Libraries\Storage\Service\ParametersHandler;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

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

    protected ChamiloRequest $request;

    protected SystemPathBuilder $systemPathBuilder;

    protected WebPathBuilder $webPathBuilder;

    private ?ContainerBuilder $builder;

    private ?string $cacheClass;

    private ?string $cacheFile;

    private ClassnameUtilities $classnameUtilities;

    private ConfigurablePathBuilder $configurablePathBuilder;

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
            $cacheFile = $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__) . '/DependencyInjection.php';
        }

        $this->cacheFile = $cacheFile;
        $this->cacheClass = $cacheClass;
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    protected function cacheContainer(ContainerBuilder $container, string $cacheFile): void
    {
        if (!is_dir(dirname($cacheFile)))
        {
            Filesystem::create_dir(dirname($cacheFile));
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
     * @throws \Exception
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

    protected function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        if (!isset($this->configurablePathBuilder))
        {
            $fileConfigurationConsulter = $this->getFileConfigurationConsulter();

            $this->configurablePathBuilder = new ConfigurablePathBuilder(
                $fileConfigurationConsulter->getSetting(['Chamilo\Configuration', 'storage'])
            );
        }

        return $this->configurablePathBuilder;
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
     * @throws \Exception
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

    public static function getInstance(): DependencyInjectionContainerBuilder
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return string[]
     * @throws \Exception
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
     * @return string[]
     */
    protected function getPackageNamespacesFromFilesystem(): array
    {
        $platformPackageBundles = new PlatformPackageBundles();

        return array_keys($platformPackageBundles->get_packages());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Exception
     */
    protected function getRegistrationConsulter(): RegistrationConsulter
    {
        if (!isset($this->registrationConsulter))
        {
            $connectionFactory = $this->getConnectionFactory();

            $storageAliasGenerator = new StorageAliasGenerator($this->getClassnameUtilities());

            $conditionPartTranslatorService = new ConditionPartTranslatorService(
                new CaseConditionVariableTranslator($storageAliasGenerator),
                new CaseElementConditionVariableTranslator($storageAliasGenerator),
                new ComparisonConditionTranslator($storageAliasGenerator),
                new DateFormatConditionVariableTranslator($storageAliasGenerator),
                new DistinctConditionVariableTranslator($storageAliasGenerator),
                new FunctionConditionVariableTranslator($storageAliasGenerator),
                new InConditionTranslator($storageAliasGenerator),
                new MultipleAggregateConditionTranslator($storageAliasGenerator),
                new NotConditionTranslator($storageAliasGenerator),
                new OperationConditionVariableTranslator($storageAliasGenerator),
                new PatternMatchConditionTranslator($storageAliasGenerator),
                new PropertiesConditionVariableTranslator($storageAliasGenerator),
                new PropertyConditionVariableTranslator($storageAliasGenerator),
                new RegularExpressionConditionTranslator($storageAliasGenerator),
                new StaticConditionVariableTranslator($storageAliasGenerator),
                new SubselectConditionTranslator($storageAliasGenerator), new ConditionPartCache(),
                $this->getFileConfigurationConsulter()->getSetting(
                    ['Chamilo\Configuration', 'debug', 'enable_query_cache']
                )
            );

            $exceptionLoggerFactory = new ExceptionLoggerFactory(
                $this->getFileConfigurationConsulter(), new SessionUtilities(
                $this->getSession(), $this->getFileConfigurationConsulter()->getSetting(
                ['Chamilo\Configuration', 'general', 'security_key']
            )
            ), new UrlGenerator($this->getRequest(), $this->getWebPathBuilder())
            );

            $dataClassRepositoryCache = new DataClassRepositoryCache();

            $dataClassRepository = new DataClassRepository(
                $dataClassRepositoryCache, new DataClassDatabase(
                $connectionFactory->getConnection(), $storageAliasGenerator,
                $exceptionLoggerFactory->createExceptionLogger(), $conditionPartTranslatorService,
                new ParametersProcessor($conditionPartTranslatorService, $storageAliasGenerator), new RecordProcessor()
            ), new DataClassFactory(), new ParametersHandler()
            );

            $this->registrationConsulter = new RegistrationConsulter(
                new RegistrationCacheDataPreLoader(
                    new PhpFilesAdapter(
                        md5('Chamilo\Configuration\Service\Registration'), 0,
                        $this->getConfigurablePathBuilder()->getConfiguredCachePath()
                    ), $this->getStringUtilities(), new RegistrationRepository($dataClassRepository)
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

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    protected function getSession(): Session
    {
        $securityKey =
            $this->getFileConfigurationConsulter()->getSetting(['Chamilo\Configuration', 'general', 'security_key']);

        $pdoSessionHandlerFactory = new PdoSessionHandlerFactory($this->getConnectionFactory()->getConnection());
        $nativeSessionStorage = new NativeSessionStorage([], $pdoSessionHandlerFactory->getPdoSessionHandler());
        $sessionFactory = new SessionFactory($nativeSessionStorage, $securityKey);

        return $sessionFactory->getSession();
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
     * @throws \Exception
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
     * @throws \Exception
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
            Filesystem::remove($this->cacheFile);

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

    public function setContainerExtensionFinder(?ContainerExtensionFinderInterface $containerExtensionFinder = null
    ): void
    {
        $this->containerExtensionFinder = $containerExtensionFinder;
    }
}