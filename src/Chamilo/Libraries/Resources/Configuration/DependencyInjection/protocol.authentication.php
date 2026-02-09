<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Authentication\Service\CasAuthentication;
use Chamilo\Libraries\Protocol\Authentication\Service\PlatformAuthentication;
use Chamilo\Libraries\Protocol\Authentication\Service\SecurityTokenAuthentication;
use Chamilo\Libraries\Protocol\Log\Factory\MonologStreamHandlerFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AuthenticationValidator::class)->args(
        ['$enabledSources' => '%cosnics.libraries.protocol.authentication.enabledSources%']
    );
    $services->set(CasAuthentication::class)->tag(AuthenticationInterface::class)->args(
        [
            '$logger' => service('Chamilo\Libraries\Protocol\Authentication\Service\CasLogger'),
            '$host' => '%cosnics.libraries.protocol.authentication.cas.host%',
            '$enableLog' => '%cosnics.libraries.protocol.authentication.cas.enableLog%',
            '$checkCertificate' => '%cosnics.libraries.protocol.authentication.cas.checkCertificate%',
            '$certificatePath' => '%cosnics.libraries.protocol.authentication.cas.certificatePath%',
            '$logPath' => '%cosnics.libraries.protocol.authentication.cas.logPath%',
            '$port' => '%cosnics.libraries.protocol.authentication.cas.port%',
            '$uri' => '%cosnics.libraries.protocol.authentication.cas.uri%'
        ]
    );
    $services->set(PlatformAuthentication::class)->tag(AuthenticationInterface::class);
    $services->set(SecurityTokenAuthentication::class)->tag(AuthenticationInterface::class);

    $services->set('Chamilo\Libraries\Protocol\Authentication\Service\CasLogger', Logger::class)->args([
        '$name' => 'Cas',
        '$handlers' => [service('Chamilo\Libraries\Protocol\Authentication\Service\CasStreamHandler')],
    ]);

    $services->set('Chamilo\Libraries\Protocol\Authentication\Service\CasStreamHandler', StreamHandler::class)->args(
        ['%cosnics.libraries.protocol.authentication.cas.logPath%']
    )->factory([service(MonologStreamHandlerFactory::class), 'createStreamHandler']);
};
