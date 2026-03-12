<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\UserInterface\Table\UserTableRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserTableRenderer::class)->args(['$currentUser' => service('Chamilo\Core\User\CurrentUser')]);
};
