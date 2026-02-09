<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\UserInterface\Translation\Factory\TranslatorFactory;
use Chamilo\Libraries\UserInterface\Translation\Service\TranslationCacheService;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(TranslatorFactory::class);
    $services->set(TranslationCacheService::class)->tag(CacheDataPreLoaderInterface::class);
};
