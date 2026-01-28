<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\Group\Storage\Repository\GroupTrackingRepository;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GroupMembershipRepository::class);
    $services->set(GroupRepository::class);
    $services->set(GroupTrackingRepository::class);
};
