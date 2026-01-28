<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\UserInterface\Menu\GroupOptionsTreeDataProvider;
use Chamilo\Core\Group\UserInterface\Menu\GroupTreeMenuDataProvider;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GroupTreeMenuDataProvider::class);
    $services->set(GroupOptionsTreeDataProvider::class);

    $services->set('Chamilo\Core\Group\UserInterface\Menu\GroupJsTreeMenuDataProvider', JsTreeMenuDataProvider::class)
        ->args(['$treeMenuDataProvider' => service(GroupTreeMenuDataProvider::class)]);

    $services->set('Chamilo\Core\Group\UserInterface\Menu\GroupOptionsTreeRenderer', OptionsTreeRenderer::class)->args(
        ['$optionsTreeDataProvider' => service(GroupOptionsTreeDataProvider::class)]
    );
};
