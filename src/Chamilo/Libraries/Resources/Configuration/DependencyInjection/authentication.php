<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Authentication\Service\PlatformAuthentication;
use Chamilo\Libraries\Protocol\Authentication\Service\SecurityTokenAuthentication;
use Ehb\Libraries\Authentication\Cas\CasAuthentication;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AuthenticationValidator::class);
    $services->set(CasAuthentication::class)->tag(AuthenticationInterface::class);
    $services->set(PlatformAuthentication::class)->tag(AuthenticationInterface::class);
    $services->set(SecurityTokenAuthentication::class)->tag(AuthenticationInterface::class);
};
