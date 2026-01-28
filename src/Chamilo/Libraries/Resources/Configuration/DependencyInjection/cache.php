<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Chamilo\Libraries\Storage\Service\CacheDataPreLoaderManager;
use Chamilo\Libraries\Storage\Service\SymfonyCacheAdapterManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(CacheDataPreLoaderManager::class);
    $services->set(SymfonyCacheAdapterManager::class);
    $services->set(SymfonyCacheAdapterFactory::class);
};
