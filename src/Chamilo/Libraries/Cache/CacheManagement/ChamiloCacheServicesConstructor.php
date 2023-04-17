<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Repository\Service\TemplateRegistrationLoader;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Format\Twig\TwigCacheService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\DoctrineProxyCacheService;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Twig\Environment;

/**
 * Creates the cache services for Chamilo
 *
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloCacheServicesConstructor implements CacheServicesConstructorInterface
{

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function addGeneralCacheServices(CacheManager $cacheManager)
    {
        $cacheManager->addCacheService(
            'chamilo_dependency_injection', new DependencyInjectionCacheService($this->getConfigurationConsulter())
        );

        $cacheManager->addCacheService(
            'chamilo_configuration', $this->container->get('Chamilo\Configuration\Service\StorageConfigurationLoader')
        );

        $cacheManager->addCacheService(
            'chamilo_registration', $this->container->get('Chamilo\Configuration\Service\RegistrationLoader')
        );

        $cacheManager->addCacheService(
            'chamilo_language', $this->container->get('Chamilo\Configuration\Service\LanguageLoader')
        );

        $cacheManager->addCacheService(
            'chamilo_repository_configuration', $this->getTemplateRegistrationLoader()
        );

        $cacheManager->addCacheService(
            'chamilo_packages',
            $this->container->get('Chamilo\Configuration\Package\Service\PackageBundlesCacheService')
        );

        $cacheManager->addCacheService(
            'chamilo_translation_bundles',
            $this->container->get('Chamilo\Configuration\Package\Service\InternationalizationBundlesCacheService')
        );

        $cacheManager->addCacheService(
            'chamilo_translations', $this->container->get('Chamilo\Libraries\Translation\TranslationCacheService')
        );

        $cacheManager->addCacheService(
            'chamilo_calculator', $this->container->get('Chamilo\Core\Repository\Quota\Service\CalculatorCacheService')
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

    protected function addUserCacheServices(CacheManager $cacheManager)
    {
        $cacheManager->addCacheService(
            'chamilo_google_events',
            $this->container->get('Chamilo\Application\Calendar\Extension\Google\Service\EventsCacheService')
        );
        $cacheManager->addCacheService(
            'chamilo_google_calendars',
            $this->container->get('Chamilo\Application\Calendar\Extension\Google\Service\OwnedCalendarsCacheService')
        );
        $cacheManager->addCacheService(
            'chamilo_external_calendar', $this->container->get(
            'Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service\ExternalCalendarCacheService'
        )
        );

        $cacheManager->addCacheService(
            'chamilo_local_settings',
            $this->container->get('Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService')
        );
    }

    public function createCacheServices(CacheManager $cacheManager)
    {
        $this->addGeneralCacheServices($cacheManager);
        $this->addUserCacheServices($cacheManager);
    }

    protected function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->container->get(ConfigurablePathBuilder::class);
    }

    protected function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->container->get('Chamilo\Configuration\Service\FileConfigurationConsulter');
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->container->get('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository');
    }

    protected function getEntityManager(): EntityManager
    {
        return $this->container->get(EntityManager::class);
    }

    protected function getEnvironment(): Environment
    {
        return $this->container->get(Environment::class);
    }

    protected function getFormFactory(): FormFactory
    {
        return $this->container->get(FormFactory::class);
    }

    protected function getStringUtilities(): StringUtilities
    {
        return $this->container->get(StringUtilities::class);
    }

    protected function getTemplateRegistrationLoader(): TemplateRegistrationLoader
    {
        return $this->container->get(TemplateRegistrationLoader::class);
    }
}