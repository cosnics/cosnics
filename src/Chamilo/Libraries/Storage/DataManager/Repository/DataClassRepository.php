<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassBaseExtensionInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassTypeAwareInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\UuidDataClassInterface;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Query\Variable\DistinctConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class DataClassRepository
{
    public const ALIAS_MAX_SORT = 'max_sort';

    private DataClassDatabaseInterface $dataClassDatabase;

    private DataClassFactory $dataClassFactory;

    private DataClassRepositoryCache $dataClassRepositoryCache;

    private bool $queryCacheEnabled;

    public function __construct(
        DataClassRepositoryCache $dataClassRepositoryCache, DataClassDatabaseInterface $dataClassDatabase,
        DataClassFactory $dataClassFactory, bool $queryCacheEnabled = true
    )
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->dataClassFactory = $dataClassFactory;
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    protected function __count(string $dataClassName, DataClassCountParameters $parameters): int
    {
        $this->applyDataClassExtensionToParameters($dataClassName, $parameters);

        $parameters->setRetrieveProperties(
            new RetrieveProperties(
                [
                    new FunctionConditionVariable(
                        FunctionConditionVariable::COUNT,
                        $parameters->getRetrieveProperties()->getFirst(new StaticConditionVariable(1))
                    )
                ]
            )
        );

        return $this->getDataClassDatabase()->count(
            $this->determineDataClassStorageUnitName($dataClassName), $parameters
        );
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    protected function __countGrouped(string $dataClassName, DataClassCountGroupedParameters $parameters): array
    {
        $retrieveProperties = $parameters->getRetrieveProperties();
        $retrieveProperties->add(
            new FunctionConditionVariable(FunctionConditionVariable::COUNT, new StaticConditionVariable(1))
        );

        return $this->getDataClassDatabase()->countGrouped($dataClassName::getStorageUnitName(), $parameters);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    protected function __distinct(string $dataClassName, DataClassDistinctParameters $parameters): array
    {
        $parameters->setRetrieveProperties(
            new RetrieveProperties([new DistinctConditionVariable($parameters->getRetrieveProperties()->toArray())])
        );

        return $this->getDataClassDatabase()->distinct($dataClassName::getStorageUnitName(), $parameters);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     *
     * @return ?string[]
     */
    protected function __record(string $dataClassName, RecordRetrieveParameters|DataClassRetrieveParameters $parameters
    ): ?array
    {
        if (is_subclass_of($dataClassName, DataClassTypeAwareInterface::class) &&
            !is_subclass_of($dataClassName, DataClassBaseExtensionInterface::class))
        {
            $dataClassName = $this->determineDataExtensionClassName($dataClassName, $parameters);
        }

        $this->applyDataClassExtensionToParameters($dataClassName, $parameters);
        $this->applyDataClassPropertiesToParameters($dataClassName, $parameters);

        $parameters->returnSingleResult();

        return $this->getDataClassDatabase()->retrieve(
            $this->determineDataClassStorageUnitName($dataClassName), $parameters
        );
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    protected function __records(
        string $dataClassName, DataClassRetrievesParameters|RecordRetrievesParameters $parameters
    ): ArrayCollection
    {
        $this->applyDataClassExtensionToParameters($dataClassName, $parameters);
        $this->applyDataClassPropertiesToParameters($dataClassName, $parameters);

        return new ArrayCollection(
            $this->getDataClassDatabase()->retrieves($dataClassName::getStorageUnitName(), $parameters)
        );
    }

    /**
     * @template tInternalRetrieveClass
     *
     * @param class-string<tInternalRetrieveClass> $dataClassName
     *
     * @return ?tInternalRetrieveClass
     */
    protected function __retrieve(string $dataClassName, DataClassRetrieveParameters $parameters)
    {
        return $this->getDataClassFactory()->getDataClass(
            $dataClassName, $this->__record($dataClassName, $parameters)
        );
    }

    /**
     * @template tInternalRetrievesClass
     *
     * @param class-string<tInternalRetrievesClass> $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return ArrayCollection<tInternalRetrievesClass>
     */
    protected function __retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): ArrayCollection
    {
        $records = $this->__records($dataClassName, $parameters);
        $dataClasses = [];

        foreach ($records as $record)
        {
            if (is_subclass_of($dataClassName, DataClassTypeAwareInterface::class) &&
                !is_subclass_of($dataClassName, DataClassBaseExtensionInterface::class))
            {
                $factoryDataClassName = $record[DataClassTypeAwareInterface::PROPERTY_TYPE];

                //TODO: Do something here to expand $record to include the properties of the extension data class
            }
            else
            {
                $factoryDataClassName = $dataClassName;
            }

            $dataClasses[] = $this->getDataClassFactory()->getDataClass($factoryDataClassName, $record);
        }

        return new ArrayCollection($dataClasses);
    }

    protected function applyDataClassExtensionToParameters(
        string $dataClassName, DataClassParameters $dataClassParameters
    ): void
    {
        if (is_subclass_of($dataClassName, DataClassBaseExtensionInterface::class))
        {
            $typeDataClassName = $dataClassName::getExtensionDataClassName();

            $condition = new EqualityCondition(
                new PropertyConditionVariable($typeDataClassName, $typeDataClassName::PROPERTY_TYPE),
                new StaticConditionVariable($dataClassName)
            );

            $dataClassParameters->addConditionUsingAnd($condition);

            if (is_subclass_of($dataClassName, DataClassExtensionInterface::class))
            {
                $join = new Join(
                    $typeDataClassName, new EqualityCondition(
                        new PropertyConditionVariable($typeDataClassName, $typeDataClassName::PROPERTY_ID),
                        new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID)
                    )
                );

                $dataClassParameters->addJoin($join);
            }
        }
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @param string $dataClassName
     *
     * @return void
     */
    protected function applyDataClassPropertiesToParameters(string $dataClassName, DataClassParameters $parameters
    ): void
    {
        if ($parameters->getRetrieveProperties()->isEmpty())
        {
            if (!is_subclass_of($dataClassName, DataClassVirtualExtensionInterface::class))
            {
                $parameters->getRetrieveProperties()->add(new PropertiesConditionVariable($dataClassName));
            }

            if (is_subclass_of($dataClassName, DataClassBaseExtensionInterface::class))
            {
                $parameters->getRetrieveProperties()->add(
                    new PropertiesConditionVariable($dataClassName::getExtensionDataClassName())
                );
            }
        }
    }

    public function count(string $dataClassName, DataClassCountParameters $parameters = new DataClassCountParameters()
    ): int
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCount(
                    $dataClassName, $parameters, $this->__count($dataClassName, $parameters)
                );
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            return $this->__count($dataClassName, $parameters);
        }
    }

    /**
     * @return int[]
     */
    public function countGrouped(
        string $dataClassName, DataClassCountGroupedParameters $parameters = new DataClassCountGroupedParameters()
    ): array
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCountGrouped(
                    $dataClassName, $parameters, $this->__countGrouped($dataClassName, $parameters)
                );
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            return $this->__countGrouped($dataClassName, $parameters);
        }
    }

    public function create(DataClass $dataClass): bool
    {
        if ($dataClass instanceof UuidDataClassInterface && !$dataClass->isIdentified())
        {
            $dataClass->setId(Uuid::v4()->__toString());
        }

        $objectProperties = $dataClass->getDefaultProperties();

        if (!$dataClass instanceof UuidDataClassInterface)
        {
            unset($objectProperties[DataClass::PROPERTY_ID]);
        }

        if ($this->createRecord($dataClass::class, $objectProperties))
        {
            if (!$dataClass instanceof UuidDataClassInterface)
            {
                $dataClass->setId(
                    (string) $this->getDataClassDatabase()->getLastInsertedIdentifier($dataClass::getStorageUnitName())
                );
            }

            if ($this->isQueryCacheEnabled())
            {
                $dataClassName = $dataClass::class;

                return $this->getDataClassRepositoryCache()->addForDataClass(
                    $dataClassName, new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable($dataClassName, DataClass::PROPERTY_ID),
                        new StaticConditionVariable($dataClass->getId())
                    )
                ), $dataClass
                );
            }

            return true;
        }

        return false;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    public function createRecord(string $dataClassName, array $record): bool
    {
        return $this->getDataClassDatabase()->create($dataClassName::getStorageUnitName(), $record);
    }

    public function delete(DataClass $dataClass): bool
    {
        $dataClassName = $dataClass::class;

        $condition = new EqualityCondition(
            new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId())
        );

        return $this->deletes($dataClassName, $condition);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    public function deletes(string $dataClassName, Condition $condition): bool
    {
        if (!$this->getDataClassDatabase()->delete($dataClassName::getStorageUnitName(), $condition))
        {
            return false;
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->truncate($dataClassName);
        }
        else
        {
            return true;
        }
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    protected function determineDataClassStorageUnitName(string $dataClassName): string
    {
        if (is_subclass_of($dataClassName, DataClassVirtualExtensionInterface::class))
        {
            $typeDataClassName = $dataClassName::getExtensionDataClassName();

            return $typeDataClassName::getStorageUnitName();
        }
        else
        {
            return $dataClassName::getStorageUnitName();
        }
    }

    protected function determineDataExtensionClassName(string $dataClassName, DataClassRetrieveParameters $parameters
    ): string
    {
        $parameters = new RecordRetrieveParameters(
            new RetrieveProperties(
                [new PropertyConditionVariable($dataClassName, DataClassTypeAwareInterface::PROPERTY_TYPE)]
            ), $parameters->getCondition(), $parameters->getOrderBy(), $parameters->getJoins()
        );

        $type = $this->__record($dataClassName, $parameters);

        return $type[DataClassTypeAwareInterface::PROPERTY_TYPE];
    }

    /**
     * @return string[]
     */
    public function distinct(
        string $dataClassName, DataClassDistinctParameters $parameters = new DataClassDistinctParameters()
    ): array
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassDistinct(
                    $dataClassName, $parameters, $this->__distinct($dataClassName, $parameters)
                );
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            return $this->__distinct($dataClassName, $parameters);
        }
    }

    public function getDataClassDatabase(): DataClassDatabaseInterface
    {
        return $this->dataClassDatabase;
    }

    public function getDataClassFactory(): DataClassFactory
    {
        return $this->dataClassFactory;
    }

    public function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->dataClassRepositoryCache;
    }

    protected function isQueryCacheEnabled(): bool
    {
        return $this->queryCacheEnabled;
    }

    public function moveDisplayOrders(
        string $dataClassName, string $displayOrderProperty, ?int $start = 1, ?int $end = null,
        ?Condition $displayOrderCondition = null
    ): bool
    {
        if ($start == $end)
        {
            return false;
        }

        $displayOrderPropertyVariable = new PropertyConditionVariable($dataClassName, $displayOrderProperty);

        $conditions = [];

        if (is_null($end) || $start < $end)
        {
            $startOperator = ComparisonCondition::GREATER_THAN;
            $direction = - 1;
        }

        if (!is_null($end))
        {
            if ($start < $end)
            {
                $endOperator = ComparisonCondition::LESS_THAN_OR_EQUAL;
            }
            else
            {
                $startOperator = ComparisonCondition::LESS_THAN;
                $endOperator = ComparisonCondition::GREATER_THAN_OR_EQUAL;
                $direction = 1;
            }
        }

        $startVariable = new StaticConditionVariable($start);

        $conditions[] = new ComparisonCondition($displayOrderPropertyVariable, $startOperator, $startVariable);

        if (!is_null($end))
        {
            $endVariable = new StaticConditionVariable($end);

            $conditions[] = new ComparisonCondition($displayOrderPropertyVariable, $endOperator, $endVariable);
        }

        if ($displayOrderCondition)
        {
            $conditions[] = $displayOrderCondition;
        }

        $condition = new AndCondition($conditions);

        $updateVariable = new OperationConditionVariable(
            $displayOrderPropertyVariable, OperationConditionVariable::ADDITION, new StaticConditionVariable($direction)
        );

        $properties = new UpdateProperties();

        $properties->add(new UpdateProperty($displayOrderPropertyVariable, $updateVariable));

        return $this->updates($dataClassName, $properties, $condition);
    }

    public function record(string $dataClassName, RecordRetrieveParameters $parameters = new RecordRetrieveParameters()
    ): ?array
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForRecord(
                    $dataClassName, $this->__record($dataClassName, $parameters), $parameters
                );
            }

            $record = $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            $record = $this->__record($dataClassName, $parameters);
        }

        return $record;
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     */
    public function records(
        string $dataClassName, RecordRetrievesParameters $parameters = new RecordRetrievesParameters()
    ): ArrayCollection
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForArrayCollection(
                    $dataClassName, $this->__records($dataClassName, $parameters), $parameters
                );
            }

            $recordIterator = $dataClassRepositoryCache->get($dataClassName, $parameters);
            $recordIterator->first();

            return $recordIterator;
        }
        else
        {
            return $this->__records($dataClassName, $parameters);
        }
    }

    /**
     * @template retrieveDataClassName
     *
     * @param class-string<retrieveDataClassName> $dataClassName
     *
     * @return ?retrieveDataClassName
     */
    public function retrieve(
        string $dataClassName, DataClassRetrieveParameters $parameters = new DataClassRetrieveParameters()
    )
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClass(
                    $dataClassName, $parameters, $this->__retrieve($dataClassName, $parameters)
                );
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            return $this->__retrieve($dataClassName, $parameters);
        }
    }

    /**
     * @template retrieveById
     *
     * @param class-string<retrieveById> $dataClassName
     * @param string $identifier
     *
     * @return ?retrieveById
     */
    public function retrieveById(string $dataClassName, string $identifier)
    {
        return $this->retrieve(
            $dataClassName, new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($dataClassName, DataClass::PROPERTY_ID),
                    new StaticConditionVariable($identifier)
                )
            )
        );
    }

    public function retrieveMaximumValue(string $dataClassName, string $property, ?Condition $condition = null): int
    {
        $parameters = new RecordRetrieveParameters(
            new RetrieveProperties(
                [
                    new FunctionConditionVariable(
                        FunctionConditionVariable::MAX, new PropertyConditionVariable($dataClassName, $property),
                        self::ALIAS_MAX_SORT
                    )
                ]
            ), $condition
        );

        $record = $this->getDataClassDatabase()->retrieve($dataClassName, $parameters);

        return (int) $record[self::ALIAS_MAX_SORT];
    }

    public function retrieveNextValue(string $dataClassName, string $property, ?Condition $condition = null): int
    {
        return $this->retrieveMaximumValue($dataClassName, $property, $condition) + 1;
    }

    /**
     * @template tRetrieves
     *
     * @param class-string<tRetrieves> $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return ArrayCollection<tRetrieves>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieves(
        string $dataClassName, DataClassRetrievesParameters $parameters = new DataClassRetrievesParameters()
    ): ArrayCollection
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForArrayCollection(
                    $dataClassName, $this->__retrieves($dataClassName, $parameters), $parameters
                );
            }

            $arrayCollection = $dataClassRepositoryCache->get($dataClassName, $parameters);
            $arrayCollection->first();

            return $arrayCollection;
        }
        else
        {
            return $this->__retrieves($dataClassName, $parameters);
        }
    }

    public function transactional(callable $function): mixed
    {
        return $this->getDataClassDatabase()->transactional($function);
    }

    public function update(DataClass $dataClass): bool
    {
        $dataClassName = get_class($dataClass);

        $condition = new EqualityCondition(
            new PropertyConditionVariable($dataClassName, DataClass::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId())
        );

        $defaultProperties = $dataClass->getDefaultProperties();
        unset($defaultProperties[DataClass::PROPERTY_ID]);

        $updatePropertes = new UpdateProperties();

        foreach ($defaultProperties as $propertyName => $propertyValue)
        {
            $updatePropertes->add(
                new UpdateProperty(
                    new PropertyConditionVariable($dataClassName, $propertyName),
                    new StaticConditionVariable($propertyValue)
                )
            );
        }

        return $this->updates($dataClassName, $updatePropertes, $condition);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     */
    public function updates(string $dataClassName, UpdateProperties $properties, Condition $condition): bool
    {
        $this->getDataClassDatabase()->update($dataClassName::getStorageUnitName(), $properties, $condition);

        if ($this->isQueryCacheEnabled())
        {
            $this->getDataClassRepositoryCache()->truncate($dataClassName);
        }

        return true;
    }
}