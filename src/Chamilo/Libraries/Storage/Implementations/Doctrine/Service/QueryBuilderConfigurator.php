<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Service;

use Chamilo\Libraries\Storage\Implementations\Doctrine\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Service\StorageAliasGenerator;
use Chamilo\Libraries\Storage\StorageParameters;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class QueryBuilderConfigurator
{

    protected ConditionPartTranslatorService $conditionPartTranslatorService;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        ConditionPartTranslatorService $conditionPartTranslatorService, StorageAliasGenerator $storageAliasGenerator
    )
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    public function applyParameters(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, StorageParameters $parameters,
        string $dataClassStorageUnitName
    ): void
    {
        $this->processCondition($dataClassDatabase, $queryBuilder, $parameters->getCondition());
        $this->processJoins($dataClassDatabase, $queryBuilder, $dataClassStorageUnitName, $parameters->getJoins());
        $this->processRetrieveProperties(
            $dataClassDatabase, $queryBuilder, $parameters->getRetrieveProperties()
        );
        $this->processOrderBy($dataClassDatabase, $queryBuilder, $parameters->getOrderBy());
        $this->processGroupBy($dataClassDatabase, $queryBuilder, $parameters->getGroupBy());
        $this->processHavingCondition($dataClassDatabase, $queryBuilder, $parameters->getHavingCondition());
        $this->processLimit($queryBuilder, $parameters->getCount(), $parameters->getOffset());
    }

    public function applyUpdate(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, UpdateProperties $properties,
        Condition $condition
    ): void
    {
        foreach ($properties as $dataClassProperty)
        {
            $key = $this->translateConditionPart(
                $dataClassDatabase, $dataClassProperty->getPropertyConditionVariable(), false
            );
            $value = $this->translateConditionPart(
                $dataClassDatabase, $dataClassProperty->getValueConditionVariable(), false
            );

            $queryBuilder->set($key, $value);
        }

        $this->processCondition($dataClassDatabase, $queryBuilder, $condition, false);
    }

    public function getConditionPartTranslatorService(): ConditionPartTranslatorService
    {
        return $this->conditionPartTranslatorService;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    protected function processCondition(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, ?Condition $condition = null,
        ?bool $enableAliasing = true
    ): void
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->where($this->translateConditionPart($dataClassDatabase, $condition, $enableAliasing));
        }
    }

    protected function processGroupBy(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, GroupBy $groupBy = new GroupBy()
    ): void
    {
        foreach ($groupBy as $groupByVariable)
        {
            $queryBuilder->addGroupBy($this->translateConditionPart($dataClassDatabase, $groupByVariable));
        }
    }

    protected function processHavingCondition(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, ?Condition $condition = null
    ): void
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->having($this->translateConditionPart($dataClassDatabase, $condition));
        }
    }

    protected function processJoins(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, string $dataClassStorageUnitName,
        Joins $joins = new Joins()
    ): void
    {
        $storageAliasGenerator = $this->getStorageAliasGenerator();

        foreach ($joins as $join)
        {
            $joinCondition = $this->translateConditionPart($dataClassDatabase, $join->getCondition());

            /**
             * @var class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $joinDataClassName
             */
            $joinDataClassName = $join->getDataClassName();
            $joinDataClassStorageUnitName = $joinDataClassName::getStorageUnitName();

            $fromAlias = $storageAliasGenerator->getTableAlias($dataClassStorageUnitName);
            $joinAlias = $storageAliasGenerator->getTableAlias($joinDataClassStorageUnitName);

            switch ($join->getType())
            {
                case Join::TYPE_NORMAL :
                    $queryBuilder->join($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
                case Join::TYPE_RIGHT :
                    $queryBuilder->rightJoin($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
                case Join::TYPE_LEFT :
                    $queryBuilder->leftJoin($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
            }
        }
    }

    protected function processLimit(QueryBuilder $queryBuilder, ?int $count = null, ?int $offset = null): void
    {
        if ($count > 0)
        {
            $queryBuilder->setMaxResults(intval($count));
        }

        if ($offset > 0)
        {
            $queryBuilder->setFirstResult(intval($offset));
        }
    }

    protected function processOrderBy(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, OrderBy $orderBy = new OrderBy()
    ): void
    {
        foreach ($orderBy as $orderByProperty)
        {
            $queryBuilder->addOrderBy(
                $this->translateConditionPart($dataClassDatabase, $orderByProperty->getConditionVariable()),
                ($orderByProperty->getDirection() == SORT_DESC ? 'DESC' : 'ASC')
            );
        }
    }

    protected function processRetrieveProperties(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder,
        RetrieveProperties $properties = new RetrieveProperties()
    ): void
    {
        foreach ($properties as $conditionVariable)
        {
            $queryBuilder->addSelect($this->translateConditionPart($dataClassDatabase, $conditionVariable));
        }
    }

    protected function translateConditionPart(
        DataClassDatabase $dataClassDatabase, ConditionPart $conditionPart, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate(
            $dataClassDatabase, $conditionPart, $enableAliasing
        );
    }
}

