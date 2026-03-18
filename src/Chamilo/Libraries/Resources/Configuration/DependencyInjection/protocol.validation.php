<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Validate\Factory\ValidatorFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ValidatorFactory::class);
    $services->set(ValidatorInterface::class)->factory(
        [service(ValidatorFactory::class), 'createValidator']
    );
};
