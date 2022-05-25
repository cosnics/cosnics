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
use Chamilo\Libraries\Storage\Iterator\DataClassCollection;
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

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface
     */
    private $dataClassDatabase;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataClass\DataClassFactory
     */
    private $dataClassFactory;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    private $dataClassRepositoryCache;

    /**
     *
     * @var boolean
     */
    private $queryCacheEnabled;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\DataClass\DataClassFactory $dataClassFactory
     * @param boolean $queryCacheEnabled
     */
    public function __construct(
        DataClassRepositoryCache $dataClassRepositoryCache, DataClassDatabaseInterface $dataClassDatabase,
        DataClassFactory $dataClassFactory, $queryCacheEnabled = true
    )
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->dataClassFactory = $dataClassFactory;
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     */
    protected function __countClass($dataClassName, DataClassCountParameters $parameters)
    {
        return $this->getDataClassDatabase()->count($dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return integer[]
     */
    protected function __countGrouped($dataClassName, DataClassCountGroupedParameters $parameters)
    {
        return $this->getDataClassDatabase()->countGrouped($dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]|string[][]
     */
    protected function __distinct($dataClassName, DataClassDistinctParameters $parameters)
    {
        return $this->getDataClassDatabase()->distinct($dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     *
     * @return string[]
     */
    protected function __record($dataClassName, $parameters)
    {
        return $this->getDataClassDatabase()->record($dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    protected function __records($dataClassName, RecordRetrievesParameters $parameters)
    {
        return new DataClassCollection($this->getDataClassDatabase()->records($dataClassName, $parameters));
    }

    /**
     *
     * @template tInternalRetrieveClass
     *
     * @param class-string<tInternalRetrieveClass> $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return tInternalRetrieveClass
     */
    protected function __retrieveClass(string $dataClassName, DataClassRetrieveParameters $parameters)
    {
        $record = $this->getDataClassDatabase()->retrieve($dataClassName, $parameters);

        return $this->getDataClassFactory()->getDataClass($dataClassName, $record);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    protected function __retrievesClass($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $records = $this->getDataClassDatabase()->retrieves($dataClassName, $parameters);
        $dataClasses = [];

        foreach ($records as $record)
        {
            $dataClasses[] = $this->getDataClassFactory()->getDataClass($dataClassName, $record);
        }

        return new DataClassCollection($dataClasses);
    }

    /**
     * Changes the display orders by a given mapping array
     *
     * @param string $dataClassName
     * @param string $displayOrderProperty
     * @param integer[] $displayOrderMapping
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $displayOrderCondition
     *
     * @return boolean
     *
     * @throws \Exception
     * @example $displayOrderMapping[$oldDisplayOrder] = $newDisplayOrder;
     */
    public function changeDisplayOrdersByMappingArray(
        $dataClassName, $displayOrderProperty, $displayOrderMapping = [], $displayOrderCondition = null
    )
    {
        foreach ($displayOrderMapping as $oldDisplayOrder => $newDisplayOrder)
        {
            if (!$this->moveDisplayOrders($dataClassName, $oldDisplayOrder, $newDisplayOrder))
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

    /**
     * Count the number of instances of a DataClass object in the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function count($dataClassName, DataClassCountParameters $parameters = null)
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

    /**
     *
     * @param string $cacheDataClassName
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \Exception
     */
    protected function countClass($cacheDataClassName, $dataClassName, DataClassCountParameters $parameters)
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

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \Exception
     */
    protected function countCompositeDataClass($dataClassName, DataClassCountParameters $parameters)
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);
        $this->setCompositeDataClassParameters($parentDataClassName, $dataClassName, $parameters);

        return $this->countClass($parentDataClassName, $dataClassName, $parameters);
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer, based on a specific property ad grouped
     * by another property in the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return integer[]
     * @throws \Exception
     */
    public function countGrouped($dataClassName, DataClassCountGroupedParameters $parameters)
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
     * Write an instance of a DataClass object to the storage layer
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function create(DataClass $dataClass)
    {
        if (!$this->getDataClassDatabase()->create($dataClass))
        {
            return false;
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->addForDataClass($dataClass);
        }
        else
        {
            return true;
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param string[] $record
     *
     * @return boolean
     */
    public function createRecord($dataClassName, $record)
    {
        return $this->getDataClassDatabase()->createRecord($dataClassName, $record);
    }

    /**
     * Delete an instance of a DataClass object from the storage layer
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function delete(DataClass $dataClass)
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

    /**
     * Deletes any given number of instance of the DataClass object from the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     */
    public function deletes($dataClassName, Condition $condition)
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
     *
     * @param string $dataClassName
     *
     * @return string
     */
    protected function determineCompositeDataClassParentClassName($dataClassName)
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

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return string
     * @throws \Exception
     */
    protected function determineCompositeDataClassType($dataClassName, DataClassRetrieveParameters $parameters)
    {
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable($dataClassName, CompositeDataClass::PROPERTY_TYPE))
            ), $parameters->getCondition(), $parameters->getOrderBy(), $parameters->getJoins()
        );

        $type = $this->record($dataClassName, $parameters);

        if (isset($type[CompositeDataClass::PROPERTY_TYPE]))
        {
            return $type[CompositeDataClass::PROPERTY_TYPE];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param integer $identifier
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Exception
     */
    public function determineDataClassType($dataClassName, $identifier)
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
     * Retrieve all distinct values of a specific DataClass' property from the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]|string[][]
     * @throws \Exception
     */
    public function distinct($dataClassName, DataClassDistinctParameters $parameters)
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

    /**
     * Get the alias of a storage unit in the storage layer
     *
     * @param string $dataClassStorageUnitName
     *
     * @return string
     */
    public function getAlias($dataClassStorageUnitName)
    {
        return $this->getDataClassDatabase()->getAlias($dataClassStorageUnitName);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface
     */
    public function getDataClassDatabase()
    {
        return $this->dataClassDatabase;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     */
    public function setDataClassDatabase(DataClassDatabaseInterface $dataClassDatabase)
    {
        $this->dataClassDatabase = $dataClassDatabase;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClassFactory
     */
    public function getDataClassFactory()
    {
        return $this->dataClassFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClassFactory $dataClassFactory
     */
    public function setDataClassFactory(DataClassFactory $dataClassFactory)
    {
        $this->dataClassFactory = $dataClassFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    public function getDataClassRepositoryCache()
    {
        return $this->dataClassRepositoryCache;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     */
    public function setDataClassRepositoryCache(DataClassRepositoryCache $dataClassRepositoryCache)
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
    }

    /**
     *
     * @return boolean
     */
    public function getQueryCacheEnabled()
    {
        return $this->queryCacheEnabled;
    }

    /**
     *
     * @return boolean
     */
    protected function isQueryCacheEnabled()
    {
        return (bool) $this->getQueryCacheEnabled();
    }

    /**
     *
     * @param boolean $queryCacheEnabled
     */
    public function setQueryCacheEnabled($queryCacheEnabled)
    {
        $this->queryCacheEnabled = $queryCacheEnabled;
    }

    /**
     *
     * @param string $dataClassName
     *
     * @return boolean
     */
    protected function isCompositeDataClass($dataClassName)
    {
        return is_subclass_of($dataClassName, CompositeDataClass::class);
    }

    /**
     *
     * @param string $dataClassName
     *
     * @return boolean
     */
    protected function isExtensionClass($dataClassName)
    {
        return $this->isCompositeDataClass($dataClassName) &&
            get_parent_class($dataClassName) !== CompositeDataClass::class;
    }

    /**
     * Generic function to move display orders Usage Start & End value: Subset of display orders Start value only: all
     * display orders from given start until the end
     *
     * @param string $dataClassName
     * @param string $displayOrderProperty
     * @param integer $start
     * @param integer $end
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $displayOrderCondition
     *
     * @return boolean
     * @throws \Exception
     */
    public function moveDisplayOrders(
        $dataClassName, $displayOrderProperty, $start = 1, $end = null, $displayOrderCondition = null
    )
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
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context());
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     *
     * @return string[]
     * @throws \Exception
     */
    public function record($dataClassName, RecordRetrieveParameters $parameters = null)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                try
                {
                    $record = $dataClassRepositoryCache->addForRecord(
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
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     * @throws \Exception
     */
    public function records($dataClassName, RecordRetrievesParameters $parameters = null)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCollection(
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
     * @param ?\Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return retrieveDataClassName
     * @throws \Exception
     */
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters = null)
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
     * @throws \Exception
     */
    public function retrieveById($dataClassName, $identifier)
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
     * @param string $cacheDataClassName
     * @param class-string<retrieveClass> $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return retrieveClass|bool
     * @throws \Exception
     */
    protected function retrieveClass($cacheDataClassName, $dataClassName, DataClassRetrieveParameters $parameters)
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
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return retrieveCompositeDataClass
     * @throws \Exception
     */
    protected function retrieveCompositeDataClass($dataClassName, DataClassRetrieveParameters $parameters)
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
     * Retrieve the additional properties for a specific CompositeDataClass instance
     *
     * @param \Chamilo\Libraries\Storage\DataClass\CompositeDataClass $compositeDataClass
     *
     * @return string[]
     * @throws \Exception
     */
    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $compositeDataClass)
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

        return $this->getDataClassDatabase()->record($compositeDataClass, $parameters);
    }

    /**
     * Get the highest value of a specific property of a DataClass in the storage layer
     *
     * @param string $dataClassName
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \Exception
     */
    public function retrieveMaximumValue($dataClassName, $property, Condition $condition = null)
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
     * Return the next value - based on the highest value - of a specific property of a DataClass in the storage layer
     *
     * @param string $dataClassName
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \Exception
     */
    public function retrieveNextValue($dataClassName, $property, Condition $condition = null)
    {
        return $this->retrieveMaximumValue($dataClassName, $property, $condition) + 1;
    }

    /**
     * Retrieve a DataClassCollection of DataClass object instances from the storage layer
     *
     * @template tRetrieves
     *
     * @param class-string<tRetrieves> $dataClassName
     * @param ?\Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return DataClassCollection<tRetrieves>
     * @throws \Exception
     */
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters = null)
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
     *
     * @param string $cacheDataClassName
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     * @throws \Exception
     */
    protected function retrievesClass(
        $cacheDataClassName, $dataClassName, DataClassRetrievesParameters $parameters = null
    )
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (!$dataClassRepositoryCache->exists($cacheDataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCollection(
                    $dataClassName, $this->__retrievesClass($dataClassName, $parameters), $parameters
                );
            }

            $dataClassCollection = $dataClassRepositoryCache->get($cacheDataClassName, $parameters);
            $dataClassCollection->first();

            return $dataClassCollection;
        }
        else
        {
            return $this->__retrievesClass($dataClassName, $parameters);
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     * @throws \Exception
     */
    protected function retrievesCompositeDataClass($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);
        $this->setCompositeDataClassParameters($parentDataClassName, $dataClassName, $parameters);

        return $this->retrievesClass($parentDataClassName, $dataClassName, $parameters);
    }

    /**
     * @param string $parentDataClassName
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassParameters
     * @throws \Exception
     */
    protected function setCompositeDataClassParameters(
        $parentDataClassName, $dataClassName, DataClassParameters $parameters
    )
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
                $parameters->setJoins($joins);
            }
            else
            {
                $joins = new Joins(array($join));
                $parameters->setJoins($joins);
            }
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
    }

    /**
     * Executes the function within the context of a transaction.
     *
     * @param mixed $function
     *
     * @return mixed
     * @throws \Exception
     */
    public function transactional($function)
    {
        return $this->getDataClassDatabase()->transactional($function);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return string
     */
    public function translateCondition(Condition $condition = null)
    {
        return $this->getDataClassDatabase()->translateCondition($condition);
    }

    /**
     * Update an instance of a DataClass object in the storage layer
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return boolean
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function update(DataClass $dataClass)
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

        if ($dataClass instanceof CompositeDataClass && $dataClass::isExtended() && $result === true)
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

    /**
     * Updates any given number of properties for a specific DataClass property in the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy []
     *
     * @return boolean
     */
    public function updates(
        $dataClassName, $properties, Condition $condition, $offset = null, $count = null, $orderBy = null
    )
    {
        if ($properties instanceof DataClassProperties)
        {
            $propertiesClass = $properties;
        }
        else
        {
            $propertiesClass = new DataClassProperties($properties);
        }

        if (!$this->getDataClassDatabase()->updates($dataClassName, $propertiesClass, $condition))
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