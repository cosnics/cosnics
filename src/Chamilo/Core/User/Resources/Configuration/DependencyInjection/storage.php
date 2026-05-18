<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Core\User\Storage\Entity\UserActivity;
use Chamilo\Core\User\Storage\Entity\UserAuthenticationActivity;
use Chamilo\Core\User\Storage\Entity\UserVisit;
use Chamilo\Core\User\Storage\Repository\UserActivityRepository;
use Chamilo\Core\User\Storage\Repository\UserAuthenticationActivityRepository;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Core\User\Storage\Repository\UserVisitRepository;
use Doctrine\ORM\EntityManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserVisitRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([UserVisit::class]);
    $services->set(UserActivityRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([UserActivity::class]);
    $services->set(UserAuthenticationActivityRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([UserAuthenticationActivity::class]);
    $services->set(UserRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([User::class]);
};
