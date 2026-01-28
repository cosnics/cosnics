<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Home\UserInterface\HomeRenderer\ColumnRenderer;
use Chamilo\Core\Home\UserInterface\HomeRenderer\HomeRenderer;
use Chamilo\Core\Home\UserInterface\HomeRenderer\TabRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(HomeRenderer::class);
    $services->set(TabRenderer::class);
    $services->set(ColumnRenderer::class);
};
