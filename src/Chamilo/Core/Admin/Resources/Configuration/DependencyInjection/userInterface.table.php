<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\UserInterface\Table\OnlineTableRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(OnlineTableRenderer::class)->args(['$user' => service('Chamilo\Core\User\CurrentUser')]);
};
