<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Security\Factory\PasswordGeneratorFactory;
use Chamilo\Libraries\Protocol\Security\Service\SecurityUtilities;
use Chamilo\Libraries\Protocol\Session\Factory\PdoSessionHandlerFactory;
use Chamilo\Libraries\Protocol\Session\Factory\SessionFactory;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(SecurityUtilities::class);

    $services->set(SessionFactory::class)->args([
        '$sessionStorage' => service(NativeSessionStorage::class),
        '$securityKey' => '%cosnics.libraries.protocol.security.securityKey%',
    ]);

    $services->set(PdoSessionHandlerFactory::class)->args(['$connection' => service('Doctrine\DBAL\Connection\Session')]
    );

    $services->alias(PasswordGeneratorInterface::class, 'Chamilo\Libraries\Protocol\Security\PasswordGenerator');

    $services->set('Chamilo\Libraries\Protocol\Security\PasswordGenerator')->factory(
        [service(PasswordGeneratorFactory::class), 'createPasswordGenerator']
    );

    $services->set(PasswordGeneratorFactory::class);
};
