<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\Error\Factory\ExceptionLoggerFactory;
use Chamilo\Libraries\Protocol\Error\Service\ErrorHandler;
use Chamilo\Libraries\Service\Bootstrap\ApplicationFactory;
use Chamilo\Libraries\Service\Bootstrap\Bootstrap;
use Chamilo\Libraries\Service\Bootstrap\Kernel;
use Chamilo\Libraries\Service\Resource\ResourceGenerator;
use Chamilo\Libraries\Service\Routing\DataClassUrlGenerator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ActionResultRenderer;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(Bootstrap::class)->args(['$showErrors' => '%cosnics.libraries.protocol.error.show%']);
    $services->set(Kernel::class)->args(
        [
            '$user' => service('Chamilo\Core\User\CurrentUser'),
            '$timezone' => '%cosnics.libraries.calendar.timezone%',
            '$maintenanceMode' => '%cosnics.libraries.service.maintenanceMode%'
        ]
    );
    $services->set(ApplicationFactory::class);

    $services->set(ErrorHandler::class)->args(
        ['$themeSystemPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder')]
    );

    $services->set('Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger')->factory(
        [service(ExceptionLoggerFactory::class), 'createExceptionLogger']
    );

    $services->alias(ExceptionLoggerInterface::class, 'Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger');

    $services->set(ExceptionLoggerFactory::class)->args(
        ['$errorHandlingConfiguration' => '%cosnics.libraries.protocol.error.handling%']
    );

    $services->set(ClassnameUtilities::class);
    $services->set(DataClassUrlGenerator::class);
    $services->set(UrlGenerator::class);
    $services->set(ResourceGenerator::class);
    $services->set(ActionResultRenderer::class);
};
