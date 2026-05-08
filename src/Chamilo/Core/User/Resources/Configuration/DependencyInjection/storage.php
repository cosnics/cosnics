<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Storage\Entity\UserVisit;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Core\User\Storage\Repository\UserTrackingEntityRepository;
use Chamilo\Core\User\Storage\Repository\UserTrackingRepository;
use Doctrine\ORM\EntityManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserRepository::class);
    $services->set(UserTrackingRepository::class);
    $services->set(UserTrackingEntityRepository::class)->factory(
        [service(EntityManager::class), 'getRepository']
    )->args([UserVisit::class]);
};
