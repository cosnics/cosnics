<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Implementation\Home\DayBlockRenderer;
use Chamilo\Application\Calendar\Implementation\Home\MonthBlockRenderer;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(DayBlockRenderer::class)->tag(BlockRenderer::class);
    $services->set(MonthBlockRenderer::class)->tag(BlockRenderer::class);
};