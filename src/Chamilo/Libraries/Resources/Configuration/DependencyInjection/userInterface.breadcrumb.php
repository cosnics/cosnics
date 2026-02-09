<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbTrailRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(BreadcrumbTrail::class);
    $services->set(BreadcrumbGenerator::class);
    $services->set(BreadcrumbTrailRenderer::class);
};