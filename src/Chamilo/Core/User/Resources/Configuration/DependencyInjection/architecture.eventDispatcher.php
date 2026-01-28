<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\User\Architecture\EventDispatcher\Subscriber\ActivityUserEventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ActivityUserEventSubscriber::class)->tag(EventSubscriberInterface::class);
};
