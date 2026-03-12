<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(CalendarRepository::class)->args([
        '$clientId' => '%cosnics.libraries.protocol.google.clientId%',
        '$clientSecret' => '%cosnics.libraries.protocol.google.clientSecret%',
        '$developerKey' => '%cosnics.libraries.protocol.google.developerKey%'
    ]);
};