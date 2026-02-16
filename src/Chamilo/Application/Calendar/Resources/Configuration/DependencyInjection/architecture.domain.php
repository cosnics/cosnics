<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionActionProviderRegistry;
use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderRegistry;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(CalendarExtensionActionProviderRegistry::class);
    $services->set(CalendarExtensionDataProviderRegistry::class);
};
