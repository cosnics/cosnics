<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorCollection;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionVariableTranslatorCollection;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\JoinTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\GroupBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Joins;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class QueryBuilderConfigurator
{
    protected ConditionTranslatorCollection $conditionTranslatorCollection;

    protected ConditionVariableTranslatorCollection $conditionVariableTranslatorCollection;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        ConditionTranslatorCollection $conditionTranslatorCollection,
        ConditionVariableTranslatorCollection $conditionVariableTranslatorCollection,
        StorageAliasGenerator $storageAliasGenerator
    )
    {
        $this->conditionTranslatorCollection = $conditionTranslatorCollection;
        $this->conditionVariableTranslatorCollection = $conditionVariableTranslatorCollection;
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
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

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function applyUpdate(
        QueryBuilder $queryBuilder, UpdateProperties $properties, ConditionInterface $condition
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

    public function getConditionTranslatorCollection(): ConditionTranslatorCollection
    {
        return $this->conditionTranslatorCollection;
    }

    public function getConditionVariableTranslatorCollection(): ConditionVariableTranslatorCollection
    {
        return $this->conditionVariableTranslatorCollection;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function processCondition(
        QueryBuilder $queryBuilder, ?ConditionInterface $condition = null, ?bool $enableAliasing = true
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
        QueryBuilder $queryBuilder, GroupBy $groupBy = new GroupBy()
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
        QueryBuilder $queryBuilder, ?ConditionInterface $condition = null
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
        QueryBuilder $queryBuilder, string $dataClassStorageUnitName, Joins $joins = new Joins()
    ): void
    {
        $storageAliasGenerator = $this->getStorageAliasGenerator();

        foreach ($joins as $join) {
            $joinCondition = $this->translateCondition($queryBuilder, $join->getCondition());

            /**
             * @var class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $joinDataClassName
             */
            $joinDataClassName = $join->getDataClassName();
            $joinDataClassStorageUnitName = $joinDataClassName::getStorageUnitName();

            $fromAlias = $storageAliasGenerator->getTableAlias($dataClassStorageUnitName);
            $joinAlias = $storageAliasGenerator->getTableAlias($joinDataClassStorageUnitName);

            switch ($join->getType()) {
                case JoinTypeEnum::NORMAL :
                    $queryBuilder->join($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
                case JoinTypeEnum::RIGHT :
                    $queryBuilder->rightJoin($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
                case JoinTypeEnum::LEFT :
                    $queryBuilder->leftJoin($fromAlias, $joinDataClassStorageUnitName, $joinAlias, $joinCondition);
                    break;
            }
        }
    }

    protected function processLimit(QueryBuilder $queryBuilder, ?int $count = null, ?int $offset = null): void
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
    protected function processOrderBy(
        QueryBuilder $queryBuilder, OrderBy $orderBy = new OrderBy()
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
        QueryBuilder $queryBuilder, RetrieveProperties $properties = new RetrieveProperties()
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
        QueryBuilder $queryBuilder, ConditionInterface $condition, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionTranslatorCollection()->translate(
            $queryBuilder, $condition, $enableAliasing
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function translateConditionVariable(
        QueryBuilder $queryBuilder, ConditionVariableInterface $conditionVariable, ?bool $enableAliasing = true
    ): string
    {
        return $this->getConditionVariableTranslatorCollection()->translate(
            $queryBuilder, $conditionVariable, $enableAliasing
        );
    }
}

