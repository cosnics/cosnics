<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionActionProviderInterface;
use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Chamilo\Application\Calendar\Extension\Google\Implementation\Calendar\CalendarExtensionActionProvider;
use Chamilo\Application\Calendar\Extension\Google\Implementation\Calendar\CalendarExtensionDataProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(CalendarExtensionDataProvider::class)->tag(CalendarExtensionDataProviderInterface::class);
    $services->set(CalendarExtensionActionProvider::class)->tag(CalendarExtensionActionProviderInterface::class);
};