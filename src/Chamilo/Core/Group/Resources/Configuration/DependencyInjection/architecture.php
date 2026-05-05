<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber\ActivityGroupEventSubscriber;
use Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber\GroupEventSubscriber;
use Chamilo\Core\Group\Architecture\EventDispatcher\Subscriber\UserEventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ActivityGroupEventSubscriber::class)->tag(EventSubscriberInterface::class);
    $services->set(GroupEventSubscriber::class)->tag(EventSubscriberInterface::class);
    $services->set(UserEventSubscriber::class)->tag(EventSubscriberInterface::class);
};
