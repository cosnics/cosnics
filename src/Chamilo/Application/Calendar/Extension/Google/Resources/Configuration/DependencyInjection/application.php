<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Extension\Google\Component\LoginComponent;
use Chamilo\Application\Calendar\Extension\Google\Component\LogoutComponent;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(LoginComponent::class)->tag(ApplicationInterface::class);
    $services->set(LogoutComponent::class)->tag(ApplicationInterface::class);
};