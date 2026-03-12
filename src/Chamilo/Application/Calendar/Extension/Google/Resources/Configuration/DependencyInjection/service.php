<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Application\Calendar\Extension\Google\Service\EventParser;
use Chamilo\Application\Calendar\Extension\Google\Service\EventsCacheService;
use Chamilo\Application\Calendar\Extension\Google\Service\OwnedCalendarsCacheService;
use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(OwnedCalendarsCacheService::class)->args([
        '$cacheAdapter' => service(
            'Chamilo\Application\Calendar\Extension\Google\Service\OwnedCalendarsCacheAdapter'
        )
    ]);

    $services->set(
        'Chamilo\Application\Calendar\Extension\Google\Service\OwnedCalendarsCacheAdapter', FilesystemAdapter::class
    )->args(['$namespace' => 'Chamilo\Application\Calendar\Extension\Google\OwnedCalendars'])->tag(
        AdapterInterface::class
    )->factory([service(SymfonyCacheAdapterFactory::class), 'createFilesystemAdapter']);

    $services->set(EventsCacheService::class)->args(
        [
            '$cacheAdapter' => service(
                'Chamilo\Application\Calendar\Extension\Google\Service\EventsCacheAdapter'
            )
        ]
    );

    $services->set(
        'Chamilo\Application\Calendar\Extension\Google\Service\EventsCacheAdapter', FilesystemAdapter::class
    )->args(['$namespace' => 'Chamilo\Application\Calendar\Extension\Google\Events'])->tag(
        AdapterInterface::class
    )->factory([service(SymfonyCacheAdapterFactory::class), 'createFilesystemAdapter']);

    $services->set(EventParser::class);
    $services->set(CalendarService::class);
};
