<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserDetailsRenderer::class)->tag(UserDetailsRendererInterface::class);
};
