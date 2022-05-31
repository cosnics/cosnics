<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Service;

use Chamilo\Libraries\Storage\DataManager\AdoDb\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ParametersProcessor
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

    public function run(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, DataClassParameters $parameters,
        string $dataClassName
    ): QueryBuilder
    {
        $this->processCondition($dataClassDatabase, $queryBuilder, $parameters->getCondition());
        $this->processJoins($dataClassDatabase, $queryBuilder, $dataClassName, $parameters->getJoins());
        $this->processDataClassProperties(
            $dataClassDatabase, $queryBuilder, $parameters->getRetrieveProperties()
        );
        $this->processOrderByCollection($dataClassDatabase, $queryBuilder, $parameters->getOrderBy());
        $this->processGroupBy($dataClassDatabase, $queryBuilder, $parameters->getGroupBy());
        $this->processHavingCondition($dataClassDatabase, $queryBuilder, $parameters->getHavingCondition());
        $this->processLimit($queryBuilder, $parameters->getCount(), $parameters->getOffset());

        return $queryBuilder;
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
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, ?Condition $condition = null
    ): QueryBuilder
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->where($this->translateConditionPart($dataClassDatabase, $condition));
        }

        return $queryBuilder;
    }

    protected function processDataClassProperties(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, ?RetrieveProperties $properties = null
    ): QueryBuilder
    {
        if ($properties instanceof RetrieveProperties)
        {
            foreach ($properties->get() as $conditionVariable)
            {
                $queryBuilder->addSelect($this->translateConditionPart($dataClassDatabase, $conditionVariable));
            }
        }

        return $queryBuilder;
    }

    protected function processGroupBy(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, GroupBy $groupBy = null
    ): QueryBuilder
    {
        if ($groupBy instanceof GroupBy)
        {
            foreach ($groupBy->get() as $groupByVariable)
            {
                $queryBuilder->addGroupBy($this->translateConditionPart($dataClassDatabase, $groupByVariable));
            }
        }

        return $queryBuilder;
    }

    protected function processHavingCondition(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, ?Condition $condition = null
    ): QueryBuilder
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->having($this->translateConditionPart($dataClassDatabase, $condition));
        }

        return $queryBuilder;
    }

    protected function processJoins(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, string $dataClassName, ?Joins $joins = null
    ): QueryBuilder
    {
        $storageAliasGenerator = $this->getStorageAliasGenerator();

        if ($joins instanceof Joins)
        {
            foreach ($joins->get() as $join)
            {
                $joinCondition = $this->translateConditionPart($dataClassDatabase, $join->getCondition());
                $joinDataClassName = $join->getDataClassName();

                switch ($join->getType())
                {
                    case Join::TYPE_NORMAL :
                        $queryBuilder->join(
                            $storageAliasGenerator->getTableAlias($dataClassName::getTableName()),
                            $joinDataClassName::getTableName(),
                            $storageAliasGenerator->getTableAlias($joinDataClassName::getTableName()), $joinCondition
                        );
                        break;
                    case Join::TYPE_RIGHT :
                        $queryBuilder->rightJoin(
                            $storageAliasGenerator->getTableAlias($dataClassName::getTableName()),
                            $joinDataClassName::getTableName(),
                            $storageAliasGenerator->getTableAlias($joinDataClassName::getTableName()), $joinCondition
                        );
                        break;
                    case Join::TYPE_LEFT :
                        $queryBuilder->leftJoin(
                            $storageAliasGenerator->getTableAlias($dataClassName::getTableName()),
                            $joinDataClassName::getTableName(),
                            $storageAliasGenerator->getTableAlias($joinDataClassName::getTableName()), $joinCondition
                        );
                        break;
                }
            }
        }

        return $queryBuilder;
    }

    protected function processLimit(QueryBuilder $queryBuilder, ?int $count = null, ?int $offset = null): QueryBuilder
    {
        if (intval($count) > 0)
        {
            $queryBuilder->setMaxResults(intval($count));
        }

        if (intval($offset) > 0)
        {
            $queryBuilder->setFirstResult(intval($offset));
        }

        return $queryBuilder;
    }

    protected function processOrderByCollection(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, ?OrderBy $orderBy = null
    ): QueryBuilder
    {
        if (!is_null($orderBy))
        {
            foreach ($orderBy->get() as $orderBy)
            {
                $queryBuilder->addOrderBy(
                    $this->translateConditionPart($dataClassDatabase, $orderBy->getConditionVariable()),
                    ($orderBy->getDirection() == SORT_DESC ? 'DESC' : 'ASC')
                );
            }
        }

        return $queryBuilder;
    }

    protected function translateConditionPart(DataClassDatabase $dataClassDatabase, ConditionPart $conditionPart
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate($dataClassDatabase, $conditionPart);
    }
}

