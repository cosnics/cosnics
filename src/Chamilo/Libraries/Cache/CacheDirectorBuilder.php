<?php
namespace Chamilo\Libraries\Cache;

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
use Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService;
use Chamilo\Libraries\Platform\TranslationCacheService;
use Chamilo\Libraries\Storage\Cache\ConditionPartCache;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the cache directory by adding custom cache services directly (through code) and indirectly
 * (through dependency injection)
 *
 * @package Chamilo\Libraries\Cache
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheDirectorBuilder
{

    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * CacheDirectorBuilder constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Builds the cache director and adds the chamilo cache services through code
     *
     * @return CacheDirector
     */
    public function buildCacheDirector()
    {
        $cacheDirector = new CacheDirector();

        $this->addChamiloCacheServices($cacheDirector);

        return $cacheDirector;
    }

    /**
     * Adds the chamilo cache services through code
     *
     * @param CacheDirector $cacheDirector
     */
    protected function addChamiloCacheServices(CacheDirector $cacheDirector)
    {
        $this->addGeneralCacheServices($cacheDirector);
        $this->addUserCacheServices($cacheDirector);
    }

    /**
     * Adds general chamilo cache services
     *
     * @param CacheDirector $cacheDirector
     */
    protected function addGeneralCacheServices(CacheDirector $cacheDirector)
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

        $dataClassRepository = new DataClassRepository(
            new DataClassRepositoryCache(),
            new DataClassDatabase(
                $connectionFactory->getConnection(),
                new StorageAliasGenerator($classnameUtilities),
                $exceptionLoggerFactory->createExceptionLogger(),
                new ConditionPartTranslatorService(
                    new ConditionPartTranslatorFactory($classnameUtilities),
                    new ConditionPartCache()
                ),
                new RecordProcessor()
            ),
            new DataClassFactory()
        );

        $cacheDirector->addCacheService(
            'chamilo_dependency_injection',
            new DependencyInjectionCacheService($configurationConsulter)
        );
        $cacheDirector->addCacheService('chamilo_translations', new TranslationCacheService());

        $cacheDirector->addCacheService(
            'chamilo_configuration',
            new DataCacheLoader(new StorageConfigurationLoader(new ConfigurationRepository($dataClassRepository)))
        );

        $cacheDirector->addCacheService(
            'chamilo_registration',
            new DataCacheLoader(
                new RegistrationLoader($stringUtilities, new RegistrationRepository($dataClassRepository))
            )
        );

        $cacheDirector->addCacheService(
            'chamilo_language',
            new DataCacheLoader(new LanguageLoader(new LanguageRepository($dataClassRepository)))
        );

        $cacheDirector->addCacheService(
            'chamilo_repository_configuration',
            new \Chamilo\Core\Repository\Service\ConfigurationCacheService()
        );

        $cacheDirector->addCacheService('chamilo_packages', new PackageBundlesCacheService());

        $configurablePathBuilder = new ConfigurablePathBuilder(
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'storage'))
        );

        $pathBuilder = new PathBuilder($classnameUtilities);
        $theme = Theme::getInstance();

        $cacheDirector->addCacheService(
            'chamilo_stylesheets',
            new StylesheetCacheService($pathBuilder, $configurablePathBuilder, $theme)
        );

        $cacheDirector->addCacheService(
            'chamilo_javascript', new JavascriptCacheService($pathBuilder, $configurablePathBuilder)
        );

        $cacheDirector->addCacheService('chamilo_calculator', new CalculatorCacheService());
        $cacheDirector->addCacheService('chamilo_menu_items', new ItemsCacheService(new ItemRepository()));
    }

    /**
     * Adds user based cache services
     *
     * @param CacheDirector $cacheDirector
     */
    protected function addUserCacheServices(CacheDirector $cacheDirector)
    {
        $cacheDirector->addCacheService(
            'chamilo_repository_type_selector',
            new TypeSelectorCacheService(new TypeSelectorFactory())
        );

        $cacheDirector->addCacheService(
            'chamilo_office365_requests',
            new RequestCacheService(
                new \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository('', '', '', '')
            )
        );

        $googleCalendarRepository = new \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository(
            '',
            '',
            ''
        );

        $cacheDirector->addCacheService('chamilo_google_events', new EventsCacheService($googleCalendarRepository));
        $cacheDirector->addCacheService(
            'chamilo_google_calendars',
            new OwnedCalendarsCacheService($googleCalendarRepository)
        );
        $cacheDirector->addCacheService('chamilo_external_calendar', new ExternalCalendarCacheService());
        $cacheDirector->addCacheService(
            'chamilo_menu_rights',
            new RightsCacheService(new ItemService(new ItemRepository()))
        );
        $cacheDirector->addCacheService('chamilo_user_groups', new UserGroupMembershipCacheService());
        $cacheDirector->addCacheService('chamilo_local_settings', new LocalSettingCacheService());
    }
}