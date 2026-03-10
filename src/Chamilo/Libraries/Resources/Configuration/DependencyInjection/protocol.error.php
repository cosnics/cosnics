<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Error\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\Error\Factory\ExceptionLoggerFactory;
use Chamilo\Libraries\Protocol\Error\Service\ErrorHandler;
use Chamilo\Libraries\Protocol\Error\Service\NoSuchClassExceptionRenderer;
use Chamilo\Libraries\Protocol\Error\Service\NoSuchParameterExceptionRenderer;
use Chamilo\Libraries\Protocol\Error\Service\PlatformNotAvailableExceptionRenderer;
use Chamilo\Libraries\Protocol\Error\Service\UserExceptionRenderer;
use Chamilo\Libraries\Protocol\Error\Service\UserExceptionResponseRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

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

    $services->set(UserExceptionResponseRenderer::class);
    $services->set(UserExceptionRendererRegistry::class);

    $services->set(NoSuchClassExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(NoSuchParameterExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(PlatformNotAvailableExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(UserExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
};
