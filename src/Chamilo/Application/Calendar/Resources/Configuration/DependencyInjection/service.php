<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Service\VisibilityService;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AvailabilityService::class);
    $services->set(VisibilityService::class);
};