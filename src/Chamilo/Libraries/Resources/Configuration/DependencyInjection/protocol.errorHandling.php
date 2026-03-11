<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\ErrorHandling\Factory\ExceptionLoggerFactory;
use Chamilo\Libraries\Protocol\ErrorHandling\Service\ErrorHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ErrorHandler::class)->args(
        ['$themeSystemPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder')]
    );

    $services->set('Chamilo\Libraries\Protocol\ErrorHandling\Service\ExceptionLogger')->factory(
        [service(ExceptionLoggerFactory::class), 'createExceptionLogger']
    );

    $services->alias(ExceptionLoggerInterface::class, 'Chamilo\Libraries\Protocol\ErrorHandling\Service\ExceptionLogger');

    $services->set(ExceptionLoggerFactory::class)->args(
        ['$errorHandlingConfiguration' => '%cosnics.libraries.protocol.error.handling%']
    );
};
