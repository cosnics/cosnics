<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Menu\Implementation\Menu\ApplicationItemRenderer;
use Chamilo\Core\Menu\Implementation\Menu\CategoryItemRenderer;
use Chamilo\Core\Menu\Implementation\Menu\LanguageItemRenderer;
use Chamilo\Core\Menu\Implementation\Menu\LinkItemRenderer;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ApplicationItemRenderer::class)->args(
        ['$fallbackIsoCodes' => '%chamilo.configuration.general.language.fallback%']
    )->tag(ItemRenderer::class);

    $services->set(CategoryItemRenderer::class)->args(
        ['$fallbackIsoCodes' => '%chamilo.configuration.general.language.fallback%']
    )->tag(ItemRenderer::class);

    $services->set(LanguageItemRenderer::class)->tag(ItemRenderer::class);

    $services->set(LinkItemRenderer::class)->args(
        ['$fallbackIsoCodes' => '%chamilo.configuration.general.language.fallback%']
    )->tag(ItemRenderer::class);
};
