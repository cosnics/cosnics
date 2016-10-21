<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Cache\DataManagerCache;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
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
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

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
     * @var \Chamilo\Configuration\Service\ConfigurationService
     */
    private $configurationService;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataManagerCache
     */
    private $dataManagerCache;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface
     */
    private $dataClassDatabase;

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationService $configurationService
     * @param \Chamilo\Libraries\Storage\Cache\DataManagerCache $dataManagerCache
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface $dataClassDatabase
     */
    public function __construct(ConfigurationService $configurationService, DataManagerCache $dataManagerCache,
        DataClassDatabaseInterface $dataClassDatabase)
    {
        $this->configurationService = $configurationService;
        $this->dataManagerCache = $dataManagerCache;
        $this->dataClassDatabase = $dataClassDatabase;
    }

    /**
     *
     * @return Chamilo\Configuration\Service\ConfigurationService
     */
    public function getConfigurationService()
    {
        return $this->configurationService;
    }

    /**
     *
     * @param Chamilo\Configuration\Service\ConfigurationService $configurationService
     */
    public function setConfigurationService($configurationService)
    {
        $this->configurationService = $configurationService;
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
    public function setDataClassDatabase($dataClassDatabase)
    {
        $this->dataClassDatabase = $dataClassDatabase;
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
        if (! $this->getDataClassDatabase()->create($object))
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
    protected function isQueryCacheEnabled()
    {
        return (bool) $this->getConfigurationService()->getSetting(
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
        return $this->getDataClassDatabase()->createRecord($className, $record);
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
        $record = $this->getDataClassDatabase()->retrieve($objectClass, $parameters);
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

    private function __record($class, $parameters)
    {
        return $this->getDataClassDatabase()->record($class, $parameters);
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
     * Retrieve an instance of a DataClass object from the storage layer by it's unique identifier
     *
     * @param $class string
     * @param $id int
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveById($className, $id)
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
        return new DataClassIterator($this->getDataClassDatabase()->retrieves($objectClass, $parameters));
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
        return new RecordIterator($this->getDataClassDatabase()->records($class, $parameters));
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
        return $this->getDataClassDatabase()->distinct($class, $parameters);
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
        if ($object instanceof CompositeDataClass)
        {
            $propertyConditionClass = $object::parent_class_name();
            $objectTableName = $parent_class::get_table_name();
        }
        else
        {
            $propertyConditionClass = $object::class_name();
            $objectTableName = $object->get_table_name();
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable($propertyConditionClass, $object::PROPERTY_ID),
            new StaticConditionVariable($object->getId()));

        $result = $this->getDataClassDatabase()->update($objectTableName, $condition, $object->get_default_properties());

        if ($object instanceof CompositeDataClass && $object::is_extended() && $result === true)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($propertyConditionClass, $object::PROPERTY_ID),
                new StaticConditionVariable($object->getId()));

            $result = $this->getDataClassDatabase()->update(
                $object->get_table_name(),
                $condition,
                $object->get_additional_properties());
        }

        return $result;
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
    public function updates($class, $properties, Condition $condition, $offset = null, $count = null, $order_by = array())
    {
        if ($properties instanceof DataClassProperties)
        {
            $propertiesClass = $properties;
        }
        else
        {
            $propertiesClass = new DataClassProperties($properties);
        }

        if (! $this->getDataClassDatabase()->updates($class, $propertiesClass, $condition, $offset, $count, $order_by))
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

        if (! $this->getDataClassDatabase()->delete($className, $condition))
        {
            return false;
        }

        if ($object instanceof CompositeDataClass && $object::is_extended())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($object::class_name(), $object::PROPERTY_ID),
                new StaticConditionVariable($object->getId()));
            if (! $this->getDataClassDatabase()->delete($object::class_name(), $condition))
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
        if (! $this->getDataClassDatabase()->delete($class, $condition))
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
        return $this->getDataClassDatabase()->count($objectClass, $parameters);
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
        return $this->getDataClassDatabase()->countGrouped($class, $parameters);
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
        return $this->getDataClassDatabase()->retrieveMaximumValue($class, $property, $condition);
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
     * Get the alias of a storage unit in the storage layer
     *
     * @param $storage_unit_name string
     * @return string
     */
    public function getAlias($storageUnitName)
    {
        return $this->getDataClassDatabase()->getAlias($storageUnitName);
    }

    /**
     * Retrieve the additional properties for a specific CompositeDataClass instance
     *
     * @param $object \libraries\storage\CompositeDataClass
     * @return multitype:string
     */
    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $object)
    {
        return $this->getDataClassDatabase()->retrieveCompositeDataClassAdditionalProperties($object);
    }

    /**
     * Executes the function within the context of a transaction.
     */
    public function transactional($function)
    {
        return $this->getDataClassDatabase()->transactional($function);
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
        return $this->getDataClassDatabase()->translateCondition($condition);
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