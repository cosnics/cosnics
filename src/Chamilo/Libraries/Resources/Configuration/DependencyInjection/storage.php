<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\DependencyInjection\CompilerPass\DoctrineEventListenerCompilerPass;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorRegistry;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionVariableTranslatorRegistry;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Chamilo\Libraries\Storage\Factory\DataClassFactory;
use Chamilo\Libraries\Storage\Factory\DoctrineEntityManagerFactory;
use Chamilo\Libraries\Storage\Factory\DoctrineMappingDriverFactory;
use Chamilo\Libraries\Storage\Factory\RepositoryFactory;
use Chamilo\Libraries\Storage\Factory\SymfonyCacheAdapterFactory;
use Chamilo\Libraries\Storage\Factory\TreeListenerFactory;
use Chamilo\Libraries\Storage\Repository\DataClassDatabase;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Repository\DisplayOrderRepository;
use Chamilo\Libraries\Storage\Service\CacheDataPreLoaderManager;
use Chamilo\Libraries\Storage\Service\Condition\AndConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\ComparisonConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\ContainsConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\EndsWithConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\EqualityConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\InConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\NotConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\OrConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\PatternMatchConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\RegularExpressionConditionTranslator;
use Chamilo\Libraries\Storage\Service\Condition\StartsWithConditionTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\CaseConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\CaseElementConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\DateFormatConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\DistinctConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\FunctionConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\OperationConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\PropertiesConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\PropertyConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\StaticConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\DisplayOrderExceptionRenderer;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Chamilo\Libraries\Storage\Service\NoSuchObjectExceptionRenderer;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\Storage\Service\StorageLastInsertedIdentifierExceptionRenderer;
use Chamilo\Libraries\Storage\Service\StorageMethodExceptionRenderer;
use Chamilo\Libraries\Storage\Service\StorageNoResultExceptionRenderer;
use Chamilo\Libraries\Storage\Service\SymfonyCacheAdapterManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Gedmo\Tree\TreeListener;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(DataClassRepositoryCache::class);

    $services->set(DataClassFactory::class);

    $services->set(DisplayOrderRepository::class);
    $services->set(DisplayOrderHandler::class);

    $services->set(PropertyMapper::class);
    $services->set(SearchQueryConditionGenerator::class);

    $services->set(Connection::class)->factory([DriverManager::class, 'getConnection'])->args(
        ['%cosnics.libraries.storage.database%']
    );

    $services->set('Doctrine\DBAL\Connection\Session')->factory([DriverManager::class, 'getConnection'])->args(
        ['%cosnics.libraries.storage.database.session%']
    );

    $services->set(DataClassDatabase::class);

    $services->alias(DataClassRepository::class, 'Chamilo\Libraries\Storage\Repository\Doctrine\DataClassRepository');
    $services->set('Chamilo\Libraries\Storage\Repository\Doctrine\DataClassRepository', DataClassRepository::class)
        ->args([
            '$dataClassDatabase' => service(DataClassDatabase::class),
            '$queryCacheEnabled' => '%cosnics.libraries.storage.enableQueryCache%',
        ]);

    $services->set(QueryBuilderConfigurator::class);

    $services->set(ConditionTranslatorRegistry::class);
    $services->set(ConditionVariableTranslatorRegistry::class);

    $services->set(CaseConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(CaseElementConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(DateFormatConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(DistinctConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(FunctionConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(OperationConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(PropertiesConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(PropertyConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);
    $services->set(StaticConditionVariableTranslator::class)->tag(ConditionVariableTranslatorInterface::class);

    $services->set(ComparisonConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(EqualityConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(InConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(AndConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(OrConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(NotConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(PatternMatchConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(RegularExpressionConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(ContainsConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(StartsWithConditionTranslator::class)->tag(ConditionTranslatorInterface::class);
    $services->set(EndsWithConditionTranslator::class)->tag(ConditionTranslatorInterface::class);

    $services->set(CacheDataPreLoaderManager::class);
    $services->set(SymfonyCacheAdapterManager::class);
    $services->set(SymfonyCacheAdapterFactory::class);

    $services->set(DisplayOrderExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(NoSuchObjectExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(StorageLastInsertedIdentifierExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(StorageMethodExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(StorageNoResultExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);

    $services->set(Configuration::class);
    $services->set(RepositoryFactory::class);

    $services->set(TreeListenerFactory::class);
    $services->set(TreeListener::class)->factory([service(TreeListenerFactory::class), 'getTreeListener'])->tag(
        DoctrineEventListenerCompilerPass::TAG_EVENT_LISTENER
    );

    $services->set(DoctrineMappingDriverFactory::class)->args(
        ['$cacheAdapter' => service('Chamilo\Libraries\StorageDoctrineMappingDriverCacheAdapter')]
    );

    $services->set(MappingDriver::class)->factory([service(DoctrineMappingDriverFactory::class), 'createMappingDriver']
    );

    $services->set('Chamilo\Libraries\Storage\EntityManagerCacheAdapter', ArrayAdapter::class)->tag(
        AdapterInterface::class
    )->factory([service(SymfonyCacheAdapterFactory::class), 'createArrayAdapter']);

    $services->set('Chamilo\Libraries\StorageDoctrineMappingDriverCacheAdapter', ArrayAdapter::class)->tag(
        AdapterInterface::class
    )->factory([service(SymfonyCacheAdapterFactory::class), 'createArrayAdapter']);

    $services->set(DoctrineEntityManagerFactory::class)->args(
        [
            '$mappingDriver' => service(MappingDriver::class),
            '$doctrineConnection' => service(Connection::class),
            '$cacheAdapter' => service('Chamilo\Libraries\Storage\EntityManagerCacheAdapter')
        ]
    );
    $services->set(EntityManager::class)->factory([service(DoctrineEntityManagerFactory::class), 'createEntityManager']
    );
};
