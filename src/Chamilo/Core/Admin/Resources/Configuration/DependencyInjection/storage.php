<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Storage\Repository\LanguageRepository;
use Chamilo\Core\Admin\Storage\Repository\OnlineRepository;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(OnlineRepository::class);
    $services->set(LanguageRepository::class);
};
