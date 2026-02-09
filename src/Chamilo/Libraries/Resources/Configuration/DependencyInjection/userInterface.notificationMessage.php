<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Interface\NotificationMessageStorageInterface;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageManager;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageRenderer;
use Chamilo\Libraries\UserInterface\NotificationMessage\Storage\Repository\NotificationMessageSessionStorage;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(NotificationMessageManager::class);
    $services->set(NotificationMessageRenderer::class);
    $services->set(NotificationMessageSessionStorage::class);
    $services->alias(NotificationMessageStorageInterface::class, NotificationMessageSessionStorage::class);
};
