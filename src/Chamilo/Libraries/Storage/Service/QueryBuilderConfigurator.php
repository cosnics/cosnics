<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionPart;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\GroupBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Join;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Joins;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
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
        QueryBuilder $queryBuilder, StorageParameters $parameters, string $dataClassStorageUnitName
    ): void
    {
        $this->processCondition($queryBuilder, $parameters->getCondition());
        $this->processJoins($queryBuilder, $dataClassStorageUnitName, $parameters->getJoins());
        $this->processRetrieveProperties($queryBuilder, $parameters->getRetrieveProperties());
        $this->processOrderBy($queryBuilder, $parameters->getOrderBy());
        $this->processGroupBy($queryBuilder, $parameters->getGroupBy());
        $this->processHavingCondition($queryBuilder, $parameters->getHavingCondition());
        $this->processLimit($queryBuilder, $parameters->getCount(), $parameters->getOffset());
    }

    public function applyUpdate(
        QueryBuilder $queryBuilder, UpdateProperties $properties, Condition $condition
    ): void
    {
        foreach ($properties as $dataClassProperty)
        {
            $key = $this->translateConditionPart(
                $queryBuilder, $dataClassProperty->getPropertyConditionVariable(), false
            );

            $value = $this->translateConditionPart(
                $queryBuilder, $dataClassProperty->getValueConditionVariable(), false
            );

            $queryBuilder->set($key, $value);
        }

        $this->processCondition($queryBuilder, $condition, false);
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
        QueryBuilder $queryBuilder, ?Condition $condition = null, ?bool $enableAliasing = true
    ): void
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->where($this->translateConditionPart($queryBuilder, $condition, $enableAliasing));
        }
    }

    protected function processGroupBy(
        QueryBuilder $queryBuilder, GroupBy $groupBy = new GroupBy()
    ): void
    {
        foreach ($groupBy as $groupByVariable)
        {
            $queryBuilder->addGroupBy($this->translateConditionPart($queryBuilder, $groupByVariable));
        }
    }

    protected function processHavingCondition(
        QueryBuilder $queryBuilder, ?Condition $condition = null
    ): void
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->having($this->translateConditionPart($queryBuilder, $condition));
        }
    }

    protected function processJoins(
        QueryBuilder $queryBuilder, string $dataClassStorageUnitName, Joins $joins = new Joins()
    ): void
    {
        $storageAliasGenerator = $this->getStorageAliasGenerator();

        foreach ($joins as $join)
        {
            $joinCondition = $this->translateConditionPart($queryBuilder, $join->getCondition());

            /**
             * @var class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $joinDataClassName
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
        QueryBuilder $queryBuilder, OrderBy $orderBy = new OrderBy()
    ): void
    {
        foreach ($orderBy as $orderByProperty)
        {
            $queryBuilder->addOrderBy(
                $this->translateConditionPart($queryBuilder, $orderByProperty->getConditionVariable()),
                ($orderByProperty->getDirection() == SORT_DESC ? 'DESC' : 'ASC')
            );
        }
    }

    protected function processRetrieveProperties(
        QueryBuilder $queryBuilder, RetrieveProperties $properties = new RetrieveProperties()
    ): void
    {
        foreach ($properties as $conditionVariable)
        {
            $queryBuilder->addSelect($this->translateConditionPart($queryBuilder, $conditionVariable));
        }
    }

    protected function translateConditionPart(
        QueryBuilder $queryBuilder, ConditionPart $conditionPart, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate(
            $queryBuilder, $conditionPart, $enableAliasing
        );
    }
}

