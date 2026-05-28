<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Entity\GroupActivity;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\Group\Storage\Repository\GroupActivityRepository;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Doctrine\ORM\EntityManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GroupActivityRepository::class);

    $services->set(GroupRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([Group::class]);

    $services->set(GroupMembershipRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([GroupMembership::class]);

    $services->set(GroupActivityRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([GroupActivity::class]);
};
