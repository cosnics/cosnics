<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

use Chamilo\Application\Calendar\Extension\Google\Service\EventsCacheService;
use Chamilo\Application\Calendar\Extension\Google\Service\OwnedCalendarsCacheService;
use Chamilo\Application\Calendar\Extension\Office365\Service\RequestCacheService;
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
use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemsCacheService;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsCacheService;
use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service\ExternalCalendarCacheService;
use Chamilo\Core\Repository\Quota\Service\CalculatorCacheService;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Service\TypeSelectorCacheService;
use Chamilo\Core\User\Service\UserGroupMembershipCacheService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerFactory;
use Chamilo\Libraries\Cache\Assetic\JavascriptCacheService;
use Chamilo\Libraries\Cache\Assetic\StylesheetCacheService;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Twig\TwigCacheService;
use Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService;
use Chamilo\Libraries\Platform\TranslationCacheService;
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
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * Creates and adds the cache services to the given cache manager
     *
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     */
    public function createCacheServices(CacheManager $cacheManager)
    {
        $this->addGeneralCacheServices($cacheManager);
        $this->addUserCacheServices($cacheManager);
    }

    /**
     * Adds general chamilo cache services
     *
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     */
    protected function addGeneralCacheServices(CacheManager $cacheManager)
    {
        $stringUtilities = new StringUtilities();
        $classnameUtilities = new ClassnameUtilities($stringUtilities);

        $configurationConsulter = new ConfigurationConsulter(
            new FileConfigurationLoader(new FileConfigurationLocator(new PathBuilder($classnameUtilities))));
        $exceptionLoggerFactory = new ExceptionLoggerFactory($configurationConsulter);
        $dataSourceName = new DataSourceName(
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'database')));
        $connectionFactory = new ConnectionFactory($dataSourceName);

        $dataClassRepository = new DataClassRepository(
            new DataClassRepositoryCache(),
            new DataClassDatabase(
                $connectionFactory->getConnection(),
                new StorageAliasGenerator($classnameUtilities),
                $exceptionLoggerFactory->createExceptionLogger(),
                new ConditionPartTranslatorService(
                    new ConditionPartTranslatorFactory($classnameUtilities),
                    new ConditionPartCache()),
                new RecordProcessor()),
            new DataClassFactory());

        $cacheManager->addCacheService(
            'chamilo_dependency_injection',
            new DependencyInjectionCacheService($configurationConsulter));
        $cacheManager->addCacheService('chamilo_translations', new TranslationCacheService());
        $cacheManager->addCacheService(
            'symfony_translations',
            new \Chamilo\Libraries\Translation\TranslationCacheService());

        $cacheManager->addCacheService(
            'chamilo_configuration',
            new DataCacheLoader(new StorageConfigurationLoader(new ConfigurationRepository($dataClassRepository))));

        $cacheManager->addCacheService(
            'chamilo_registration',
            new DataCacheLoader(
                new RegistrationLoader($stringUtilities, new RegistrationRepository($dataClassRepository))));

        $cacheManager->addCacheService(
            'chamilo_language',
            new DataCacheLoader(new LanguageLoader(new LanguageRepository($dataClassRepository))));

        $cacheManager->addCacheService(
            'chamilo_repository_configuration',
            new \Chamilo\Core\Repository\Service\ConfigurationCacheService());

        $cacheManager->addCacheService('chamilo_packages', new PackageBundlesCacheService());

        $configurablePathBuilder = new ConfigurablePathBuilder(
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'storage')));

        $pathBuilder = new PathBuilder($classnameUtilities);
        $theme = Theme::getInstance();

        $cacheManager->addCacheService(
            'chamilo_stylesheets',
            new StylesheetCacheService($pathBuilder, $configurablePathBuilder, $theme));

        $cacheManager->addCacheService(
            'chamilo_javascript',
            new JavascriptCacheService($pathBuilder, $configurablePathBuilder));

        $cacheManager->addCacheService('chamilo_calculator', new CalculatorCacheService());
        $cacheManager->addCacheService('chamilo_menu_items', new ItemsCacheService(new ItemRepository()));

        $cacheManager->addCacheService(
            'chamilo_twig',
            new TwigCacheService(
                $this->container->get('twig.environment'),
                $this->container->get('symfony.component.forms.form')));

        $cacheManager->addCacheService(
            'doctrine_proxies',
            new DoctrineProxyCacheService($this->container->get('doctrine.orm.entity_manager')));
    }

    /**
     * Adds user based cache services
     *
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     */
    protected function addUserCacheServices(CacheManager $cacheManager)
    {
        $cacheManager->addCacheService(
            'chamilo_repository_type_selector',
            new TypeSelectorCacheService(new TypeSelectorFactory()));

        $cacheManager->addCacheService(
            'chamilo_office365_requests',
            new RequestCacheService(
                new \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository('', '', '', '')));

        $googleCalendarRepository = new \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository(
            '',
            '',
            '');

        $cacheManager->addCacheService('chamilo_google_events', new EventsCacheService($googleCalendarRepository));
        $cacheManager->addCacheService(
            'chamilo_google_calendars',
            new OwnedCalendarsCacheService($googleCalendarRepository));
        $cacheManager->addCacheService('chamilo_external_calendar', new ExternalCalendarCacheService());
        $cacheManager->addCacheService(
            'chamilo_menu_rights',
            new RightsCacheService(new ItemService(new ItemRepository())));
        $cacheManager->addCacheService('chamilo_user_groups', new UserGroupMembershipCacheService());
        $cacheManager->addCacheService('chamilo_local_settings', new LocalSettingCacheService());
    }
}