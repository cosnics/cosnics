<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set('Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder', ThemePathBuilder::class)
        ->args([
            '$pathBuilder' => service(SystemPathBuilder::class),
            '$theme' => '%cosnics.libraries.userInterface.theme%',
        ]);

    $services->set('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder', ThemePathBuilder::class)->args([
        '$pathBuilder' => service(WebPathBuilder::class),
        '$theme' => '%cosnics.libraries.userInterface.theme%',
    ]);
};
