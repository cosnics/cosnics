<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionActionProviderCollection;
use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderCollection;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(CalendarExtensionActionProviderCollection::class);
    $services->set(CalendarExtensionDataProviderCollection::class);
};
