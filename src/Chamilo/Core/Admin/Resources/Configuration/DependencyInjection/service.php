<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Admin\Service\Finder\BasicBundlesGenerator;
use Chamilo\Core\Admin\Service\Finder\InternationalizationBundlesGenerator;
use Chamilo\Core\Admin\Service\Finder\PackageBundlesGenerator;
use Chamilo\Core\Admin\Service\InternationalizationBundlesCacheService;
use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Service\PackageFactory;
use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(OnlineService::class);
    $services->set(PackageFactory::class);

    $services->set(PackageBundlesCacheService::class)->args(
        ['$cacheAdapter' => service('Chamilo\Core\Admin\Service\PackageBundlesCacheAdapter')]
    )->tag(CacheDataPreLoaderInterface::class);

    $services->set('Chamilo\Core\Admin\Service\PackageBundlesCacheAdapter', FilesystemAdapter::class)->args(
        ['$namespace' => 'Chamilo\Core\Admin\PackageBundles']
    )->tag(AdapterInterface::class)->factory([service(SymfonyCacheAdapterFactory::class), 'createFilesystemAdapter']);

    $services->set(InternationalizationBundlesCacheService::class)->args(
        ['$cacheAdapter' => service('Chamilo\Core\Admin\Service\InternationalizationBundlesCacheAdapter')]
    )->tag(CacheDataPreLoaderInterface::class);

    $services->set('Chamilo\Core\Admin\Service\InternationalizationBundlesCacheAdapter', FilesystemAdapter::class)
        ->args(['$namespace' => 'Chamilo\Core\Admin\InternationalizationBundles'])->tag(AdapterInterface::class)
        ->factory([service(SymfonyCacheAdapterFactory::class), 'createFilesystemAdapter']);

    $services->set(LanguageConsulter::class);

    $services->set(BasicBundlesGenerator::class);
    $services->set(InternationalizationBundlesGenerator::class);
    $services->set(PackageBundlesGenerator::class);
};
