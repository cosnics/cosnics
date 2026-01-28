<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Storage\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AvailabilityRepository::class);
    $services->set(VisibilityRepository::class);
};