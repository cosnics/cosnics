<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Application\Calendar\Extension\Google\Service\EventsCacheService;
use Chamilo\Application\Calendar\Extension\Google\Service\OwnedCalendarsCacheService;
use Chamilo\Configuration\Package\Service\InternationalizationBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Service\DataCacheLoader;
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
use Chamilo\Libraries\DependencyInjection\DependencyInjectionCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Format\Twig\TwigCacheService;
use Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\DoctrineProxyCacheService;
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
     */
    protected function addGeneralCacheServices(CacheManager $cacheManager)
    {

        $dataClassRepository =
            $this->container->get('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository');

        $cacheManager->addCacheService(
            'chamilo_dependency_injection', new DependencyInjectionCacheService($this->getConfigurationConsulter())
        );

        $cacheManager->addCacheService(
            'chamilo_configuration', new DataCacheLoader(
                new StorageConfigurationLoader(new ConfigurationRepository($this->getDataClassRepository())),
                $this->getConfigurablePathBuilder()
            )
        );

        $cacheManager->addCacheService(
            'chamilo_registration', new DataCacheLoader(
                new RegistrationLoader(
                    $this->getStringUtilities(), new RegistrationRepository($this->getDataClassRepository())
                ), $this->getConfigurablePathBuilder()
            )
        );

        $cacheManager->addCacheService(
            'chamilo_language', new DataCacheLoader(
                new LanguageLoader(new LanguageRepository($this->getDataClassRepository())),
                $this->getConfigurablePathBuilder()
            )
        );

        $cacheManager->addCacheService(
            'chamilo_repository_configuration', $this->getTemplateRegistrationLoader()
        );

        $cacheManager->addCacheService(
            'chamilo_packages', new PackageBundlesCacheService($this->getConfigurablePathBuilder())
        );

        $cacheManager->addCacheService(
            'chamilo_translation_bundles',
            new InternationalizationBundlesCacheService($this->getConfigurablePathBuilder())
        );

        $cacheManager->addCacheService(
            'chamilo_translations', new TranslationCacheService($this->getConfigurablePathBuilder())
        );

        $cacheManager->addCacheService(
            'chamilo_calculator', new CalculatorCacheService($this->getConfigurablePathBuilder())
        );

        // TODO: fix this for the new cache services for items
        //            $cacheManager->addCacheService('chamilo_menu_items', $this->container->get(ItemCacheService::class));

        $cacheManager->addCacheService(
            'chamilo_twig', new TwigCacheService(
                $this->getEnvironment(), $this->getFormFactory()
            )
        );

        $cacheManager->addCacheService(
            'doctrine_proxies', new DoctrineProxyCacheService($this->getEntityManager())
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
            'chamilo_repository_type_selector',
            new TypeSelectorCacheService(new TypeSelectorFactory(), $this->getConfigurablePathBuilder())
        );

        $googleCalendarRepository = new CalendarRepository(
            '', '', ''
        );

        $cacheManager->addCacheService(
            'chamilo_google_events',
            new EventsCacheService($googleCalendarRepository, $this->getConfigurablePathBuilder())
        );
        $cacheManager->addCacheService(
            'chamilo_google_calendars',
            new OwnedCalendarsCacheService($googleCalendarRepository, $this->getConfigurablePathBuilder())
        );
        $cacheManager->addCacheService(
            'chamilo_external_calendar', new ExternalCalendarCacheService($this->getConfigurablePathBuilder())
        );

        // TODO: fix the new cache services for rights
        //        $cacheManager->addCacheService(
        //            'chamilo_menu_rights', $this->container->get(RightsCacheService::class)
        //
        //        );

        $cacheManager->addCacheService(
            'chamilo_user_groups', new UserGroupMembershipCacheService($this->getConfigurablePathBuilder())
        );
        $cacheManager->addCacheService(
            'chamilo_local_settings', new LocalSettingCacheService($this->getConfigurablePathBuilder())
        );
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

    /**
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected function getConfigurablePathBuilder()
    {
        return $this->container->get(ConfigurablePathBuilder::class);
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected function getConfigurationConsulter()
    {
        return $this->container->get('Chamilo\Configuration\Service\FileConfigurationConsulter');
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->container->get('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get(EntityManager::class);
    }

    /**
     * @return \Twig\Environment
     */
    protected function getEnvironment()
    {
        return $this->container->get(Environment::class);
    }

    /**
     * @return \Symfony\Component\Form\FormFactory
     */
    protected function getFormFactory()
    {
        return $this->container->get(FormFactory::class);
    }

    /**
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    protected function getStringUtilities()
    {
        return $this->container->get(StringUtilities::class);
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationLoader
     */
    protected function getTemplateRegistrationLoader()
    {
        return $this->container->get(TemplateRegistrationLoader::class);
    }
}