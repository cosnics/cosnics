<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(CalendarRepository::class)->args(['$currentUser' => service('Chamilo\Core\User\CurrentUser')]);
};