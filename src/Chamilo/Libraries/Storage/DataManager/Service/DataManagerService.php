<?php
namespace Chamilo\Libraries\Storage\DataManager\Service;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Storage\Cache\DataManagerCache;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManagerRepositoryInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataManagerService
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var \Chamilo\Configuration\Configuration
     */
    private $configuration;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataManagerCache
     */
    private $dataManagerCache;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\DataManagerRepositoryInterface
     */
    private $dataManagerRepository;

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     * @param \Chamilo\Libraries\Storage\Cache\DataManagerCache $dataManagerCache
     * @param \Chamilo\Libraries\Storage\DataManager\DataManagerRepositoryInterface $dataManagerRepository
     */
    public function __construct(Configuration $configuration, DataManagerCache $dataManagerCache,
        DataManagerRepositoryInterface $dataManagerRepository)
    {
        $this->configuration = $configuration;
        $this->dataManagerCache = $dataManagerCache;
        $this->dataManagerRepository = $dataManagerRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManagerRepositoryInterface
     */
    public function getDataManagerRepository()
    {
        return $this->dataManagerRepository;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManagerRepositoryInterface $dataManagerRepository
     */
    public function setDataManagerRepository($dataManagerRepository)
    {
        $this->dataManagerRepository = $dataManagerRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataManagerCache
     */
    public function getDataManagerCache()
    {
        return $this->dataManagerCache;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataManagerCache $dataManagerCache
     */
    public function setDataManagerCache($dataManagerCache)
    {
        $this->dataManagerCache = $dataManagerCache;
    }

    /**
     * Write an instance of a DataClass object to the storage layer
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @return boolean
     */
    public function create(DataClass $object)
    {
        if (! $this->getDataManagerRepository()->create($object))
        {
            return false;
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataManagerCache()->addForDataClass($object);
        }
        else
        {
            return true;
        }
    }

    /**
     *
     * @return boolean
     */
    private function isQueryCacheEnabled()
    {
        return (bool) $this->getConfiguration()->get_setting(
            array('Chamilo\Configuration', 'debug', 'enable_query_cache'));
    }

    /**
     *
     * @param string $className
     * @param string[] $record
     * @return boolean
     */
    public function createRecord($className, $record)
    {
        return $this->getDataManagerRepository()->createRecord($className, $record);
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by a set of parameters
     *
     * @param $className string
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieve($className, DataClassRetrieveParameters $parameters = null)
    {
        if (is_subclass_of($className, CompositeDataClass::class_name()))
        {
            return $this->retrieveCompositeDataClass($className, $parameters);
        }
        else
        {
            return $this->retrieveClass($className, $className, DataClass::class_name(), $parameters);
        }
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @return Ambiguous
     */
    private function retrieveCompositeDataClass($className, $parameters)
    {
        $parentClassName = $this->determineCompositeDataClassParentClassName($className);

        if ($this->isCompositeDataClass($className) && ! $this->isExtensionClass($className))
        {
            $className = $this->determineCompositeDataClassType($className, $parameters);
        }

        $parameters = $this->setCompositeDataClassParameters($parentClassName, $className, $parameters);

        return $this->retrieveClass($parentClassName, $className, CompositeDataClass::class_name(), $parameters);
    }

    private function setCompositeDataClassParameters($parentClassName, $className, $parameters)
    {
        if ($className::is_extended())
        {
            $join = new Join(
                $parentClassName,
                new EqualityCondition(
                    new PropertyConditionVariable($parentClassName, $parentClassName::PROPERTY_ID),
                    new PropertyConditionVariable($className, $className::PROPERTY_ID)));

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

        if ($this->isExtensionClass($className))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($parentClassName, $parentClassName::PROPERTY_TYPE),
                new StaticConditionVariable($className));

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

    private function __retrieveClass($objectClass, $factoryClass, $parameters)
    {
        $record = $this->processRecord($this->getDataManagerRepository()->retrieve($objectClass, $parameters));
        return $factoryClass::factory($objectClass, $record);
    }

    private function retrieveClass($cacheClass, $objectClass, $factoryClass, $parameters)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataManagerCache = $this->getDataManagerCache();

            if (! $dataManagerCache->exists($cacheClass, $parameters))
            {
                try
                {
                    $dataManagerCache->addForDataClass(
                        $this->__retrieveClass($objectClass, $factoryClass, $parameters),
                        $parameters);
                }
                catch (DataClassNoResultException $exception)
                {
                    $dataManagerCache->addForNoResult($exception);
                }
            }

            return $dataManagerCache->get($cacheClass, $parameters);
        }
        else
        {
            try
            {
                return $this->__retrieveClass($objectClass, $factoryClass, $parameters);
            }
            catch (DataClassNoResultException $exception)
            {
                return false;
            }
        }
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @return Ambiguous|boolean
     */
    private function determineCompositeDataClassType($className, $parameters)
    {
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(array(new PropertyConditionVariable($className, CompositeDataClass::PROPERTY_TYPE))),
            $parameters->get_condition(),
            $parameters->get_order_by(),
            $parameters->get_joins());

        $type = $this->record($className, $parameters);

        if (isset($type[CompositeDataClass::PROPERTY_TYPE]))
        {
            return $type[CompositeDataClass::PROPERTY_TYPE];
        }
        else
        {
            return false;
        }
    }

    private static function __record($class, $parameters)
    {
        return $this->processRecord($this->getDataManagerRepository()->record($class, $parameters));
    }

    public function record($class, RecordRetrieveParameters $parameters = null)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataManagerCache = $this->getDataManagerCache();

            if (! $dataManagerCache->exists($class, $parameters))
            {
                try
                {
                    $record = $dataManagerCache->addForRecord($class, $this->__record($class, $parameters), $parameters);
                }
                catch (DataClassNoResultException $exception)
                {
                    $dataManagerCache->addForNoResult($exception);
                }
            }
            return $dataManagerCache->get($class, $parameters);
        }
        else
        {
            try
            {
                return $this->__record($class, $parameters);
            }
            catch (DataClassNoResultException $exception)
            {
                return false;
            }
        }
    }

    /**
     * Processes a given record by transforming to the correct type
     *
     * @param mixed[] $record
     * @return mixed[]
     */
    public static function processRecord($record)
    {
        foreach ($record as &$field)
        {
            if (is_resource($field))
            {
                $data = '';
                while (! feof($field))
                {
                    $data .= fread($field, 1024);
                }
                $field = $data;
            }
        }

        return $record;
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by it's unique identifier
     *
     * @param $class string
     * @param $id int
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public static function retrieveById($className, $id)
    {
        $parentClass = $this->determineCompositeDataClassParentClassName($className);

        return $this->retrieve(
            $className,
            new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($parentClass, $parentClass::PROPERTY_ID),
                    new StaticConditionVariable($id))));
    }

    /**
     *
     * @param string $className
     * @return boolean
     */
    private function isCompositeDataClass($className)
    {
        return is_subclass_of($className, CompositeDataClass::class_name());
    }

    /**
     *
     * @param string $className
     * @return boolean
     */
    private function isExtensionClass($className)
    {
        return $this->isCompositeDataClass($className) &&
             get_parent_class($className) !== CompositeDataClass::class_name();
    }

    /**
     *
     * @param string $className
     * @return string
     */
    private function determineCompositeDataClassParentClassName($className)
    {
        if ($this->isExtensionClass($className))
        {
            return $className::parent_class_name();
        }
        else
        {
            return $className;
        }
    }

    /**
     *
     * @param string $className
     * @param integer $identifier
     * @throws ObjectNotExistException
     * @return string
     */
    public function determineDataClassType($className, $identifier)
    {
        $conditionClass = $this->determineCompositeDataClassParentClassName($className);

        $condition = new EqualityCondition(
            new PropertyConditionVariable($conditionClass, $conditionClass::PROPERTY_ID),
            new StaticConditionVariable($identifier));

        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable($conditionClass, $conditionClass::PROPERTY_TYPE))),
            $condition);

        $type = $this->record($conditionClass, $parameters);

        if (isset($type[$conditionClass::PROPERTY_TYPE]))
        {
            return $type[$conditionClass::PROPERTY_TYPE];
        }
        else
        {
            throw new ObjectNotExistException($identifier);
        }
    }

    /**
     * Retrieve a ResultSet of DataClass object instances from the storage layer
     *
     * @param $class string
     * @param $parameters DataClassRetrievesParameters
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieves($class, $parameters = null)
    {
        if (! $parameters instanceof DataClassRetrievesParameters)
        {
            $parameters = DataClassRetrievesParameters::generate($parameters);
        }

        if (is_subclass_of($class, CompositeDataClass::class_name()))
        {
            return $this->retrievesCompositeDataClass($class, $parameters);
        }
        else
        {
            return $this->retrievesClass($class, $class, $parameters);
        }
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return Ambiguous
     */
    private function retrievesCompositeDataClass($className, $parameters)
    {
        $parentClassName = $this->determineCompositeDataClassParentClassName($className);
        $parameters = $this->setCompositeDataClassParameters($parentClassName, $className, $parameters);

        return $this->retrievesClass($parentClassName, $className, $parameters);
    }

    /**
     *
     * @param string $objectClass
     * @param unknown $parameters
     */
    private function __retrievesClass($objectClass, $parameters)
    {
        return $this->getDataManagerRepository()->retrieves($objectClass, $parameters);
    }

    private function retrievesClass($cacheClass, $objectClass, $parameters = null)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataManagerCache = $this->getDataManagerCache();

            if (! $dataManagerCache->exists($cacheClass, $parameters))
            {
                $dataManagerCache->addForDataClassResultSet(
                    $this->__retrievesClass($objectClass, $parameters),
                    $parameters);
            }

            $resultSet = $dataManagerCache->get($cacheClass, $parameters);
            $resultSet->reset();

            return $resultSet;
        }
        else
        {
            return $this->retrievesClass($objectClass, $parameters);
        }
    }

    private function __records($class, $parameters)
    {
        return $this->getDataManagerRepository()->records($class, $parameters);
    }

    public function records($class, $parameters = null)
    {
        if (! $parameters instanceof RecordRetrievesParameters)
        {
            $parameters = RecordRetrievesParameters::generate($parameters);
        }

        if ($this->isQueryCacheEnabled())
        {
            $dataManagerCache = $this->getDataManagerCache();

            if (! $dataManagerCache->exists($class, $parameters))
            {
                $dataManagerCache->addForRecordResultSet($class, $this->__records($class, $parameters), $parameters);
            }

            $resultSet = $dataManagerCache->get($class, $parameters);
            $resultSet->reset();

            return $resultSet;
        }
        else
        {
            return $this->__records($class, $parameters);
        }
    }

    private function __distinct($class, $parameters)
    {
        return $this->getDataManagerRepository()->distinct($class, $parameters);
    }

    /**
     * Retrieve all distinct values of a specific DataClass' property from the storage layer
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return array
     */
    public function distinct($class, $parameters)
    {
        if (! $parameters instanceof DataClassDistinctParameters)
        {
            $parameters = DataClassDistinctParameters::generate($parameters);
        }

        if ($this->isQueryCacheEnabled())
        {
            $dataManagerCache = $this->getDataManagerCache();

            if (! $dataManagerCache->exists($class, $parameters))
            {
                $dataManagerCache->addForDataClassDistinct($class, $parameters, $this->__distinct($class, $parameters));
            }

            return $dataManagerCache->get($class, $parameters);
        }
        else
        {
            return $this->__distinct($class, $parameters);
        }
    }

    /**
     * Update an instance of a DataClass object in the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     * @return boolean
     */
    public function update(DataClass $object)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($object::class_name(), $object::PROPERTY_ID),
            new StaticConditionVariable($object->getId()));
        return $this->getDataManagerRepository()->update($object, $condition);
    }

    /**
     * Updates any given number of properties for a specific DataClass property in the storage layer
     *
     * @param $class string
     * @param $properties mixed
     * @param $condition \libraries\storage\Condition
     * @param $offset int
     * @param $count int
     * @param $order_by multitype:\common\libraries\ObjectTableOrder
     * @return boolean
     */
    public static function updates($class, $properties, Condition $condition, $offset = null, $count = null, $order_by = array())
    {
        if ($properties instanceof DataClassProperties)
        {
            $propertiesClass = $properties;
        }
        else
        {
            $propertiesClass = new DataClassProperties($properties);
        }

        if (! $this->getDataManagerRepository()->updates(
            $class,
            $propertiesClass,
            $condition,
            $offset,
            $count,
            $order_by))
        {
            return false;
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataManagerCache()->truncate($class);
        }
        else
        {
            return true;
        }
    }

    /**
     * Delete an instance of a DataClass object from the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     * @return boolean
     */
    public function delete(DataClass $object)
    {
        $className = ($object instanceof CompositeDataClass ? $object::parent_class_name() : $object::class_name());

        $condition = new EqualityCondition(
            new PropertyConditionVariable($className, $className::PROPERTY_ID),
            new StaticConditionVariable($object->getId()));

        if (! $this->getDataManagerRepository()->delete($className, $condition))
        {
            return false;
        }

        if ($object instanceof CompositeDataClass && $object::is_extended())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($object::class_name(), $object::PROPERTY_ID),
                new StaticConditionVariable($object->getId()));
            if (! $this->getDataManagerRepository()->delete($object::class_name(), $condition))
            {
                return false;
            }
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataManagerCache()->truncate($className);
        }
        else
        {
            return true;
        }
    }

    /**
     * Deletes any given number of instance of the DataClass object from the storage layer
     *
     * @param $class string
     * @param $condition \libraries\storage\Condition
     * @return boolean
     */
    public function deletes($class, Condition $condition)
    {
        if (! $this->getDataManagerRepository()->delete($class, $condition))
        {
            return false;
        }

        if ($this->isQueryCacheEnabled())
        {
            return $this->getDataManagerCache()->truncate($class);
        }
        else
        {
            return true;
        }
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassCountParameters
     * @return int
     */
    public function count($class, $parameters = null)
    {
        if (! $parameters instanceof DataClassCountParameters)
        {
            $parameters = DataClassCountParameters::generate($parameters);
        }

        if (is_subclass_of($class, CompositeDataClass::class_name()))
        {
            return $this->countCompositeDataClass($class, $parameters);
        }
        else
        {
            return $this->countClass($class, $class, $parameters);
        }
    }

    private function countCompositeDataClass($className, $parameters)
    {
        $parentClassName = $this->determineCompositeDataClassParentClassName($className);
        $parameters = $this->setCompositeDataClassParameters($parentClassName, $className, $parameters);

        return $this->countClass($parentClassName, $className, $parameters);
    }

    private function __countClass($objectClass, $parameters)
    {
        return $this->getDataManagerRepository()->count($objectClass, $parameters);
    }

    private function countClass($cacheClass, $objectClass, $parameters)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataManagerCache = $this->getDataManagerCache();

            if (! $dataManagerCache->exists($cacheClass, $parameters))
            {
                $dataManagerCache->addForDataClassCount(
                    $cacheClass,
                    $parameters,
                    $this->__countClass($objectClass, $parameters));
            }

            return $dataManagerCache->get($cacheClass, $parameters);
        }
        else
        {
            return $this->__countClass($objectClass, $parameters);
        }
    }

    private function __countGrouped($class, $parameters)
    {
        return $this->getDataManagerRepository()->count_grouped($class, $parameters);
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer, based on a specific property ad grouped
     * by another property in the storage layer
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassCountGroupedParameters
     * @return multitype:int
     */
    public function countGrouped($class, DataClassCountGroupedParameters $parameters)
    {
        if ($this->isQueryCacheEnabled())
        {
            $dataManagerCache = $this->getDataManagerCache();

            if (! $dataManagerCache->exists($class, $parameters))
            {
                $dataManagerCache->addForDataClassCountGrouped(
                    $class,
                    $parameters,
                    $this->__countGrouped($class, $parameters));
            }

            return $dataManagerCache->get($class, $parameters);
        }
        else
        {
            return $this->__countGrouped($class, $parameters);
        }
    }

    /**
     * Get the highest value of a specific property of a DataClass in the storage layer
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return int
     */
    public function retrieveMaximumValue($class, $property, Condition $condition = null)
    {
        return $this->getDataManagerRepository()->retrieve_maximum_value($class, $property, $condition);
    }

    /**
     * Return the next value - based on the highest value - of a specific property of a DataClass in the storage layer
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return int
     */
    public function retrieveNextValue($class, $property, Condition $condition = null)
    {
        return $this->retrieveMaximumValue($class, $property, $condition) + 1;
    }

    /**
     * Create a storage unit in the storage layer
     *
     * @param $name string
     * @param $properties multitype:mixed
     * @param $indexes multitype:mixed
     * @return boolean
     */
    public function createStorageUnit($name, $properties, $indexes)
    {
        return $this->getDataManagerRepository()->create_storage_unit($name, $properties, $indexes);
    }

    /**
     * Determine whether a storage unit exists in the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function storageUnitExists($name)
    {
        return $this->getDataManagerRepository()->storage_unit_exists($name);
    }

    /**
     * Drop a storage unit from the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function dropStorageUnit($name)
    {
        return $this->getDataManagerRepository()->drop_storage_unit($name);
    }

    /**
     * Rename a storage unit
     *
     * @param string $old_name
     * @param string $new_name
     */
    public function renameStorageUnit($old_name, $new_name)
    {
        return $this->getDataManagerRepository()->rename_storage_unit($old_name, $new_name);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param multitype:mixed $attributes
     * @return boolean
     */
    public function alterStorageUnit($type, $table_name, $property, $attributes = array())
    {
        return $this->getDataManagerRepository()->alter_storage_unit($type, $table_name, $property, $attributes);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $name
     * @param multitype:string $columns
     * @return boolean
     */
    public function alterStorageUnit_index($type, $table_name, $name = null, $columns = array())
    {
        return $this->getDataManagerRepository()->alter_storage_unit_index($type, $table_name, $name, $columns);
    }

    /**
     * Truncate a storage unit in the storage layer and optionally optimize it afterwards
     *
     * @param $name string
     * @param $optimize boolean
     * @return boolean
     */
    public static function truncateStorageUnit($name, $optimize = true)
    {
        if (! $this->getDataManagerRepository()->truncate_storage_unit($name))
        {
            return false;
        }

        if ($optimize && ! $this->optimizeStorageUnit($name))
        {
            return false;
        }

        return true;
    }

    /**
     * Optimize a storage unit in the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function optimizeStorageUnit($name)
    {
        return $this->getDataManagerRepository()->optimize_storage_unit($name);
    }

    /**
     * Get the alias of a storage unit in the storage layer
     *
     * @param $storage_unit_name string
     * @return string
     */
    public function getAlias($storageUnitName)
    {
        return $this->getDataManagerRepository()->get_alias($storageUnitName);
    }

    /**
     * Retrieve the additional properties for a specific CompositeDataClass instance
     *
     * @param $object \libraries\storage\CompositeDataClass
     * @return multitype:string
     */
    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $object)
    {
        return $this->getDataManagerRepository()->retrieve_composite_data_class_additional_properties($object);
    }

    /**
     * Executes the function within the context of a transaction.
     */
    public function transactional($function)
    {
        return $this->getDataManagerRepository()->transactional($function);
    }

    /**
     * *************************************************************************************************************
     * Display order functionality *
     * *************************************************************************************************************
     */

    /**
     * Changes the display orders by a given mapping array
     *
     * @param string $class_name
     * @param string $display_order_property
     * @param int[] $display_order_mapping
     * @param \libraries\storage\Condition $display_order_condition
     *
     * @return bool
     *
     * @example $display_order_mapping[$old_display_order] = $new_display_order;
     */
    public function changeDisplayOrdersByMappingArray($class_name, $displayOrderProperty, $displayOrderMapping = array(),
        $displayOrderCondition = null)
    {
        foreach ($displayOrderMapping as $oldDisplayOrder => $newDisplayOrder)
        {
            if (! $this->moveDisplayOrders($class_name, $oldDisplayOrder, $newDisplayOrder))
            {
                return false;
            }

            $displayOrderPropertyVariable = new PropertyConditionVariable($class_name, $displayOrderProperty);

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

            return $this->updates($class_name, $properties, $condition);
        }

        return true;
    }

    /**
     * Generic function to move display orders Usage Start & End value: Subset of display orders Start value only: all
     * display orders from given start until the end
     *
     * @param string $class_name
     * @param string $display_order_property
     * @param int $start
     * @param int $end
     * @param \libraries\storage\Condition $display_order_condition
     *
     * @return bool
     */
    public function moveDisplayOrders($className, $displayOrderProperty, $start = 1, $end = null,
        $displayOrderCondition = null)
    {
        if ($start == $end)
        {
            return false;
        }

        $displayOrderPropertyVariable = new PropertyConditionVariable($className, $displayOrderProperty);

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

        return $this->updates($className, $properties, $condition);
    }

    /**
     *
     * @param Condition $condition
     * @return string
     */
    public function translateCondition(Condition $condition = null)
    {
        return $this->getDataManagerRepository()->translateCondition($condition);
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