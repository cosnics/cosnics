<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Service;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Database\DataClassDatabase;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\ConditionPart;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

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

    public function getConditionPartTranslatorService(): ConditionPartTranslatorService
    {
        return $this->conditionPartTranslatorService;
    }

    public function setConditionPartTranslatorService(ConditionPartTranslatorService $conditionPartTranslatorService
    ): ParametersProcessor
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;

        return $this;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    public function setStorageAliasGenerator(StorageAliasGenerator $storageAliasGenerator): ParametersProcessor
    {
        $this->storageAliasGenerator = $storageAliasGenerator;

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function handleCompositeDataClassJoins(string $dataClassName, DataClassParameters $parameters
    ): ParametersProcessor
    {
        if ($parameters->getJoins() instanceof Joins)
        {
            $dataClassProperties = $parameters->getDataClassProperties();

            foreach ($parameters->getJoins()->get() as $join)
            {
                if (is_subclass_of($join->getDataClassName(), CompositeDataClass::class))
                {
                    if (is_subclass_of($dataClassName, $join->getDataClassName()))
                    {
                        $dataClassProperties->add(new PropertiesConditionVariable($join->getDataClassName()));
                    }
                }
            }
        }

        return $this;
    }

    public function handleDataClassCountGroupedParameters(DataClassCountGroupedParameters $parameters
    ): DataClassCountGroupedParameters
    {
        $dataClassProperties = $parameters->getDataClassProperties();
        $dataClassProperties->add(
            new FunctionConditionVariable(FunctionConditionVariable::COUNT, new StaticConditionVariable(1))
        );

        return $parameters;
    }

    public function handleDataClassCountParameters(DataClassCountParameters $parameters): DataClassCountParameters
    {
        $dataClassProperties = $parameters->getDataClassProperties();

        if ($dataClassProperties instanceof DataClassProperties)
        {
            $dataClassPropertyVariable = $dataClassProperties->getFirst();
        }
        else
        {
            $dataClassPropertyVariable = new StaticConditionVariable(1);
        }

        $countVariable = new FunctionConditionVariable(FunctionConditionVariable::COUNT, $dataClassPropertyVariable);

        $parameters->setDataClassProperties(new DataClassProperties(array($countVariable)));

        return $parameters;
    }

    public function handleDataClassDistinctParameters(DataClassDistinctParameters $parameters
    ): DataClassDistinctParameters
    {
        $existingConditionVariables = $parameters->getDataClassProperties()->get();

        $parameters->setDataClassProperties(
            new DataClassProperties(array(new DistinctConditionVariable($existingConditionVariables)))
        );

        return $parameters;
    }

    /**
     * @throws \Exception
     */
    public function handleDataClassRetrieveParameters(string $dataClassName, DataClassRetrieveParameters $parameters
    ): DataClassRetrieveParameters
    {
        $this->setDataClassPropertiesClassName($dataClassName, $parameters);

        $parameters->setCount(1);
        $parameters->setOffset(0);

        $this->handleCompositeDataClassJoins($dataClassName, $parameters);

        return $parameters;
    }

    /**
     * @throws \Exception
     */
    public function handleDataClassRetrievesParameters(string $dataClassName, DataClassRetrievesParameters $parameters
    ): DataClassRetrievesParameters
    {
        $this->setDataClassPropertiesClassName($dataClassName, $parameters);
        $this->handleCompositeDataClassJoins($dataClassName, $parameters);

        return $parameters;
    }

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @throws \ReflectionException
     */
    protected function processDataClassProperties(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, ?DataClassProperties $properties = null
    ): QueryBuilder
    {
        if ($properties instanceof DataClassProperties)
        {
            foreach ($properties->get() as $conditionVariable)
            {
                $queryBuilder->addSelect($this->translateConditionPart($dataClassDatabase, $conditionVariable));
            }
        }

        return $queryBuilder;
    }

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @throws \ReflectionException
     */
    public function processParameters(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, DataClassParameters $parameters,
        string $dataClassName
    ): QueryBuilder
    {
        $this->processCondition($dataClassDatabase, $queryBuilder, $parameters->getCondition());
        $this->processJoins($dataClassDatabase, $queryBuilder, $dataClassName, $parameters->getJoins());
        $this->processDataClassProperties(
            $dataClassDatabase, $queryBuilder, $parameters->getDataClassProperties()
        );
        $this->processOrderByCollection($dataClassDatabase, $queryBuilder, $parameters->getOrderBy());
        $this->processGroupBy($dataClassDatabase, $queryBuilder, $parameters->getGroupBy());
        $this->processHavingCondition($dataClassDatabase, $queryBuilder, $parameters->getHavingCondition());
        $this->processLimit($queryBuilder, $parameters->getCount(), $parameters->getOffset());

        return $queryBuilder;
    }

    /**
     * @throws \Exception
     */
    public function setDataClassPropertiesClassName(
        string $dataClassName, DataClassParameters $dataClassRetrieveParameters
    )
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class) &&
            get_parent_class($dataClassName) == CompositeDataClass::class)
        {
            $propertiesClassName = $dataClassName;
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && $dataClassName::isExtended())
        {
            $propertiesClassName = $dataClassName;
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && !$dataClassName::isExtended())
        {
            $propertiesClassName = $dataClassName::parentClassName();
        }
        else
        {
            $propertiesClassName = $dataClassName;
        }

        $dataClassRetrieveParameters->setDataClassProperties(
            new DataClassProperties(
                array(new PropertiesConditionVariable($propertiesClassName))
            )
        );
    }

    /**
     * @throws \ReflectionException
     */
    protected function translateConditionPart(DataClassDatabase $dataClassDatabase, ConditionPart $conditionPart
    ): string
    {
        return $this->getConditionPartTranslatorService()->translate($dataClassDatabase, $conditionPart);
    }
}

