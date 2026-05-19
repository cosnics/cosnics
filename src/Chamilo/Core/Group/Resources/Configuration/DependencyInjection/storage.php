<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\Group\Storage\Repository\GroupEntityRepository;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipEntityRepository;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\Group\Storage\Repository\GroupTrackingRepository;
use Doctrine\ORM\EntityManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GroupMembershipRepository::class);
    $services->set(GroupRepository::class);
    $services->set(GroupTrackingRepository::class);

    $services->set(GroupEntityRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([Group::class]);

    $services->set(GroupMembershipEntityRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([GroupMembership::class]);
};
