<?php
namespace Chamilo\Libraries\Storage\DataManager;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Cache\DataClassCountCache;
use Chamilo\Libraries\Storage\Cache\DataClassCountGroupedCache;
use Chamilo\Libraries\Storage\Cache\DataClassDistinctCache;
use Chamilo\Libraries\Storage\Cache\DataClassResultCache;
use Chamilo\Libraries\Storage\Cache\RecordCache;
use Chamilo\Libraries\Storage\Cache\RecordResultCache;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * General and basic DataManager, providing basic functionality for all other DataManager objects
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @deprecated Replaced by the service-based DataClassRepository and StorageUnitRepository
 */
class DataManager
{
    use ClassContext;

    const ALTER_STORAGE_UNIT_ADD = 1;
    const ALTER_STORAGE_UNIT_ADD_INDEX = 7;
    const ALTER_STORAGE_UNIT_ADD_PRIMARY_KEY = 5;
    const ALTER_STORAGE_UNIT_ADD_UNIQUE = 8;
    const ALTER_STORAGE_UNIT_CHANGE = 2;
    const ALTER_STORAGE_UNIT_DROP = 3;
    const ALTER_STORAGE_UNIT_DROP_INDEX = 6;
    const ALTER_STORAGE_UNIT_DROP_PRIMARY_KEY = 4;

    const TYPE_DOCTRINE = 'Doctrine';
    const TYPE_MDB2 = 'Mdb2';

    /**
     * Instance of this class for the singleton pattern
     *
     * @var \Chamilo\Libraries\Storage\DataManager\DataManager
     */
    public static $instance;

    /**
     * @param string $objectClass
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     */
    private static function __countClass($objectClass, $parameters)
    {
        return static::getInstance()->count($objectClass, $parameters);
    }

    /**
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return integer[]
     */
    private static function __countGrouped($class, $parameters)
    {
        return static::getInstance()->count_grouped($class, $parameters);
    }

    /**
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]
     */
    private static function __distinct($class, $parameters)
    {
        return static::getInstance()->distinct($class, $parameters);
    }

    /**
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     *
     * @return string[]
     */
    private static function __record($class, $parameters)
    {
        return static::process_record(static::getInstance()->record($class, $parameters));
    }

