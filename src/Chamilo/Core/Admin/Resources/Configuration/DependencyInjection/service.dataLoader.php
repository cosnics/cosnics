<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Service\DataLoader\FileConfigurationCacheDataPreLoader;
use Chamilo\Core\Admin\Service\DataLoader\StorageConfigurationCacheDataPreLoader;
use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(FileConfigurationCacheDataPreLoader::class)->args(
        ['$cacheAdapter' => service('Chamilo\Core\Admin\Service\DataLoader\FileConfigurationCacheAdapter')]
    )->tag(CacheDataPreLoaderInterface::class);

    $services->set('Chamilo\Core\Admin\Service\DataLoader\FileConfigurationCacheAdapter', ArrayAdapter::class)->tag(
        AdapterInterface::class
    );

    $services->set(StorageConfigurationCacheDataPreLoader::class)->args(
        ['$cacheAdapter' => service('Chamilo\Core\Admin\Service\DataLoader\StorageConfigurationCacheAdapter')]
    )->tag(CacheDataPreLoaderInterface::class);

    $services->set('Chamilo\Core\Admin\Service\DataLoader\StorageConfigurationCacheAdapter', PhpFilesAdapter::class)
        ->args(['$namespace' => 'Chamilo\Core\Admin\Service\Configuration\Storage'])->tag(AdapterInterface::class)
        ->factory([service(SymfonyCacheAdapterFactory::class), 'createPhpFilesAdapter']);
};
