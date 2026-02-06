<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Architecture\Domain\UserDetailsRendererCollection;
use Chamilo\Core\User\Architecture\Domain\UserPictureProviderCollection;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Service\UserFactory;
use Chamilo\Core\User\Storage\DataClass\User;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserDetailsRendererCollection::class);
    $services->set(UserPictureProviderCollection::class)->args(
        ['$activePictureProviderClass' => '%cosnics.application.user.pictureProviderClass%']
    );

    $services->set('Chamilo\Core\User\CurrentUser', User::class)->factory([service(UserFactory::class), 'getUser']);

    $services->alias(UserPictureProviderInterface::class, 'Chamilo\Core\User\Service\UserPictureProvider');

    $services->set('Chamilo\Core\User\Service\UserPictureProvider', UserPictureProviderInterface::class)->factory(
        [service(UserPictureProviderCollection::class), 'getActivePictureProvider']
    );
};
