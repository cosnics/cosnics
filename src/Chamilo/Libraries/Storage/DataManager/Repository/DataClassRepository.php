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
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\DataClassParameters;
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
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    protected function __count(string $dataClassName, DataClassParameters $parameters): int
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
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    protected function __countGrouped(string $dataClassName, DataClassParameters $parameters): array
    {
        $retrieveProperties = $parameters->getRetrieveProperties();
        $retrieveProperties->add(
            new FunctionConditionVariable(FunctionConditionVariable::COUNT, new StaticConditionVariable(1))
        );

        return $this->getDataClassDatabase()->countGrouped($dataClassName::getStorageUnitName(), $parameters);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    protected function __distinct(string $dataClassName, DataClassParameters $parameters): array
    {
        $parameters->setRetrieveProperties(
            new RetrieveProperties([new DistinctConditionVariable($parameters->getRetrieveProperties()->toArray())])
        );

        return $this->getDataClassDatabase()->distinct($dataClassName::getStorageUnitName(), $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    protected function __expandRecordsWithAdditionalProperties(string $dataClassName, array $records): array
    {
        $isDataTypeAware = is_subclass_of($dataClassName, DataClassTypeAwareInterface::class);
        $isNotABaseExtension = !is_subclass_of($dataClassName, DataClassBaseExtensionInterface::class);

        if (!$isDataTypeAware || !$isNotABaseExtension)
        {
            return $records;
        }

        $additionalQueries = [];

        foreach ($records as $record)
        {
            $dataClassExtensionType = $record[DataClassTypeAwareInterface::PROPERTY_TYPE];
            $identifier = $record[DataClass::PROPERTY_ID];

            $hasType = isset($dataClassExtensionType);
            $isExtension = is_subclass_of($dataClassExtensionType, DataClassExtensionInterface::class);
            $hasIdentifier = isset($identifier);

            if ($hasType && $isExtension && $hasIdentifier)
            {
                $additionalQueries[$dataClassExtensionType][] = $identifier;
            }
        }

        $additionalRecords = [];

        /**
         * @var ?class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassExtensionType
         */
        foreach ($additionalQueries as $dataClassExtensionType => $identifiers)
        {
            $additionalRecordsForIdentifiers = $this->getDataClassDatabase()->retrieves(
                $dataClassExtensionType::getStorageUnitName(), new DataClassParameters(
                    condition: new InCondition(
                        new PropertyConditionVariable($dataClassExtensionType, DataClass::PROPERTY_ID), $identifiers
                    ), retrieveProperties: new RetrieveProperties(
                    [new PropertiesConditionVariable($dataClassExtensionType)]
                )
                )
            );

            foreach ($additionalRecordsForIdentifiers as $additionalRecordsForIdentifier)
            {
                $additionalRecords[$additionalRecordsForIdentifier[DataClass::PROPERTY_ID]] =
                    $additionalRecordsForIdentifier;
            }
        }

        if (count($additionalRecords) > 0)
        {
            foreach ($records as $recordKey => $record)
            {
                if (array_key_exists($record[DataClass::PROPERTY_ID], $additionalRecords))
                {
                    $records[$recordKey] = array_merge($record, $additionalRecords[$record[DataClass::PROPERTY_ID]]);
                }
            }
        }

        return $records;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     *
     * @return ?string[]
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     */
    protected function __record(string $dataClassName, DataClassParameters $parameters): ?array
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
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    protected function __records(string $dataClassName, DataClassParameters $parameters): ArrayCollection
    {
        $expandRecords = $parameters->getRetrieveProperties()->isEmpty();

        $this->applyDataClassExtensionToParameters($dataClassName, $parameters);
        $this->applyDataClassPropertiesToParameters($dataClassName, $parameters);

        $records = $this->getDataClassDatabase()->retrieves($dataClassName::getStorageUnitName(), $parameters);

        if ($expandRecords)
        {
            $records = $this->__expandRecordsWithAdditionalProperties($dataClassName, $records);
        }

        return new ArrayCollection($records);
    }

    /**
     * @template tInternalRetrieveClass
     *
     * @param class-string<tInternalRetrieveClass> $dataClassName
     *
     * @return ?tInternalRetrieveClass
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     */
    protected function __retrieve(string $dataClassName, DataClassParameters $parameters)
    {
        return $this->getDataClassFactory()->getDataClass(
            $dataClassName, $this->__record($dataClassName, $parameters)
        );
    }

    /**
     * @template tInternalRetrievesClass
     *
     * @param class-string<tInternalRetrievesClass> $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\DataClassParameters $parameters
     *
     * @return ArrayCollection<tInternalRetrievesClass>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    protected function __retrieves(string $dataClassName, DataClassParameters $parameters): ArrayCollection
    {
        $records = $this->__records($dataClassName, $parameters);
        $dataClasses = [];

        foreach ($records as $record)
        {
            if (is_subclass_of($dataClassName, DataClassTypeAwareInterface::class) &&
                !is_subclass_of($dataClassName, DataClassBaseExtensionInterface::class) &&
                isset($record[DataClassTypeAwareInterface::PROPERTY_TYPE]))
            {
                /**
                 * @var class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $factoryDataClassName
                 */
                $factoryDataClassName = $record[DataClassTypeAwareInterface::PROPERTY_TYPE];
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
     * @param \Chamilo\Libraries\Storage\Query\DataClassParameters $parameters
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

    protected function buildRetrieveByIdentifierParameters(string $dataClassName, string $identifier
    ): DataClassParameters
    {
        return new DataClassParameters(
            condition: new EqualityCondition(
                new PropertyConditionVariable($dataClassName, DataClass::PROPERTY_ID),
                new StaticConditionVariable($identifier)
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function count(string $dataClassName, DataClassParameters $parameters = new DataClassParameters()): int
    {
        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->addForCount(
                $dataClassName, $parameters, $this->__count($dataClassName, $parameters)
            );
        }
        else
        {
            return $this->__count($dataClassName, $parameters);
        }
    }

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function countGrouped(
        string $dataClassName, DataClassParameters $parameters = new DataClassParameters()
    ): array
    {
        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->addForCountGrouped(
                $dataClassName, $parameters, $this->__countGrouped($dataClassName, $parameters)
            );
        }
        else
        {
            return $this->__countGrouped($dataClassName, $parameters);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
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

        $dataClassName = $dataClass::class;

        if ($this->createRecord($dataClassName, $objectProperties))
        {
            if (!$dataClass instanceof UuidDataClassInterface)
            {
                $dataClass->setId(
                    (string) $this->getDataClassDatabase()->getLastInsertedIdentifier($dataClass::getStorageUnitName())
                );
            }

            if ($this->isQueryCacheEnabled())
            {
                $this->getDataClassRepositoryCache()->addForRetrieve(
                    $dataClassName, $this->buildRetrieveByIdentifierParameters($dataClassName, $dataClass->getId()),
                    $dataClass
                );

                return true;
            }

            return true;
        }

        return false;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function createRecord(string $dataClassName, array $record): bool
    {
        return $this->getDataClassDatabase()->create($dataClassName::getStorageUnitName(), $record);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
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
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function deletes(string $dataClassName, Condition $condition): bool
    {
        if (!$this->getDataClassDatabase()->delete($dataClassName::getStorageUnitName(), $condition))
        {
            return false;
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->truncateClass($dataClassName);
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

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    protected function determineDataExtensionClassName(string $dataClassName, DataClassParameters $parameters): string
    {
        $parameters = new DataClassParameters(
            condition: $parameters->getCondition(), joins: $parameters->getJoins(),
            retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable($dataClassName, DataClassTypeAwareInterface::PROPERTY_TYPE)]
            ), orderBy: $parameters->getOrderBy()
        );

        $type = $this->__record($dataClassName, $parameters);

        return $type[DataClassTypeAwareInterface::PROPERTY_TYPE];
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function distinct(string $dataClassName, DataClassParameters $parameters = new DataClassParameters()): array
    {
        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->addForDistinct(
                $dataClassName, $parameters, $this->__distinct($dataClassName, $parameters)
            );
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

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
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

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function record(string $dataClassName, DataClassParameters $parameters = new DataClassParameters()): ?array
    {
        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->addForRecord(
                $dataClassName, $parameters, $this->__record($dataClassName, $parameters)
            );
        }
        else
        {
            return $this->__record($dataClassName, $parameters);
        }
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\DataClassParameters $parameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function records(
        string $dataClassName, DataClassParameters $parameters = new DataClassParameters()
    ): ArrayCollection
    {
        if ($this->isQueryCacheEnabled())
        {
            $recordIterator = $this->getDataClassRepositoryCache()->addForRecords(
                $dataClassName, $parameters, $this->__records($dataClassName, $parameters)
            );
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
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     */
    public function retrieve(
        string $dataClassName, DataClassParameters $parameters = new DataClassParameters()
    )
    {
        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->addForRetrieve(
                $dataClassName, $parameters, $this->__retrieve($dataClassName, $parameters)
            );
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
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     */
    public function retrieveById(string $dataClassName, string $identifier)
    {
        return $this->retrieve(
            $dataClassName, $this->buildRetrieveByIdentifierParameters($dataClassName, $identifier)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function retrieveMaximumValue(string $dataClassName, string $property, ?Condition $condition = null): int
    {
        $parameters = new DataClassParameters(
            condition: $condition, retrieveProperties: new RetrieveProperties(
            [
                new FunctionConditionVariable(
                    FunctionConditionVariable::MAX, new PropertyConditionVariable($dataClassName, $property),
                    self::ALIAS_MAX_SORT
                )
            ]
        )
        );

        $record = $this->getDataClassDatabase()->retrieve($dataClassName, $parameters);

        return (int) $record[self::ALIAS_MAX_SORT];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function retrieveNextValue(string $dataClassName, string $property, ?Condition $condition = null): int
    {
        return $this->retrieveMaximumValue($dataClassName, $property, $condition) + 1;
    }

    /**
     * @template tRetrieves
     *
     * @param class-string<tRetrieves> $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\DataClassParameters $parameters
     *
     * @return ArrayCollection<tRetrieves>
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function retrieves(
        string $dataClassName, DataClassParameters $parameters = new DataClassParameters()
    ): ArrayCollection
    {
        if ($this->isQueryCacheEnabled())
        {
            $arrayCollection = $this->getDataClassRepositoryCache()->addForRetrieves(
                $dataClassName, $parameters, $this->__retrieves($dataClassName, $parameters)
            );
            $arrayCollection->first();

            return $arrayCollection;
        }
        else
        {
            return $this->__retrieves($dataClassName, $parameters);
        }
    }

    /**
     * @throws \Throwable
     */
    public function transactional(callable $function): mixed
    {
        return $this->getDataClassDatabase()->transactional($function);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
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
     *
     * @throws \Chamilo\Libraries\Storage\Exception\StorageMethodException
     */
    public function updates(string $dataClassName, UpdateProperties $properties, Condition $condition): bool
    {
        $this->getDataClassDatabase()->update($dataClassName::getStorageUnitName(), $properties, $condition);

        if ($this->isQueryCacheEnabled())
        {
            $this->getDataClassRepositoryCache()->truncateClass($dataClassName);
        }

        return true;
    }
}