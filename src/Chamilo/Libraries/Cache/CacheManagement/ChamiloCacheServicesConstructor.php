<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Application\Calendar\Extension\Google\Service\EventsCacheService;
use Chamilo\Application\Calendar\Extension\Google\Service\OwnedCalendarsCacheService;
use Chamilo\Configuration\Package\Service\InternationalizationBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\DataCacheLoader;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Configuration\Service\LanguageLoader;
use Chamilo\Configuration\Service\RegistrationLoader;
use Chamilo\Configuration\Service\StorageConfigurationLoader;
use Chamilo\Configuration\Storage\Repository\ConfigurationRepository;
use Chamilo\Configuration\Storage\Repository\LanguageRepository;
use Chamilo\Configuration\Storage\Repository\RegistrationRepository;
use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service\ExternalCalendarCacheService;
use Chamilo\Core\Repository\Quota\Service\CalculatorCacheService;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Service\TemplateRegistrationLoader;
use Chamilo\Core\Repository\Service\TypeSelectorCacheService;
use Chamilo\Core\User\Service\UserGroupMembershipCacheService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerFactory;
use Chamilo\Libraries\Cache\Assetic\JavascriptCacheService;
use Chamilo\Libraries\Cache\Assetic\StylesheetCacheService;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Format\Twig\TwigCacheService;
use Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\DoctrineProxyCacheService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Translation\TranslationCacheService;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Twig\Environment;

/**
 * Creates the cache services for Chamilo
 *
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloCacheServicesConstructor implements CacheServicesConstructorInterface
{

    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Adds general chamilo cache services
     *
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     *
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function addGeneralCacheServices(CacheManager $cacheManager)
    {
        $stringUtilities = new StringUtilities();
        $classnameUtilities = new ClassnameUtilities($stringUtilities);

        $configurationConsulter = new ConfigurationConsulter(
            new FileConfigurationLoader(new FileConfigurationLocator(new PathBuilder($classnameUtilities)))
        );
        $exceptionLoggerFactory = new ExceptionLoggerFactory($configurationConsulter);
        $dataSourceName = new DataSourceName(
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'database'))
        );
        $connectionFactory = new ConnectionFactory($dataSourceName);

        $conditionPartTranslatorService = new ConditionPartTranslatorService(
            new ConditionPartTranslatorFactory($classnameUtilities), new ConditionPartCache()
        );

        $storageAliasGenerator = new StorageAliasGenerator($classnameUtilities);

        $dataClassRepository = new DataClassRepository(
            new DataClassRepositoryCache(), new DataClassDatabase(
            $connectionFactory->getConnection(), $storageAliasGenerator,
            $exceptionLoggerFactory->createExceptionLogger(), $conditionPartTranslatorService,
            new ParametersProcessor($conditionPartTranslatorService, $storageAliasGenerator), new RecordProcessor()
        ), new DataClassFactory()
        );

        $cacheManager->addCacheService(
            'chamilo_dependency_injection', new DependencyInjectionCacheService($configurationConsulter)
        );

        $cacheManager->addCacheService(
            'chamilo_configuration',
            new DataCacheLoader(new StorageConfigurationLoader(new ConfigurationRepository($dataClassRepository)))
        );

        $cacheManager->addCacheService(
            'chamilo_registration', new DataCacheLoader(
                new RegistrationLoader($stringUtilities, new RegistrationRepository($dataClassRepository))
            )
        );

        $cacheManager->addCacheService(
            'chamilo_language', new DataCacheLoader(new LanguageLoader(new LanguageRepository($dataClassRepository)))
        );

        $cacheManager->addCacheService(
            'chamilo_repository_configuration', $this->container->get(TemplateRegistrationLoader::class)
        );

        $cacheManager->addCacheService('chamilo_packages', new PackageBundlesCacheService());

        $configurablePathBuilder = $this->container->get(ConfigurablePathBuilder::class);
        $pathBuilder = $this->container->get(PathBuilder::class);

        $cacheManager->addCacheService(
            'chamilo_translation_bundles', new InternationalizationBundlesCacheService()
        );

        $cacheManager->addCacheService(
            'chamilo_translations', new TranslationCacheService($configurablePathBuilder)
        );

        $cacheManager->addCacheService(
            'chamilo_stylesheets', new StylesheetCacheService(
                $pathBuilder, $configurablePathBuilder, $this->container->get(ThemePathBuilder::class)
            )
        );

        $cacheManager->addCacheService(
            'chamilo_javascript', new JavascriptCacheService($pathBuilder, $configurablePathBuilder)
        );

        $cacheManager->addCacheService(
            'chamilo_calculator', new CalculatorCacheService()
        );

        // TODO: fix this for the new cache services for items
        //            $cacheManager->addCacheService('chamilo_menu_items', $this->container->get(ItemCacheService::class));

        $cacheManager->addCacheService(
            'chamilo_twig', new TwigCacheService(
                $this->container->get(Environment::class), $this->container->get(FormFactory::class)
            )
        );

        $cacheManager->addCacheService(
            'doctrine_proxies', new DoctrineProxyCacheService($this->container->get(EntityManager::class))
        );
    }

    /**
     * Adds user based cache services
     *
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     */
    protected function addUserCacheServices(CacheManager $cacheManager)
    {
        $cacheManager->addCacheService(
            'chamilo_repository_type_selector', new TypeSelectorCacheService(new TypeSelectorFactory())
        );

        $googleCalendarRepository = new CalendarRepository(
            '', '', ''
        );

        $cacheManager->addCacheService('chamilo_google_events', new EventsCacheService($googleCalendarRepository));
        $cacheManager->addCacheService(
            'chamilo_google_calendars', new OwnedCalendarsCacheService($googleCalendarRepository)
        );
        $cacheManager->addCacheService('chamilo_external_calendar', new ExternalCalendarCacheService());

        // TODO: fix the new cache services for rights
        //        $cacheManager->addCacheService(
        //            'chamilo_menu_rights', $this->container->get(RightsCacheService::class)
        //
        //        );

        $cacheManager->addCacheService('chamilo_user_groups', new UserGroupMembershipCacheService());
        $cacheManager->addCacheService('chamilo_local_settings', new LocalSettingCacheService());
    }

    /**
     * Creates and adds the cache services to the given cache manager
     *
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     *
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createCacheServices(CacheManager $cacheManager)
    {
        $this->addGeneralCacheServices($cacheManager);
        $this->addUserCacheServices($cacheManager);
    }
}