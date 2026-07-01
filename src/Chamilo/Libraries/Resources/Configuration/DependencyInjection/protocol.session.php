<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Session\Factory\SessionFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(SessionFactory::class)->args([
        '$sessionStorage' => service(NativeSessionStorage::class),
        '$securityKey' => '%cosnics.libraries.protocol.security.securityKey%',
    ]);

    $services->alias(SessionInterface::class, Session::class);
    $services->set(Session::class)->factory([service(SessionFactory::class), 'getSession']);
    $services->set(NativeSessionStorage::class)->args(
        ['$handler' => service(NativeFileSessionHandler::class)]
    );

    $services->set(NativeFileSessionHandler::class)->args(
        ['$savePath' => '%cosnics.libraries.protocol.session.savePath%']
    );
};
