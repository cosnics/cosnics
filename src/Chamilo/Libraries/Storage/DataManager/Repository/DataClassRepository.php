<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
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
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataClassRepository
{
    use ClassContext;

    const ALIAS_MAX_SORT = 'max_sort';

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

    protected function __countClass(string $dataClassName, DataClassCountParameters $parameters): int
    {
        return $this->getDataClassDatabase()->count($dataClassName, $parameters);
    }

    protected function __countGrouped(string $dataClassName, DataClassCountGroupedParameters $parameters): array
    {
        return $this->getDataClassDatabase()->countGrouped($dataClassName, $parameters);
    }

    protected function __distinct(string $dataClassName, DataClassDistinctParameters $parameters): array
    {
        return $this->getDataClassDatabase()->distinct($dataClassName, $parameters);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function __record(string $dataClassName, RecordRetrieveParameters $parameters): array
    {
        return $this->getDataClassDatabase()->record($dataClassName, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function __records(string $dataClassName, RecordRetrievesParameters $parameters): ArrayCollection
    {
        return new ArrayCollection($this->getDataClassDatabase()->records($dataClassName, $parameters));
    }

    /**
     *
     * @template tInternalRetrieveClass
     *
     * @param class-string<tInternalRetrieveClass> $dataClassName
     *
     * @return tInternalRetrieveClass
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function __retrieveClass(string $dataClassName, DataClassRetrieveParameters $parameters)
    {
        $record = $this->getDataClassDatabase()->retrieve($dataClassName, $parameters);

        return $this->getDataClassFactory()->getDataClass($dataClassName, $record);
    }

    /**
     * @template tInternalRetrievesClass
     *
     * @param class-string<tInternalRetrievesClass> $dataClassName
     * @param ?\Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return ArrayCollection<tInternalRetrievesClass>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    protected function __retrievesClass(string $dataClassName, DataClassRetrievesParameters $parameters
    ): ArrayCollection
    {
        $records = $this->getDataClassDatabase()->retrieves($dataClassName, $parameters);
        $dataClasses = [];

        foreach ($records as $record)
        {
            $dataClasses[] = $this->getDataClassFactory()->getDataClass($dataClassName, $record);
        }

        return new ArrayCollection($dataClasses);
    }

    /**
     * @example $displayOrderMapping[$oldDisplayOrder] = $newDisplayOrder;
     */
    public function changeDisplayOrdersByMappingArray(
        string $dataClassName, string $displayOrderProperty, ?array $displayOrderMapping = [],
        ?Condition $displayOrderCondition = null
    ): bool
    {
        foreach ($displayOrderMapping as $oldDisplayOrder => $newDisplayOrder)
        {
            if (!$this->moveDisplayOrders($dataClassName, $displayOrderProperty, $oldDisplayOrder, $newDisplayOrder))
            {
                return false;
            }

            $displayOrderPropertyVariable = new PropertyConditionVariable($dataClassName, $displayOrderProperty);

            $properties = new DataClassProperties([]);

            $properties->add(
                new DataClassProperty($displayOrderPropertyVariable, new StaticConditionVariable($newDisplayOrder))
            );

            $conditions = [];

            if ($displayOrderCondition)
            {
                $conditions[] = $displayOrderCondition;
            }

            $conditions[] = new EqualityCondition(
                $displayOrderPropertyVariable, new StaticConditionVariable($oldDisplayOrder)
            );

            $condition = new AndCondition($conditions);

            return $this->updates($dataClassName, $properties, $condition);
        }

        return true;
    }

    public function count(string $dataClassName, DataClassCountParameters $parameters): int
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class))
        {
            return $this->countCompositeDataClass($dataClassName, $parameters);
        }
        else
        {
            return $this->countClass($dataClassName, $dataClassName, $parameters);
        }
    }

    protected function countClass(
        string $cacheDataClassName, string $dataClassName, DataClassCountParameters $parameters
    ): int
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($cacheDataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCount(
                    $cacheDataClassName, $parameters, $this->__countClass($dataClassName, $parameters)
                );
            }

            return $dataClassRepositoryCache->get($cacheDataClassName, $parameters);
        }
        else
        {
            return $this->__countClass($dataClassName, $parameters);
        }
    }

    protected function countCompositeDataClass(string $dataClassName, DataClassCountParameters $parameters): int
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);
        $this->setCompositeDataClassParameters($parentDataClassName, $dataClassName, $parameters);

        return $this->countClass($parentDataClassName, $dataClassName, $parameters);
    }

    /**
     * @return int[]
     * @throws \Exception
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

    /**
     * @throws \ReflectionException
     */
    public function create(DataClass $dataClass): bool
    {
        if ($dataClass instanceof CompositeDataClass)
        {
            $parentClass = $dataClass->parentClassName();
            $objectTableName = $parentClass::getTableName();
        }
        else
        {
            $objectTableName = $dataClass->getTableName();
        }

        $objectProperties = $dataClass->getDefaultProperties();
        $objectProperties[DataClass::PROPERTY_ID] = null;
        unset($objectProperties[DataClass::PROPERTY_ID]);

        $dataClassCreated = $this->getDataClassDatabase()->create($objectTableName, $objectProperties);

        $dataClass->setId($this->getDataClassDatabase()->getLastInsertedIdentifier($objectTableName));

        if ($dataClassCreated === true && $dataClass instanceof CompositeDataClass && $dataClass->isExtended())
        {
            $objectProperties = $dataClass->getAdditionalProperties();
            $objectProperties[DataClass::PROPERTY_ID] = $dataClass->getId();

            $dataClassCreated = $this->getDataClassDatabase()->create($dataClass->getTableName(), $objectProperties);
        }

        if ($dataClassCreated === true && $this->isQueryCacheEnabled())
        {
            $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClass::class_name());

            return $this->getDataClassRepositoryCache()->addForDataClass(
                $dataClass, new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable($parentDataClassName, DataClass::PROPERTY_ID),
                        new StaticConditionVariable($dataClass->getId())
                    )
                )
            );
        }
        else
        {
            return true;
        }
    }

    public function createRecord(string $dataClassName, array $record): bool
    {
        return $this->getDataClassDatabase()->create($dataClassName::getTableName(), $record);
    }

    /**
     * @throws \ReflectionException
     */
    public function delete(DataClass $dataClass): bool
    {
        $dataClassName =
            ($dataClass instanceof CompositeDataClass ? $dataClass::parentClassName() : $dataClass::class_name());

        $condition = new EqualityCondition(
            new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId())
        );

        if (!$this->getDataClassDatabase()->delete($dataClassName, $condition))
        {
            return false;
        }

        if ($dataClass instanceof CompositeDataClass && $dataClass::isExtended())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($dataClass::class_name(), DataClass::PROPERTY_ID),
                new StaticConditionVariable($dataClass->getId())
            );

            if (!$this->getDataClassDatabase()->delete($dataClass::class_name(), $condition))
            {
                return false;
            }
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

    protected function determineCompositeDataClassParentClassName(string $dataClassName): string
    {
        if ($this->isExtensionClass($dataClassName))
        {
            return $dataClassName::parentClassName();
        }
        else
        {
            return $dataClassName;
        }
    }

    protected function determineCompositeDataClassType(string $dataClassName, DataClassRetrieveParameters $parameters
    ): string
    {
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable($dataClassName, CompositeDataClass::PROPERTY_TYPE))
            ), $parameters->getCondition(), $parameters->getOrderBy(), $parameters->getJoins()
        );

        $type = $this->record($dataClassName, $parameters);

        return $type[CompositeDataClass::PROPERTY_TYPE] ?? false;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function determineDataClassType(string $dataClassName, int $identifier): string
    {
        $conditionDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);

        $condition = new EqualityCondition(
            new PropertyConditionVariable($conditionDataClassName, DataClass::PROPERTY_ID),
            new StaticConditionVariable($identifier)
        );

        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable($conditionDataClassName, CompositeDataClass::PROPERTY_TYPE))
            ), $condition
        );

        $type = $this->record($conditionDataClassName, $parameters);

        if (isset($type[$conditionDataClassName::PROPERTY_TYPE]))
        {
            return $type[$conditionDataClassName::PROPERTY_TYPE];
        }
        else
        {
            throw new ObjectNotExistException($identifier);
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

    public function getAlias(string $dataClassStorageUnitName): string
    {
        return $this->getDataClassDatabase()->getAlias($dataClassStorageUnitName);
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

    protected function isCompositeDataClass(string $dataClassName): bool
    {
        return is_subclass_of($dataClassName, CompositeDataClass::class);
    }

    protected function isExtensionClass(string $dataClassName): bool
    {
        return $this->isCompositeDataClass($dataClassName) &&
            get_parent_class($dataClassName) !== CompositeDataClass::class;
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

        $properties = new DataClassProperties([]);

        $properties->add(new DataClassProperty($displayOrderPropertyVariable, $updateVariable));

        return $this->updates($dataClassName, $properties, $condition);
    }

    /**
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context());
    }

    public function record(string $dataClassName, RecordRetrieveParameters $parameters): array
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                try
                {
                    $dataClassRepositoryCache->addForRecord(
                        $dataClassName, $this->__record($dataClassName, $parameters), $parameters
                    );
                }
                catch (DataClassNoResultException $exception)
                {
                    $dataClassRepositoryCache->addForNoResult($exception);
                }
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            try
            {
                return $this->__record($dataClassName, $parameters);
            }
            catch (DataClassNoResultException $exception)
            {
                return false;
            }
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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
     * @return retrieveDataClassName
     * @throws \ReflectionException
     */
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters)
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class))
        {
            return $this->retrieveCompositeDataClass($dataClassName, $parameters);
        }
        else
        {
            return $this->retrieveClass($dataClassName, $dataClassName, $parameters);
        }
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by it's unique identifier
     *
     * @template retrieveById
     *
     * @param class-string<retrieveById> $dataClassName
     * @param integer $identifier
     *
     * @return retrieveById
     * @throws \ReflectionException
     */
    public function retrieveById(string $dataClassName, int $identifier)
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);

        return $this->retrieve(
            $dataClassName, new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($parentDataClassName, DataClass::PROPERTY_ID),
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
     * @return retrieveClass|bool
     * @throws \ReflectionException
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
                try
                {
                    $dataClassRepositoryCache->addForDataClass(
                        $this->__retrieveClass($dataClassName, $parameters), $parameters
                    );
                }
                catch (DataClassNoResultException $exception)
                {
                    $dataClassRepositoryCache->addForNoResult($exception);
                }
            }

            return $dataClassRepositoryCache->get($cacheDataClassName, $parameters);
        }
        else
        {
            try
            {
                return $this->__retrieveClass($dataClassName, $parameters);
            }
            catch (DataClassNoResultException $exception)
            {
                return false;
            }
        }
    }

    /**
     * @template retrieveCompositeDataClass
     *
     * @param class-string<retrieveCompositeDataClass> $dataClassName
     *
     * @return retrieveCompositeDataClass
     * @throws \ReflectionException
     */
    protected function retrieveCompositeDataClass(string $dataClassName, DataClassRetrieveParameters $parameters)
    {
        $parentClassName = $this->determineCompositeDataClassParentClassName($dataClassName);

        if ($this->isCompositeDataClass($dataClassName) && !$this->isExtensionClass($dataClassName))
        {
            $dataClassName = $this->determineCompositeDataClassType($dataClassName, $parameters);
        }

        $this->setCompositeDataClassParameters($parentClassName, $dataClassName, $parameters);

        return $this->retrieveClass($parentClassName, $dataClassName, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $compositeDataClass): array
    {
        if (!$compositeDataClass->isExtended())
        {
            return [];
        }

        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(array(new PropertiesConditionVariable($compositeDataClass::class_name()))),
            new EqualityCondition(
                new PropertyConditionVariable($compositeDataClass::class_name(), $compositeDataClass::PROPERTY_ID),
                new StaticConditionVariable($compositeDataClass->getId())
            )
        );

        return $this->getDataClassDatabase()->record($compositeDataClass::class_name(), $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveMaximumValue(string $dataClassName, string $property, ?Condition $condition = null): int
    {
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(
                    new FunctionConditionVariable(
                        FunctionConditionVariable::MAX, new PropertyConditionVariable($dataClassName, $property),
                        self::ALIAS_MAX_SORT
                    )
                )
            ), $condition
        );

        $record = $this->getDataClassDatabase()->record($dataClassName, $parameters);

        return (int) $record[self::ALIAS_MAX_SORT];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
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
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): ArrayCollection
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class))
        {
            return $this->retrievesCompositeDataClass($dataClassName, $parameters);
        }
        else
        {
            return $this->retrievesClass($dataClassName, $dataClassName, $parameters);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function retrievesClass(
        string $cacheDataClassName, string $dataClassName, DataClassRetrievesParameters $parameters
    ): ArrayCollection
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($cacheDataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForArrayCollection(
                    $dataClassName, $this->__retrievesClass($dataClassName, $parameters), $parameters
                );
            }

            $arrayCollection = $dataClassRepositoryCache->get($cacheDataClassName, $parameters);
            $arrayCollection->first();

            return $arrayCollection;
        }
        else
        {
            return $this->__retrievesClass($dataClassName, $parameters);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function retrievesCompositeDataClass(string $dataClassName, DataClassRetrievesParameters $parameters
    ): ArrayCollection
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);
        $this->setCompositeDataClassParameters($parentDataClassName, $dataClassName, $parameters);

        return $this->retrievesClass($parentDataClassName, $dataClassName, $parameters);
    }

    protected function setCompositeDataClassParameters(
        string $parentDataClassName, string $dataClassName, DataClassParameters $parameters
    ): DataClassRepository
    {
        if ($dataClassName::isExtended())
        {
            $join = new Join(
                $parentDataClassName, new EqualityCondition(
                    new PropertyConditionVariable($parentDataClassName, $parentDataClassName::PROPERTY_ID),
                    new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID)
                )
            );

            if ($parameters->getJoins() instanceof Joins)
            {
                $joins = $parameters->getJoins();
                $joins->add($join);
            }
            else
            {
                $joins = new Joins(array($join));
            }

            $parameters->setJoins($joins);
        }

        if ($this->isExtensionClass($dataClassName))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($parentDataClassName, $parentDataClassName::PROPERTY_TYPE),
                new StaticConditionVariable($dataClassName)
            );

            if ($parameters->getCondition() instanceof Condition)
            {
                $parameters->setCondition(new AndCondition([$parameters->getCondition(), $condition]));
            }
            else
            {
                $parameters->setCondition($condition);
            }
        }

        return $this;
    }

    /**
     *
     * @param callable $function
     *
     * @return mixed
     * @throws \Throwable
     */
    public function transactional(callable $function)
    {
        return $this->getDataClassDatabase()->transactional($function);
    }

    public function translateCondition(Condition $condition): string
    {
        return $this->getDataClassDatabase()->translateCondition($condition);
    }

    /**
     * @throws \ReflectionException
     */
    public function update(DataClass $dataClass): bool
    {
        if ($dataClass instanceof CompositeDataClass)
        {
            $propertyConditionClass = $dataClass::parentClassName();
            $dataClassTableName = $propertyConditionClass::getTableName();
        }
        else
        {
            $propertyConditionClass = $dataClass::class_name();
            $dataClassTableName = $dataClass->getTableName();
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable($propertyConditionClass, DataClass::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId())
        );

        $defaultProperties = $dataClass->getDefaultProperties();
        unset($defaultProperties[DataClass::PROPERTY_ID]);

        $result = $this->getDataClassDatabase()->update(
            $dataClassTableName, $condition, $defaultProperties
        );

        if ($result === true && $dataClass instanceof CompositeDataClass && $dataClass::isExtended())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(get_class($dataClass), DataClass::PROPERTY_ID),
                new StaticConditionVariable($dataClass->getId())
            );

            $result = $this->getDataClassDatabase()->update(
                $dataClass->getTableName(), $condition, $dataClass->getAdditionalProperties()
            );
        }

        return $result;
    }

    public function updates(string $dataClassName, DataClassProperties $properties, Condition $condition): bool
    {
        if (!$this->getDataClassDatabase()->updates($dataClassName, $properties, $condition))
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
}