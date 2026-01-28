<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Storage\Architecture\Domain\DataSourceName;
use Chamilo\Libraries\Storage\Factory\ConnectionFactory;
use Chamilo\Libraries\Storage\Repository\DataClassDatabase;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Repository\NestedSetDataClassRepository;
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
use Chamilo\Libraries\Storage\Service\Condition\SubselectConditionTranslator;
use Chamilo\Libraries\Storage\Service\ConditionPartTranslator;
use Chamilo\Libraries\Storage\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\Service\ConditionVariable\CaseConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\CaseElementConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\DateFormatConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\DistinctConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\FunctionConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\OperationConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\PropertiesConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\PropertyConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\ConditionVariable\StaticConditionVariableTranslator;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Doctrine\DBAL\Connection;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(DataSourceName::class)->args(['%chamilo.configuration.database%']);
    $services->set(ConnectionFactory::class);
    $services->set(Connection::class)->factory([service(ConnectionFactory::class), 'getConnection']);

    $services->set('Doctrine\DBAL\Connection\Session')->factory([service(ConnectionFactory::class), 'getConnection']);

    $services->set(DataClassDatabase::class);

    $services->alias(DataClassRepository::class, 'Chamilo\Libraries\Storage\Repository\Doctrine\DataClassRepository');
    $services->set('Chamilo\Libraries\Storage\Repository\Doctrine\DataClassRepository', DataClassRepository::class)
        ->args([
            '$dataClassDatabase' => service(DataClassDatabase::class),
            '$queryCacheEnabled' => '%chamilo.configuration.debug.enable_query_cache%',
        ]);

    $services->alias(
        NestedSetDataClassRepository::class,
        'Chamilo\Libraries\Storage\Repository\Doctrine\NestedSetDataClassRepository'
    );
    $services->set(
        'Chamilo\Libraries\Storage\Repository\Doctrine\NestedSetDataClassRepository',
        NestedSetDataClassRepository::class
    )->args(['$dataClassRepository' => service('Chamilo\Libraries\Storage\Repository\Doctrine\DataClassRepository')]);

    $services->set(QueryBuilderConfigurator::class);

    $services->set(ConditionPartTranslatorService::class)->args(
        ['$queryCacheEnabled' => '%chamilo.configuration.debug.enable_query_cache%']
    );

    $services->set(CaseConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(CaseElementConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(DateFormatConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(DistinctConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(FunctionConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(OperationConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(PropertiesConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(PropertyConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(StaticConditionVariableTranslator::class)->tag(ConditionPartTranslator::class);

    $services->set(ComparisonConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(EqualityConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(InConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(AndConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(OrConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(NotConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(PatternMatchConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(RegularExpressionConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(SubselectConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(ContainsConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(StartsWithConditionTranslator::class)->tag(ConditionPartTranslator::class);
    $services->set(EndsWithConditionTranslator::class)->tag(ConditionPartTranslator::class);
};