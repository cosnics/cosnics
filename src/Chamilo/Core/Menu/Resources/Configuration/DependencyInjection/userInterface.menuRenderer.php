<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Menu\UserInterface\MenuRenderer\MenuRenderer;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(MenuRenderer::class)->args([
        '$webPathBuilder' => service(WebPathBuilder::class),
        '$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder'),
    ]);
};