    /**
     * @param string $objectClass
     * @param string $factoryClass
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private static function __retrieveClass($objectClass, $factoryClass, $parameters)
    {
        $record = static::process_record(static::getInstance()->retrieve($objectClass, $parameters));

        return $factoryClass::factory($objectClass, $record);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param string[][] $attributes
     *
     * @return boolean
     */
    public static function alter_storage_unit($type, $table_name, $property, $attributes = array())
    {
        return static::getInstance()->alter_storage_unit($type, $table_name, $property, $attributes);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $name
     * @param string[] $columns
     *
     * @return boolean
     */
    public static function alter_storage_unit_index($type, $table_name, $name = null, $columns = array())
    {
        return static::getInstance()->alter_storage_unit_index($type, $table_name, $name, $columns);
    }

    /**
     * Changes the display orders by a given mapping array
     *
     * @param string $class_name
     * @param string $display_order_property
     * @param int[] $display_order_mapping
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $display_order_condition
     *
     * @return bool
     *
     * @throws \Exception
     * @example $display_order_mapping[$old_display_order] = $new_display_order;
     */
    public static function change_display_orders_by_mapping_array(
        $class_name, $display_order_property, $display_order_mapping = array(), $display_order_condition = null
    )
    {
        return self::getDataClassRepository()->changeDisplayOrdersByMappingArray(
            $class_name, $display_order_property, $display_order_mapping, $display_order_condition
        );
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return int
     * @throws \ReflectionException
     */
    public static function count($class, $parameters = null)
    {
        return self::getDataClassRepository()->count($class, $parameters);
    }

    /**
     * @param string $cacheClass
     * @param string $objectClass
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \Exception
     */
    private static function countClass($cacheClass, $objectClass, $parameters)
    {
        if (!DataClassCountCache::exists($cacheClass, $parameters))
        {
            DataClassCountCache::add($cacheClass, $parameters, static::__countClass($objectClass, $parameters));
        }

        return DataClassCountCache::get($cacheClass, $parameters);
    }

    /**
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \Exception
     */
    private static function countCompositeDataClass($className, $parameters)
    {
        $parentClassName = static::determineCompositeDataClassParentClassName($className);
        $parameters = static::setCompositeDataClassParameters($parentClassName, $className, $parameters);

        return static::countClass($parentClassName, $className, $parameters);
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer, based on a specific property ad grouped
     * by another property in the storage layer
     *
     * @param $class string
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters
     *
     * @return integer[]
     * @throws \Exception
     */
    public static function count_grouped($class, DataClassCountGroupedParameters $parameters)
    {
        return self::getDataClassRepository()->countGrouped($class, $parameters);
    }

    /**
     * Write an instance of a DataClass object to the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public static function create(DataClass $object)
    {
        return self::getDataClassRepository()->create($object);
    }

    /**
     * @param string $class_name
     * @param string[] $record
     *
     * @return bool
     */
    public static function create_record($class_name, $record)
    {
        return self::getDataClassRepository()->createRecord($class_name, $record);
    }

    /**
     * Create a storage unit in the storage layer
     *
     * @param $name string
     * @param string[] $properties
     * @param string[] $indexes
     *
     * @return boolean
     */
    public static function create_storage_unit($name, $properties, $indexes)
    {
        return static::getInstance()->create_storage_unit($name, $properties, $indexes);
    }

    /**
     * Delete an instance of a DataClass object from the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     *
     * @return boolean
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function delete(DataClass $object)
    {
        return self::getDataClassRepository()->delete($object);
    }

    /**
     * Deletes any given number of instance of the DataClass object from the storage layer
     *
     * @param $class string
     * @param $condition \Chamilo\Libraries\Storage\Query\Condition\Condition
     *
     * @return boolean
     */
    public static function deletes($class, Condition $condition)
    {
        return self::getDataClassRepository()->deletes($class, $condition);
    }

    /**
     *
     * @param string $className
     *
     * @return string
     * @throws \ReflectionException
     */
    private static function determineCompositeDataClassParentClassName($className)
    {
        if (static::isExtensionClass($className))
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
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return string|boolean
     * @throws \Exception
     */
    private static function determineCompositeDataClassType($className, $parameters)
    {
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable($className, CompositeDataClass::PROPERTY_TYPE))
            ), $parameters->getCondition(), $parameters->getOrderBy(), $parameters->getJoins()
        );

        $type = static::record($className, $parameters);

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
     * @param string $className
     * @param integer $identifier
     *
     * @return string
     * @throws ObjectNotExistException
     * @throws Exception
     */
    public static function determineDataClassType($className, $identifier)
    {
        return self::getDataClassRepository()->determineDataClassType($className, $identifier);
    }

    /**
     * Retrieve all distinct values of a specific DataClass' property from the storage layer
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]
     * @throws \Exception
     */
    public static function distinct($class, $parameters)
    {
        return self::getDataClassRepository()->distinct($class, $parameters);
    }

    /**
     * Drop a storage unit from the storage layer
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function drop_storage_unit($name)
    {
        return static::getInstance()->drop_storage_unit($name);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     * @throws \Exception
     */
    public static function getDataClassRepository()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository'
        );
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data manager.
     * The configuration determines which
     * data manager class is to be instantiated
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database
     */
    public static function getInstance()
    {
        $type = static::get_type();

        if (!isset(self::$instance[$type]))
        {
            $class = '\Chamilo\Libraries\Storage\DataManager\\' . $type . '\Database';
            self::$instance[$type] = new $class();
        }

        return self::$instance[$type];
    }

    /**
     * Set the instance of the DataManager
     *
     * @param \Chamilo\Libraries\Storage\DataManager\DataManager $instance
     */
    public static function set_instance($instance)
    {
        self::$instance[ClassnameUtilities::getInstance()->getNamespaceFromObject($instance)] = $instance;
    }

    /**
     * Get the alias of a storage unit in the storage layer
     *
     * @param $storage_unit_name string
     *
     * @return string
     */
    public static function get_alias($storage_unit_name)
    {
        return static::getInstance()->get_alias($storage_unit_name);
    }

    /**
     * Gets the type of DataManager to be instantiated, by default configured in the main Chamilo configuration file
     *
     * @return string
     */
    public static function get_type()
    {
        return Database::STORAGE_TYPE;
    }

    /**
     *
     * @param string $className
     *
     * @return boolean
     * @throws \ReflectionException
     */
    private static function isCompositeDataClass($className)
    {
        return is_subclass_of($className, CompositeDataClass::class);
    }

    /**
     *
     * @param string $className
     *
     * @return boolean
     * @throws \ReflectionException
     */
    private static function isExtensionClass($className)
    {
        return static::isCompositeDataClass($className) && get_parent_class($className) !== CompositeDataClass::class;
    }

    /**
     * Generic function to move display orders Usage Start & End value: Subset of display orders Start value only: all
     * display orders from given start until the end
     *
     * @param string $class_name
     * @param string $display_order_property
     * @param int $start
     * @param int $end
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $display_order_condition
     *
     * @return bool
     * @throws \Exception
     */
    public static function move_display_orders(
        $class_name, $display_order_property, $start = 1, $end = null, $display_order_condition = null
    )
    {
        return self::getDataClassRepository()->moveDisplayOrders(
            $class_name, $display_order_property, $start, $end, $display_order_condition
        );
    }

    /**
     * Optimize a storage unit in the storage layer
     *
     * @param $name string
     *
     * @return boolean
     */
    public static function optimize_storage_unit($name)
    {
        return static::getInstance()->optimize_storage_unit($name);
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
     * Processes a given record by transforming to the correct type
     *
     * @param mixed[] $record
     *
     * @return mixed[]
     */
    public static function process_record($record)
    {
        foreach ($record as &$field)
        {
            if (is_resource($field))
            {
                $data = '';
                while (!feof($field))
                {
                    $data .= fread($field, 1024);
                }
                $field = $data;
            }
        }

        return $record;
    }

    /**
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     *
     * @return string[]
     * @throws \Exception
     */
    public static function record($class, RecordRetrieveParameters $parameters = null)
    {
        return self::getDataClassRepository()->record($class, $parameters);
    }

    /**
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     * @throws \Exception
     */
    public static function records($class, $parameters = null)
    {
        return self::getDataClassRepository()->records($class, $parameters);
    }

    /**
     * Rename a storage unit
     *
     * @param string $old_name
     * @param string $new_name
     *
     * @return boolean
     */
    public static function rename_storage_unit($old_name, $new_name)
    {
        return static::getInstance()->rename_storage_unit($old_name, $new_name);
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by a set of parameters
     *
     * @param $class string
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public static function retrieve($class, DataClassRetrieveParameters $parameters = null)
    {
        return self::getDataClassRepository()->retrieve($class, $parameters);
    }

    /**
     * @param string $cacheClass
     * @param string $objectClass
     * @param string $factoryClass
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return bool|mixed
     * @throws \Exception
     */
    private static function retrieveClass($cacheClass, $objectClass, $factoryClass, $parameters)
    {
        if (!DataClassCache::exists($cacheClass, $parameters))
        {
            try
            {
                DataClassResultCache::add(
                    static::__retrieveClass($objectClass, $factoryClass, $parameters), $parameters
                );
            }
            catch (DataClassNoResultException $exception)
            {
                DataClassResultCache::no_result($exception);
            }
        }

        return DataClassCache::get($cacheClass, $parameters);
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return bool|mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    private static function retrieveCompositeDataClass($className, $parameters)
    {
        $parentClassName = static::determineCompositeDataClassParentClassName($className);

        if (static::isCompositeDataClass($className) && !static::isExtensionClass($className))
        {
            $className = static::determineCompositeDataClassType($className, $parameters);
        }

        if (!$className)
        {
            throw new Exception('Could not determine the composite data class type');
        }

        $parameters = static::setCompositeDataClassParameters($parentClassName, $className, $parameters);

        return static::retrieveClass($parentClassName, $className, CompositeDataClass::class, $parameters);
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by it's unique identifier
     *
     * @param $class string
     * @param $id int
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function retrieve_by_id($class, $id)
    {
        return self::getDataClassRepository()->retrieveById($class, $id);
    }

    /**
     * Retrieve the additional properties for a specific CompositeDataClass instance
     *
     * @param \Chamilo\Libraries\Storage\DataClass\CompositeDataClass $object
     *
     * @return string[]
     */
    public static function retrieve_composite_data_class_additional_properties(CompositeDataClass $object)
    {
        return self::getDataClassRepository()->retrieveCompositeDataClassAdditionalProperties($object);
    }

    /**
     * Get the highest value of a specific property of a DataClass in the storage layer
     *
     * @param string $class
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public static function retrieve_maximum_value($class, $property, Condition $condition = null)
    {
        return self::getDataClassRepository()->retrieveMaximumValue($class, $property, $condition);
    }

    /**
     * Return the next value - based on the highest value - of a specific property of a DataClass in the storage layer
     *
     * @param string $class
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public static function retrieve_next_value($class, $property, Condition $condition = null)
    {
        return self::getDataClassRepository()->retrieveNextValue($class, $property, $condition);
    }

    /**
     * Retrieve a ResultSet of DataClass object instances from the storage layer
     *
     * @param $class string
     * @param DataClassRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     * @throws \Exception
     */
    public static function retrieves($class, $parameters = null)
    {
        return self::getDataClassRepository()->retrieves($class, $parameters);
    }

    /**
     * @param $parentClassName
     * @param $className
     * @param $parameters
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    private static function setCompositeDataClassParameters($parentClassName, $className, $parameters)
    {
        if ($className::is_extended())
        {
            $join = new Join(
                $parentClassName, new EqualityCondition(
                    new PropertyConditionVariable($parentClassName, $parentClassName::PROPERTY_ID),
                    new PropertyConditionVariable($className, $className::PROPERTY_ID)
                )
            );

            if ($parameters->get_joins() instanceof Joins)
            {
                $joins = $parameters->get_joins();
                $joins->prepend($join);
                $parameters->set_joins($joins);
            }
            else
            {
                $joins = new Joins(array($join));
                $parameters->set_joins($joins);
            }
        }

        if (static::isExtensionClass($className))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($parentClassName, $parentClassName::PROPERTY_TYPE),
                new StaticConditionVariable($className)
            );

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
     * Determine whether a storage unit exists in the storage layer
     *
     * @param $name string
     *
     * @return boolean
     */
    public static function storage_unit_exists($name)
    {
        return static::getInstance()->storage_unit_exists($name);
    }

    /**
     * Executes the function within the context of a transaction.
     *
     * @param $function
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public static function transactional($function)
    {
        return self::getDataClassRepository()->transactional($function);
    }

    /**
     *
     * @param Condition $condition
     *
     * @return string
     */
    public static function translateCondition(Condition $condition = null)
    {
        return static::getInstance()->translateCondition($condition);
    }

    /**
     * Truncate a storage unit in the storage layer and optionally optimize it afterwards
     *
     * @param $name string
     * @param $optimize boolean
     *
     * @return boolean
     */
    public static function truncate_storage_unit($name, $optimize = true)
    {
        if (!static::getInstance()->truncate_storage_unit($name))
        {
            return false;
        }

        if ($optimize && !static::optimize_storage_unit($name))
        {
            return false;
        }

        return true;
    }

    /**
     * Update an instance of a DataClass object in the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public static function update(DataClass $object)
    {
        return self::getDataClassRepository()->update($object);
    }

    /**
     * Updates any given number of properties for a specific DataClass property in the storage layer
     *
     * @param string $class
     * @param mixed $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     *
     * @return boolean
     * @throws \Exception
     */
    public static function updates(
        $class, $properties, Condition $condition, $offset = null, $count = null, $order_by = array()
    )
    {
        return self::getDataClassRepository()->updates($class, $properties, $condition, $offset, $count, $order_by);
    }
}
