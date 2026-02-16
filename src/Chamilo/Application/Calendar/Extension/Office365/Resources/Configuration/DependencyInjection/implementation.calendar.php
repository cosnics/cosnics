<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Chamilo\Application\Calendar\Extension\Office365\Implementation\Calendar\CalendarExtensionDataProvider;
use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(
        CalendarExtensionDataProvider::class
    )->args(
        [
            '$filesystemAdapter' => service(
                'Chamilo\Application\Calendar\Extension\Office365\Cache\CalendarCacheAdapter'
            )
        ]
    )->tag(CalendarExtensionDataProviderInterface::class);

    $services->set(
        'Chamilo\Application\Calendar\Extension\Office365\Cache\CalendarCacheAdapter', FilesystemAdapter::class
    )->args([
        '$namespace' => 'Chamilo\Application\Calendar\Extension\Office365',
        '$defaultLifetime' => '%cosnics.libraries.storage.cache.external.defaultLifetime%',
    ])->tag(AdapterInterface::class)->factory(
        [service(SymfonyCacheAdapterFactory::class), 'createFilesystemAdapter']
    );
};
