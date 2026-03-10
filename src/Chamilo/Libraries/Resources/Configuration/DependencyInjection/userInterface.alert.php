<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Interface\AlertStorageInterface;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsRenderer;
use Chamilo\Libraries\UserInterface\Alert\Storage\Repository\AlertSessionStorage;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AlertsManager::class);
    $services->set(AlertRenderer::class);
    $services->set(AlertsRenderer::class);
    $services->set(AlertSessionStorage::class);
    $services->alias(AlertStorageInterface::class, AlertSessionStorage::class);
};
