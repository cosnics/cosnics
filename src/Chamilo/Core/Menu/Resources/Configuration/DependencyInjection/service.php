<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemFormDataHandler;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ItemService::class)->args(
        ['$fallbackIsoCodes' => '%cosnics.libraries.userInterface.translation.language.fallback%']
    );
    $services->set(ItemFormDataHandler::class);

    $services->set(CachedItemService::class)->args(
        ['$cacheAdapter' => service('Chamilo\Core\Menu\Service\Cache\ItemCacheProvider')]
    )->tag(CacheDataPreLoaderInterface::class);

    $services->set('Chamilo\Core\Menu\Service\Cache\ItemCacheProvider', FilesystemAdapter::class)->args(
        ['$namespace' => 'Chamilo\Core\Menu\Item']
    )->tag(AdapterInterface::class)->factory([service(SymfonyCacheAdapterFactory::class), 'createFilesystemAdapter']);
};
