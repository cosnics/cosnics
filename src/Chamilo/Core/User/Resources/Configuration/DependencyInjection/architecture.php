<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Architecture\Domain\UserDetailsRendererRegistry;
use Chamilo\Core\User\Architecture\Domain\UserPictureProviderRegistry;
use Chamilo\Core\User\Architecture\EventDispatcher\Subscriber\ActivityUserEventSubscriber;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Service\UserFactory;
use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserDetailsRendererRegistry::class);
    $services->set(UserPictureProviderRegistry::class)->args(
        ['$activePictureProviderClass' => '%cosnics.application.user.pictureProviderClass%']
    );

    $services->set('Chamilo\Core\User\CurrentUser', User::class)->factory([service(UserFactory::class), 'getUser']);

    $services->alias(UserPictureProviderInterface::class, 'Chamilo\Core\User\Service\UserPictureProvider');
    $services->alias(UserPictureUpdateProviderInterface::class, 'Chamilo\Core\User\Service\UserPictureProvider');

    $services->set('Chamilo\Core\User\Service\UserPictureProvider', UserPictureProviderInterface::class)->factory(
        [service(UserPictureProviderRegistry::class), 'getActivePictureProvider']
    );

    $services->set(ActivityUserEventSubscriber::class)->tag(EventSubscriberInterface::class);
};
