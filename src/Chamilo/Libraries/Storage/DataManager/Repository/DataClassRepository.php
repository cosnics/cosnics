<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataClass\Interfaces\UuidDataClassInterface;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\ParametersHandler;
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

    private ParametersHandler $parametersHandler;

    private bool $queryCacheEnabled;

    public function __construct(
        DataClassRepositoryCache $dataClassRepositoryCache, DataClassDatabaseInterface $dataClassDatabase,
        DataClassFactory $dataClassFactory, ParametersHandler $parametersHandler, bool $queryCacheEnabled = true
    )
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->dataClassFactory = $dataClassFactory;
        $this->parametersHandler = $parametersHandler;
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    protected function __countClass(string $dataClassName, DataClassCountParameters $parameters): int
    {
        $this->getParametersHandler()->handleDataClassCountParameters($parameters);

        return $this->getDataClassDatabase()->count($dataClassName, $parameters);
    }

    protected function __countGrouped(string $dataClassName, DataClassCountGroupedParameters $parameters): array
    {
        $this->getParametersHandler()->handleDataClassCountGroupedParameters($parameters);

        return $this->getDataClassDatabase()->countGrouped($dataClassName, $parameters);
    }

    protected function __distinct(string $dataClassName, DataClassDistinctParameters $parameters): array
    {
        $this->getParametersHandler()->handleDataClassDistinctParameters($parameters);

        return $this->getDataClassDatabase()->distinct($dataClassName, $parameters);
    }

    /**
     * @return ?string[]
     */
    protected function __record(string $dataClassName, RecordRetrieveParameters $parameters): ?array
    {
        if (!$parameters->getRetrieveProperties() instanceof RetrieveProperties)
        {
            $this->getParametersHandler()->handleDataClassRetrieveParameters($dataClassName, $parameters);
        }

        return $this->getDataClassDatabase()->retrieve($dataClassName, $parameters);
    }

    protected function __records(string $dataClassName, RecordRetrievesParameters $parameters): ArrayCollection
    {
        $this->getParametersHandler()->handleDataClassRetrievesParameters($dataClassName, $parameters);

        return new ArrayCollection($this->getDataClassDatabase()->retrieves($dataClassName, $parameters));
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
        $this->getParametersHandler()->handleDataClassRetrieveParameters($dataClassName, $parameters);

        $record = $this->getDataClassDatabase()->retrieve($dataClassName, $parameters);

        return $this->getDataClassFactory()->getDataClass($dataClassName, $record);
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
        $this->getParametersHandler()->handleDataClassRetrievesParameters($dataClassName, $parameters);

        $records = $this->getDataClassDatabase()->retrieves($dataClassName, $parameters);
        $dataClasses = [];

        foreach ($records as $record)
        {
            $dataClasses[] = $this->getDataClassFactory()->getDataClass($dataClassName, $record);
        }

        return new ArrayCollection($dataClasses);
    }

    public function count(string $dataClassName, DataClassCountParameters $parameters): int
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCount(
                    $dataClassName, $parameters, $this->__countClass($dataClassName, $parameters)
                );
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            return $this->__countClass($dataClassName, $parameters);
        }
    }

    /**
     * @return int[]
     */
    public function countGrouped(string $dataClassName, DataClassCountGroupedParameters $parameters): array
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

        if (!$this->getDataClassDatabase()->delete($dataClassName, $condition))
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

    public function deletes(string $dataClassName, Condition $condition): bool
    {
        if (!$this->getDataClassDatabase()->delete($dataClassName, $condition))
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
     * @return string[]
     */
    public function distinct(string $dataClassName, DataClassDistinctParameters $parameters): array
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

    public function getParametersHandler(): ParametersHandler
    {
        return $this->parametersHandler;
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

    public function record(string $dataClassName, RecordRetrieveParameters $parameters): ?array
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
    public function records(string $dataClassName, RecordRetrievesParameters $parameters): ArrayCollection
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
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters)
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

    /**
     * @template retrieveClass
     *
     * @param class-string<retrieveClass> $dataClassName
     *
     * @return ?retrieveClass
     */
    protected function retrieveClass(
        string $cacheDataClassName, string $dataClassName, DataClassRetrieveParameters $parameters
    )
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($cacheDataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClass(
                    $cacheDataClassName, $parameters, $this->__retrieve($dataClassName, $parameters)
                );
            }

            return $dataClassRepositoryCache->get($cacheDataClassName, $parameters);
        }
        else
        {
            return $this->__retrieve($dataClassName, $parameters);
        }
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
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): ArrayCollection
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

        $propertyConditionClass = get_class($dataClass);
        $dataClassTableName = $dataClass::getStorageUnitName();

        $condition = new EqualityCondition(
            new PropertyConditionVariable($propertyConditionClass, DataClass::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId())
        );

        $defaultProperties = $dataClass->getDefaultProperties();
        unset($defaultProperties[DataClass::PROPERTY_ID]);

        $updatePropertes = new UpdateProperties();

        foreach ($defaultProperties as $propertyName => $propertyValue)
        {
            $updatePropertes->add(
                new UpdateProperty(
                    new PropertyConditionVariable($propertyConditionClass, $propertyName),
                    new StaticConditionVariable($propertyValue)
                )
            );
        }

        $this->getDataClassDatabase()->update($dataClassTableName, $updatePropertes, $condition);

        return true;
    }

    /**
     * @template updatesDataClassName
     *
     * @param class-string<updatesDataClassName> $dataClassName
     */
    public function updates(string $dataClassName, UpdateProperties $properties, Condition $condition): bool
    {
        $this->getDataClassDatabase()->update($dataClassName, $properties, $condition);

        if ($this->isQueryCacheEnabled())
        {
            $this->getDataClassRepositoryCache()->truncate($dataClassName);
        }

        return true;
    }
}