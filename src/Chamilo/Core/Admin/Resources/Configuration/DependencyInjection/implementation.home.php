<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Implementation\Home\PortalHomeBlockRenderer;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(PortalHomeBlockRenderer::class)->tag(BlockRenderer::class);
};
