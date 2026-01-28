<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Service\Finder\BasicBundlesGenerator;
use Chamilo\Core\Admin\Service\Finder\InternationalizationBundlesGenerator;
use Chamilo\Core\Admin\Service\Finder\PackageBundlesGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(BasicBundlesGenerator::class);
    $services->set(InternationalizationBundlesGenerator::class);
    $services->set(PackageBundlesGenerator::class);
};
