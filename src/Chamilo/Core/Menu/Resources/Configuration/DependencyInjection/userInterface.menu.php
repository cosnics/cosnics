<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Menu\UserInterface\Menu\ItemOptionsTreeDataProvider;
use Chamilo\Core\Menu\UserInterface\Menu\ItemTreeMenuDataProvider;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ItemTreeMenuDataProvider::class);
    $services->set(ItemOptionsTreeDataProvider::class);

    $services->set('Chamilo\Core\Menu\UserInterface\Menu\ItemJsTreeMenuDataProvider', JsTreeMenuDataProvider::class)
        ->args(['$treeMenuDataProvider' => service(ItemTreeMenuDataProvider::class)]);

    $services->set('Chamilo\Core\Menu\UserInterface\Menu\ItemOptionsTreeRenderer', OptionsTreeRenderer::class)->args(
        ['$optionsTreeDataProvider' => service(ItemOptionsTreeDataProvider::class)]
    );
};
