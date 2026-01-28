<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Core\User\Storage\Repository\UserTrackingRepository;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserRepository::class);
    $services->set(UserTrackingRepository::class);
};
