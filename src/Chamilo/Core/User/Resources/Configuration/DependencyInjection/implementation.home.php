<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Implementation\Home\LoginBlockRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(LoginBlockRenderer::class)->tag(BlockRenderer::class);
};
