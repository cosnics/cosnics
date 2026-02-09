<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Session\Factory\PdoSessionHandlerFactory;
use Chamilo\Libraries\Protocol\Session\Factory\SessionFactory;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(SessionFactory::class)->args([
        '$sessionStorage' => service(NativeSessionStorage::class),
        '$securityKey' => '%cosnics.libraries.protocol.security.securityKey%',
    ]);

    $services->set(PdoSessionHandlerFactory::class)->args(['$connection' => service('Doctrine\DBAL\Connection\Session')]
    );
};
