<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorRegistry;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionVariableTranslatorRegistry;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\JoinTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\GroupBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Joins;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class QueryBuilderConfigurator
{
    public function __construct(
        protected ConditionTranslatorRegistry $conditionTranslatorRegistry,
        protected ConditionVariableTranslatorRegistry $conditionVariableTranslatorRegistry
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function applyParameters(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, StorageParameters $parameters
    ): void
    {
        $this->processCondition($queryBuilder, $parameters->getCondition());
        $this->processJoins($queryBuilder, $parameters->getJoins());
        $this->processRetrieveProperties($queryBuilder, $parameters->getRetrieveProperties());
        $this->processOrderBy($queryBuilder, $parameters->getOrderBy());
        $this->processGroupBy($queryBuilder, $parameters->getGroupBy());
        $this->processHavingCondition($queryBuilder, $parameters->getHavingCondition());
        $this->processLimit($queryBuilder, $parameters->getCount(), $parameters->getOffset());
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function applyUpdate(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, UpdateProperties $properties, ConditionInterface $condition
    ): void
    {
        foreach ($properties as $dataClassProperty) {
            $key = $this->translateConditionVariable(
                $queryBuilder, $dataClassProperty->getPropertyConditionVariable(), false
            );

            $value = $this->translateConditionVariable(
                $queryBuilder, $dataClassProperty->getValueConditionVariable(), false
            );

            $queryBuilder->set($key, $value);
        }

        $this->processCondition($queryBuilder, $condition, false);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processCondition(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, ?ConditionInterface $condition = null,
        ?bool $enableAliasing = true
    ): void
    {
        if ($condition instanceof ConditionInterface) {
            $queryBuilder->where($this->translateCondition($queryBuilder, $condition, $enableAliasing));
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processGroupBy(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, GroupBy $groupBy = new GroupBy()
    ): void
    {
        foreach ($groupBy as $groupByVariable) {
            $queryBuilder->addGroupBy($this->translateConditionVariable($queryBuilder, $groupByVariable));
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processHavingCondition(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, ?ConditionInterface $condition = null
    ): void
    {
        if ($condition instanceof ConditionInterface) {
            $queryBuilder->having($this->translateCondition($queryBuilder, $condition));
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processJoins(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, Joins $joins = new Joins()
    ): void
    {
        if ($queryBuilder instanceof ORMQueryBuilder) {
            $this->processORMJoins($queryBuilder, $joins);
        }
        else {
            $this->processLegacyJoins($queryBuilder, $joins);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processLegacyJoins(
        DBALQueryBuilder $queryBuilder, Joins $joins = new Joins()
    ): void
    {
        foreach ($joins as $join) {
            if ($join->condition instanceof ConditionInterface) {
                $joinCondition = $this->translateCondition($queryBuilder, $join->condition);
            }

            else {
                $joinCondition = null;
            }

            $fromAlias = $join->propertyConditionVariable->getDataClassName()::getAlias();
            $joinAlias = $join->entityClassName::getAlias();
            $joinDataClassStorageUnitName = $join->entityClassName::getStorageUnitName();

            switch ($join->type) {
                case JoinTypeEnum::NORMAL :
                    $queryBuilder->join($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
                case JoinTypeEnum::LEFT :
                    $queryBuilder->leftJoin($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
            }
        }
    }

    protected function processLimit(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, ?int $count = null, ?int $offset = null
    ): void
    {
        if ($count > 0) {
            $queryBuilder->setMaxResults(intval($count));
        }

        if ($offset > 0) {
            $queryBuilder->setFirstResult(intval($offset));
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processORMJoins(
        ORMQueryBuilder $queryBuilder, Joins $joins = new Joins()
    ): void
    {
        foreach ($joins as $join) {
            if ($join->condition instanceof ConditionInterface) {
                $joinCondition = $this->translateCondition($queryBuilder, $join->condition);
            }

            else {
                $joinCondition = null;
            }
            $joinAlias = $join->entityClassName::getAlias();
            $propertyConditionVariable =
                $this->translateConditionVariable($queryBuilder, $join->propertyConditionVariable);

            switch ($join->type) {
                case JoinTypeEnum::NORMAL :
                    $queryBuilder->join($propertyConditionVariable, $joinAlias, Join::ON, $joinCondition);
                    break;
                case JoinTypeEnum::LEFT :
                    $queryBuilder->leftJoin($propertyConditionVariable, $joinAlias, Join::ON, $joinCondition);
                    break;
            }
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processOrderBy(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, OrderBy $orderBy = new OrderBy()
    ): void
    {
        foreach ($orderBy as $orderByProperty) {
            $queryBuilder->addOrderBy(
                $this->translateConditionVariable($queryBuilder, $orderByProperty->getConditionVariable()),
                ($orderByProperty->getDirection() == SORT_DESC ? 'DESC' : 'ASC')
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processRetrieveProperties(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, RetrieveProperties $properties = new RetrieveProperties()
    ): void
    {
        foreach ($properties as $conditionVariable) {
            $queryBuilder->addSelect($this->translateConditionVariable($queryBuilder, $conditionVariable));
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function translateCondition(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, ConditionInterface $condition, ?bool $enableAliasing = true
    ): string
    {
        return $this->conditionTranslatorRegistry->translate(
            $queryBuilder, $condition, $enableAliasing
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function translateConditionVariable(
        DBALQueryBuilder|ORMQueryBuilder $queryBuilder, ConditionVariableInterface $conditionVariable,
        ?bool $enableAliasing = true
    ): string
    {
        return $this->conditionVariableTranslatorRegistry->translate(
            $queryBuilder, $conditionVariable, $enableAliasing
        );
    }
}

