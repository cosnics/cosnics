<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Storage\Architecture\Domain\ConditionPartCache;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Factory\DataClassFactory;
use Chamilo\Libraries\Storage\Repository\DisplayOrderRepository;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\Storage\Service\StorageAliasGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ConditionPartCache::class);
    $services->set(DataClassRepositoryCache::class);

    $services->set(DataClassFactory::class);

    $services->set(DisplayOrderRepository::class);
    $services->set(DisplayOrderHandler::class);

    $services->set(PropertyMapper::class);
    $services->set(SearchQueryConditionGenerator::class);
    $services->set(StorageAliasGenerator::class);
};
