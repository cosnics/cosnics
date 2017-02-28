<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\DataClassFactory;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
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
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataClassRepository
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    private $dataClassRepositoryCache;

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
     * @var boolean
     */
    private $queryCacheEnabled;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     * @param \Chamilo\Libraries\Storage\DataClass\DataClassFactory $dataClassFactory
     * @param boolean $isQueryCacheEnabled
     */
    public function __construct(DataClassRepositoryCache $dataClassRepositoryCache,
        DataClassDatabaseInterface $dataClassDatabase, DataClassFactory $dataClassFactory, $queryCacheEnabled = true)
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->dataClassDatabase = $dataClassDatabase;
        $this->dataClassFactory = $dataClassFactory;
        $this->queryCacheEnabled = $queryCacheEnabled;
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
     * @return boolean
     */
    public function getQueryCacheEnabled()
    {
        return $this->queryCacheEnabled;
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
     * Write an instance of a DataClass object to the storage layer
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @return boolean
     */
    public function create(DataClass $dataClass)
    {
        if (! $this->getDataClassDatabase()->create($dataClass))
        {
            return false;
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataClassRepositoryCache()->addForDataClass($dataClass, null);
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
     * @return boolean
     */
    public function createRecord($dataClassName, $record)
    {
        return $this->getDataClassDatabase()->createRecord($dataClassName, $record);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieve($dataClassName, DataClassRetrieveParameters $parameters = null)
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class_name()))
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
     * @param string $dataClassName
     * @param integer $identifier
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveById($dataClassName, $identifier)
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);

        return $this->retrieve(
            $dataClassName,
            new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($parentDataClassName, DataClass::PROPERTY_ID),
                    new StaticConditionVariable($identifier))));
    }

    /**
     * Retrieve a ResultSet of DataClass object instances from the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieves($dataClassName, $parameters = null)
    {
        if (! $parameters instanceof DataClassRetrievesParameters)
        {
            $parameters = DataClassRetrievesParameters::generate($parameters);
        }

        if (is_subclass_of($dataClassName, CompositeDataClass::class_name()))
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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     * @return string[]
     */
    public function record($dataClassName, RecordRetrieveParameters $parameters = null)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (! $dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                try
                {
                    $record = $dataClassRepositoryCache->addForRecord(
                        $dataClassName,
                        $this->__record($dataClassName, $parameters),
                        $parameters);
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
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function records($dataClassName, $parameters = null)
    {
        if (! $parameters instanceof RecordRetrievesParameters)
        {
            $parameters = RecordRetrievesParameters::generate($parameters);
        }

        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (! $dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForRecordIterator(
                    $dataClassName,
                    $this->__records($dataClassName, $parameters),
                    $parameters);
            }

            $recordIterator = $dataClassRepositoryCache->get($dataClassName, $parameters);
            $recordIterator->rewind();

            return $recordIterator;
        }
        else
        {
            return $this->__records($dataClassName, $parameters);
        }
    }

    /**
     * Update an instance of a DataClass object in the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     * @return boolean
     */
    public function update(DataClass $dataClass)
    {
        if ($dataClass instanceof CompositeDataClass)
        {
            $propertyConditionClass = $dataClass::parent_class_name();
            $dataClassTableName = $propertyConditionClass::get_table_name();
        }
        else
        {
            $propertyConditionClass = $dataClass::class_name();
            $dataClassTableName = $dataClass->get_table_name();
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable($propertyConditionClass, DataClass::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId()));

        $result = $this->getDataClassDatabase()->update(
            $dataClassTableName,
            $condition,
            $dataClass->get_default_properties());

        if ($dataClass instanceof CompositeDataClass && $dataClass::is_extended() && $result === true)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($propertyConditionClass, DataClass::PROPERTY_ID),
                new StaticConditionVariable($dataClass->getId()));

            $result = $this->getDataClassDatabase()->update(
                $dataClass->get_table_name(),
                $condition,
                $dataClass->get_additional_properties());
        }

        return $result;
    }

    /**
     * Updates any given number of properties for a specific DataClass property in the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param Condition $condition
     * @return boolean
     */
    public function updates($dataClassName, $properties, Condition $condition, $offset = null, $count = null,
        $order_by = array())
    {
        if ($properties instanceof DataClassProperties)
        {
            $propertiesClass = $properties;
        }
        else
        {
            $propertiesClass = new DataClassProperties($properties);
        }

        if (! $this->getDataClassDatabase()->updates($dataClassName, $propertiesClass, $condition))
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
     * Delete an instance of a DataClass object from the storage layer
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @return boolean
     */
    public function delete(DataClass $dataClass)
    {
        $dataClassName = ($dataClass instanceof CompositeDataClass ? $dataClass::parent_class_name() : $dataClass::class_name());

        $condition = new EqualityCondition(
            new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID),
            new StaticConditionVariable($dataClass->getId()));

        if (! $this->getDataClassDatabase()->delete($dataClassName, $condition))
        {
            return false;
        }

        if ($dataClass instanceof CompositeDataClass && $dataClass::is_extended())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($dataClass::class_name(), DataClass::PROPERTY_ID),
                new StaticConditionVariable($dataClass->getId()));

            if (! $this->getDataClassDatabase()->delete($dataClass::class_name(), $condition))
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
     * @return boolean
     */
    public function deletes($dataClassName, Condition $condition)
    {
        if (! $this->getDataClassDatabase()->delete($dataClassName, $condition))
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
     * Count the number of instances of a DataClass object in the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @return integer
     */
    public function count($dataClassName, $parameters = null)
    {
        if (! $parameters instanceof DataClassCountParameters)
        {
            $parameters = DataClassCountParameters::generate($parameters);
        }

        if (is_subclass_of($dataClassName, CompositeDataClass::class_name()))
        {
            return $this->countCompositeDataClass($dataClassName, $parameters);
        }
        else
        {
            return $this->countClass($dataClassName, $dataClassName, $parameters);
        }
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer, based on a specific property ad grouped
     * by another property in the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @return integer[]
     */
    public function countGrouped($dataClassName, DataClassCountGroupedParameters $parameters)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (! $dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCountGrouped(
                    $dataClassName,
                    $parameters,
                    $this->__countGrouped($dataClassName, $parameters));
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            return $this->__countGrouped($dataClassName, $parameters);
        }
    }

    /**
     * Retrieve all distinct values of a specific DataClass' property from the storage layer
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     * @return string[]
     */
    public function distinct($dataClassName, $parameters)
    {
        if (! $parameters instanceof DataClassDistinctParameters)
        {
            $parameters = DataClassDistinctParameters::generate($parameters);
        }

        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (! $dataClassRepositoryCache->exists($dataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassDistinct(
                    $dataClassName,
                    $parameters,
                    $this->__distinct($dataClassName, $parameters));
            }

            return $dataClassRepositoryCache->get($dataClassName, $parameters);
        }
        else
        {
            return $this->__distinct($dataClassName, $parameters);
        }
    }

    /**
     * Get the highest value of a specific property of a DataClass in the storage layer
     *
     * @param string $dataClassName
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return integer
     */
    public function retrieveMaximumValue($dataClassName, $property, Condition $condition = null)
    {
        return $this->getDataClassDatabase()->retrieveMaximumValue($dataClassName, $property, $condition);
    }

    /**
     * Return the next value - based on the highest value - of a specific property of a DataClass in the storage layer
     *
     * @param string $dataClassName
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return integer
     */
    public function retrieveNextValue($dataClassName, $property, Condition $condition = null)
    {
        return $this->retrieveMaximumValue($dataClassName, $property, $condition) + 1;
    }

    /**
     * Executes the function within the context of a transaction.
     *
     * @param mixed $function
     * @throws Exception
     * @return mixed
     */
    public function transactional($function)
    {
        return $this->getDataClassDatabase()->transactional($function);
    }

    /**
     * Get the alias of a storage unit in the storage layer
     *
     * @param string $dataClassStorageUnitName
     * @return string
     */
    public function getAlias($dataClassStorageUnitName)
    {
        return $this->getDataClassDatabase()->getAlias($dataClassStorageUnitName);
    }

    /**
     *
     * @param Condition $condition
     * @return string
     */
    public function translateCondition(Condition $condition = null)
    {
        return $this->getDataClassDatabase()->translateCondition($condition);
    }

    /**
     *
     * @param string $dataClassName
     * @param integer $identifier
     * @throws ObjectNotExistException
     * @return string
     */
    public function determineDataClassType($dataClassName, $identifier)
    {
        $conditionDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);

        $condition = new EqualityCondition(
            new PropertyConditionVariable($conditionDataClassName, DataClass::PROPERTY_ID),
            new StaticConditionVariable($identifier));

        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable($conditionDataClassName, $conditionClass::PROPERTY_TYPE))),
            $condition);

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
     * Retrieve the additional properties for a specific CompositeDataClass instance
     *
     * @param \Chamilo\Libraries\Storage\DataClass\CompositeDataClass $compositeDataClass
     * @return string[]
     */
    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $compositeDataClass)
    {
        return $this->getDataClassDatabase()->retrieveCompositeDataClassAdditionalProperties($compositeDataClass);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass
     */
    protected function __retrieveClass($dataClassName, $parameters)
    {
        $record = $this->getDataClassDatabase()->retrieve($dataClassName, $parameters);
        return $this->getDataClassFactory()->getDataClass($dataClassName, $record);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function __retrievesClass($dataClassName, $parameters)
    {
        $records = $this->getDataClassDatabase()->retrieves($dataClassName, $parameters);
        $dataClasses = array();

        foreach ($records as $record)
        {
            $dataClasses[] = $this->getDataClassFactory()->getDataClass($dataClassName, $record);
        }

        return new DataClassIterator($dataClassName, $dataClasses);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
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
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    protected function __records($dataClassName, $parameters)
    {
        return new RecordIterator($dataClassName, $this->getDataClassDatabase()->records($dataClassName, $parameters));
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @return integer
     */
    protected function __countClass($dataClassName, $parameters)
    {
        return $this->getDataClassDatabase()->count($dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @return integer[]
     */
    protected function __countGrouped($dataClassName, $parameters)
    {
        return $this->getDataClassDatabase()->countGrouped($dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     * @return string[]
     */
    protected function __distinct($dataClassName, $parameters)
    {
        return $this->getDataClassDatabase()->distinct($dataClassName, $parameters);
    }

    /**
     *
     * @param string $cacheClassName
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass
     */
    protected function retrieveClass($cacheDataClassName, $dataClassName, $parameters)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (! $dataClassRepositoryCache->exists($cacheDataClassName, $parameters))
            {
                try
                {
                    $dataClassRepositoryCache->addForDataClass(
                        $this->__retrieveClass($dataClassName, $parameters),
                        $parameters);
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
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass
     */
    protected function retrieveCompositeDataClass($dataClassName, $parameters)
    {
        $parentClassName = $this->determineCompositeDataClassParentClassName($dataClassName);

        if ($this->isCompositeDataClass($dataClassName) && ! $this->isExtensionClass($dataClassName))
        {
            $dataClassName = $this->determineCompositeDataClassType($dataClassName, $parameters);
        }

        $parameters = $this->setCompositeDataClassParameters($parentClassName, $dataClassName, $parameters);

        return $this->retrieveClass($parentClassName, $dataClassName, $parameters);
    }

    /**
     *
     * @param string $cacheClassName
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function retrievesClass($cacheDataClassName, $dataClassName, $parameters = null)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (! $dataClassRepositoryCache->exists($cacheDataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassIterator(
                    $this->__retrievesClass($dataClassName, $parameters),
                    $parameters);
            }

            $dataClassIterator = $dataClassRepositoryCache->get($cacheDataClassName, $parameters);
            $dataClassIterator->rewind();

            return $dataClassIterator;
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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function retrievesCompositeDataClass($dataClassName, $parameters)
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);
        $parameters = $this->setCompositeDataClassParameters($parentDataClassName, $dataClassName, $parameters);

        return $this->retrievesClass($parentDataClassName, $dataClassName, $parameters);
    }

    /**
     *
     * @param string $cacheDataClassName
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @return integer
     */
    protected function countClass($cacheDataClassName, $dataClassName, $parameters)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataClassRepositoryCache = $this->getDataClassRepositoryCache();

            if (! $dataClassRepositoryCache->exists($cacheDataClassName, $parameters))
            {
                $dataClassRepositoryCache->addForDataClassCount(
                    $cacheDataClassName,
                    $parameters,
                    $this->__countClass($dataClassName, $parameters));
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
     * @return integer
     */
    protected function countCompositeDataClass($dataClassName, $parameters)
    {
        $parentDataClassName = $this->determineCompositeDataClassParentClassName($dataClassName);
        $parameters = $this->setCompositeDataClassParameters($parentDataClassName, $dataClassName, $parameters);

        return $this->countClass($parentDataClassName, $dataClassName, $parameters);
    }

    /**
     *
     * @param string $parentDataClassName
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassParameters
     */
    protected function setCompositeDataClassParameters($parentDataClassName, $dataClassName, $parameters)
    {
        if ($dataClassName::is_extended())
        {
            $join = new Join(
                $parentDataClassName,
                new EqualityCondition(
                    new PropertyConditionVariable($parentDataClassName, $parentDataClassName::PROPERTY_ID),
                    new PropertyConditionVariable($dataClassName, $dataClassName::PROPERTY_ID)));

            if ($parameters->get_joins() instanceof Joins)
            {
                $joins = $parameters->get_joins();
                $joins->add($join);
                $parameters->set_joins($joins);
            }
            else
            {
                $joins = new Joins(array($join));
                $parameters->set_joins($joins);
            }
        }

        if ($this->isExtensionClass($dataClassName))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($parentDataClassName, $parentDataClassName::PROPERTY_TYPE),
                new StaticConditionVariable($dataClassName));

            if ($parameters->get_condition() instanceof Condition)
            {
                $parameters->set_condition(new AndCondition($parameters->get_condition(), $condition));
            }
            else
            {
                $parameters->set_condition($condition);
            }
        }

        return $parameters;
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
     * @param string $dataClassName
     * @return boolean
     */
    protected function isCompositeDataClass($dataClassName)
    {
        return is_subclass_of($dataClassName, CompositeDataClass::class_name());
    }

    /**
     *
     * @param string $dataClassName
     * @return boolean
     */
    protected function isExtensionClass($dataClassName)
    {
        return $this->isCompositeDataClass($dataClassName) &&
             get_parent_class($dataClassName) !== CompositeDataClass::class_name();
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     * @return string
     */
    protected function determineCompositeDataClassType($dataClassName, $parameters)
    {
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable($dataClassName, CompositeDataClass::PROPERTY_TYPE))),
            $parameters->get_condition(),
            $parameters->get_order_by(),
            $parameters->get_joins());

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
     * @return string
     */
    protected function determineCompositeDataClassParentClassName($dataClassName)
    {
        if ($this->isExtensionClass($dataClassName))
        {
            return $dataClassName::parent_class_name();
        }
        else
        {
            return $dataClassName;
        }
    }

    /**
     * *************************************************************************************************************
     * Display order functionality *
     * *************************************************************************************************************
     */

    /**
     * Changes the display orders by a given mapping array
     *
     * @param string $dataClassName
     * @param string $displayOrderProperty
     * @param integer[] $displayOrderMapping
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $displayOrderCondition
     * @return boolean
     *
     * @example $displayOrderMapping[$oldDisplayOrder] = $newDisplayOrder;
     */
    public function changeDisplayOrdersByMappingArray($dataClassName, $displayOrderProperty,
        $displayOrderMapping = array(), $displayOrderCondition = null)
    {
        foreach ($displayOrderMapping as $oldDisplayOrder => $newDisplayOrder)
        {
            if (! $this->moveDisplayOrders($dataClassName, $oldDisplayOrder, $newDisplayOrder))
            {
                return false;
            }

            $displayOrderPropertyVariable = new PropertyConditionVariable($dataClassName, $displayOrderProperty);

            $properties = new DataClassProperties(array());

            $properties->add(
                new DataClassProperty($displayOrderPropertyVariable, new StaticConditionVariable($newDisplayOrder)));

            $conditions = array();

            if ($displayOrderCondition)
            {
                $conditions[] = $displayOrderCondition;
            }

            $conditions[] = new EqualityCondition(
                $displayOrderPropertyVariable,
                new StaticConditionVariable($oldDisplayOrder));

            $condition = new AndCondition($conditions);

            return $this->updates($dataClassName, $properties, $condition);
        }

        return true;
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
     * @return boolean
     */
    public function moveDisplayOrders($dataClassName, $displayOrderProperty, $start = 1, $end = null,
        $displayOrderCondition = null)
    {
        if ($start == $end)
        {
            return false;
        }

        $displayOrderPropertyVariable = new PropertyConditionVariable($dataClassName, $displayOrderProperty);

        $conditions = array();

        if (is_null($end) || $start < $end)
        {
            $startOperator = ComparisonCondition::GREATER_THAN;
            $direction = - 1;
        }

        if (! is_null($end))
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

        if (! is_null($end))
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
            $displayOrderPropertyVariable,
            OperationConditionVariable::ADDITION,
            new StaticConditionVariable($direction));

        $properties = new DataClassProperties(array());

        $properties->add(new DataClassProperty($displayOrderPropertyVariable, $updateVariable));

        return $this->updates($dataClassName, $properties, $condition);
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context());
    }
}