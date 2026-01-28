<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\UserInterface\Table\GroupTableRenderer;
use Chamilo\Core\Group\UserInterface\Table\NonSubscribedUserTableRenderer;
use Chamilo\Core\Group\UserInterface\Table\SubscribedUserTableRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GroupTableRenderer::class);
    $services->set(SubscribedUserTableRenderer::class);
    $services->set(NonSubscribedUserTableRenderer::class);
};
