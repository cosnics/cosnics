<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Service;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase;
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
use Doctrine\DBAL\Query\QueryBuilder;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ParametersProcessor
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService
     */
    protected $conditionPartTranslatorService;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    protected $storageAliasGenerator;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     */
    public function __construct(
        ConditionPartTranslatorService $conditionPartTranslatorService, StorageAliasGenerator $storageAliasGenerator
    )
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService
     */
    public function getConditionPartTranslatorService()
    {
        return $this->conditionPartTranslatorService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     */
    public function setConditionPartTranslatorService(ConditionPartTranslatorService $conditionPartTranslatorService)
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    public function getStorageAliasGenerator()
    {
        return $this->storageAliasGenerator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     */
    public function setStorageAliasGenerator(StorageAliasGenerator $storageAliasGenerator)
    {
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @param string $dataClassName
     *
     * @throws \Exception
     */
    protected function handleCompositeDataClassJoins($dataClassName, DataClassRetrieveParameters $parameters)
    {
        if ($parameters->getJoins() instanceof Joins)
        {
            $dataClassProperties = $parameters->getDataClassProperties();

            foreach ($parameters->getJoins()->get() as $join)
            {
                if (is_subclass_of($join->get_data_class(), CompositeDataClass::class))
                {
                    if (is_subclass_of($dataClassName, $join->get_data_class()))
                    {
                        $dataClassProperties->add(new PropertiesConditionVariable($join->get_data_class()));
                    }
                }
            }
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters
     */
    public function handleDataClassCountGroupedParameters(DataClassCountGroupedParameters $parameters)
    {
        $dataClassProperties = $parameters->getDataClassProperties();
        $dataClassProperties->add(
            new FunctionConditionVariable(FunctionConditionVariable::COUNT, new StaticConditionVariable(1))
        );

        return $parameters;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters
     */
    public function handleDataClassCountParameters(DataClassCountParameters $parameters)
    {
        $dataClassProperties = $parameters->getDataClassProperties();

        if ($dataClassProperties instanceof DataClassProperties)
        {
            $dataClassPropertyVariable = $dataClassProperties->get()[0];
        }
        else
        {
            $dataClassPropertyVariable = new StaticConditionVariable(1);
        }

        $countVariable = new FunctionConditionVariable(FunctionConditionVariable::COUNT, $dataClassPropertyVariable);

        $parameters->setDataClassProperties(new DataClassProperties(array($countVariable)));

        return $parameters;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters
     */
    public function handleDataClassDistinctParameters(DataClassDistinctParameters $parameters)
    {
        $existingConditionVariables = $parameters->getDataClassProperties()->get();

        $parameters->setDataClassProperties(
            new DataClassProperties(array(new DistinctConditionVariable($existingConditionVariables)))
        );

        return $parameters;
    }

    /**
     *
     * @param string $dataClassName ;
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters
     * @throws \Exception
     */
    public function handleDataClassRetrieveParameters($dataClassName, DataClassRetrieveParameters $parameters)
    {
        $this->setDataClassPropertiesClassName($dataClassName, $parameters);

        $parameters->setCount(1);
        $parameters->setOffset(0);

        $this->handleCompositeDataClassJoins($dataClassName, $parameters);

        return $parameters;
    }

    /**
     *
     * @param string $dataClassName ;
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     * @throws \Exception
     */
    public function handleDataClassRetrievesParameters($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $this->setDataClassPropertiesClassName($dataClassName, $parameters);
        $this->handleCompositeDataClassJoins($dataClassName, $parameters);

        return $parameters;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processCondition(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, Condition $condition = null
    )
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->where($this->translate($dataClassDatabase, $condition));
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties|null $properties
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processDataClassProperties(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, DataClassProperties $properties = null
    )
    {
        if ($properties instanceof DataClassProperties)
        {
            foreach ($properties->get() as $conditionVariable)
            {
                $queryBuilder->addSelect($this->translate($dataClassDatabase, $conditionVariable));
            }
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\Query\GroupBy|null $groupBy
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processGroupBy(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, GroupBy $groupBy = null
    )
    {
        if ($groupBy instanceof GroupBy)
        {
            foreach ($groupBy->get() as $groupByVariable)
            {
                $queryBuilder->addGroupBy($this->translate($dataClassDatabase, $groupByVariable));
            }
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processHavingCondition(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, Condition $condition = null
    )
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->having($this->translate($dataClassDatabase, $condition));
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Joins|null $joins
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processJoins(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, $dataClassName, Joins $joins = null
    )
    {
        $storageAliasGenerator = $this->getStorageAliasGenerator();

        if ($joins instanceof Joins)
        {
            foreach ($joins->get() as $join)
            {
                $joinCondition = $this->translate($dataClassDatabase, $join->get_condition());
                $joinDataClassName = $join->get_data_class();

                switch ($join->get_type())
                {
                    case Join::TYPE_NORMAL :
                        $queryBuilder->join(
                            $storageAliasGenerator->getTableAlias($dataClassName::get_table_name()),
                            $joinDataClassName::get_table_name(),
                            $storageAliasGenerator->getTableAlias($joinDataClassName::get_table_name()), $joinCondition
                        );
                        break;
                    case Join::TYPE_RIGHT :
                        $queryBuilder->rightJoin(
                            $storageAliasGenerator->getTableAlias($dataClassName::get_table_name()),
                            $joinDataClassName::get_table_name(),
                            $storageAliasGenerator->getTableAlias($joinDataClassName::get_table_name()), $joinCondition
                        );
                        break;
                    case Join::TYPE_LEFT :
                        $queryBuilder->leftJoin(
                            $storageAliasGenerator->getTableAlias($dataClassName::get_table_name()),
                            $joinDataClassName::get_table_name(),
                            $storageAliasGenerator->getTableAlias($joinDataClassName::get_table_name()), $joinCondition
                        );
                        break;
                }
            }
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param integer|null $count
     * @param integer|null $offset
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processLimit(QueryBuilder $queryBuilder, $count = null, $offset = null)
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
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderByCollection
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processOrderByCollection(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, array $orderByCollection = null
    )
    {
        if (is_null($orderByCollection))
        {
            $orderByCollection = [];
        }
        elseif (!is_array($orderByCollection) && $orderByCollection instanceof OrderBy)
        {
            $orderByCollection = array($orderByCollection);
        }

        foreach ($orderByCollection as $orderBy)
        {
            $queryBuilder->addOrderBy(
                $this->translate($dataClassDatabase, $orderBy->getConditionVariable()),
                ($orderBy->getDirection() == SORT_DESC ? 'DESC' : 'ASC')
            );
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @param string $dataClassName
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function processParameters(
        DataClassDatabase $dataClassDatabase, QueryBuilder $queryBuilder, DataClassParameters $parameters,
        $dataClassName
    )
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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $dataClassRetrieveParameters
     *
     * @throws \Exception
     */
    public function setDataClassPropertiesClassName(
        $dataClassName, DataClassRetrieveParameters $dataClassRetrieveParameters
    )
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class) &&
            get_parent_class($dataClassName) == CompositeDataClass::class)
        {
            $propertiesClassName = $dataClassName;
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && $dataClassName::is_extended())
        {
            $propertiesClassName = $dataClassName;
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && !$dataClassName::is_extended())
        {
            $propertiesClassName = $dataClassName::parent_class_name();
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
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\Query\ConditionPart $conditionPart
     *
     * @return string
     */
    protected function translate(DataClassDatabase $dataClassDatabase, ConditionPart $conditionPart)
    {
        return $this->getConditionPartTranslatorService()->translate($dataClassDatabase, $conditionPart);
    }
}

