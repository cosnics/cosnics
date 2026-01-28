<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Implementation\Menu\AccountItemRenderer;
use Chamilo\Core\User\Implementation\Menu\LogoutItemRenderer;
use Chamilo\Core\User\Implementation\Menu\WidgetItemRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AccountItemRenderer::class)->tag(ItemRenderer::class);
    $services->set(LogoutItemRenderer::class)->tag(ItemRenderer::class);
    $services->set(WidgetItemRenderer::class)->tag(ItemRenderer::class);
};
