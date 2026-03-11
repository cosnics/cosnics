<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\NoSuchClassExceptionRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\NoSuchParameterExceptionRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\PlatformNotAvailableExceptionRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\UserExceptionRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\UserExceptionResponseRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(UserExceptionResponseRenderer::class);
    $services->set(UserExceptionRendererRegistry::class);

    $services->set(NoSuchClassExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(NoSuchParameterExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(PlatformNotAvailableExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(UserExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
};
