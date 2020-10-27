<?php
namespace Chamilo\Libraries\Storage\DataManager;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
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
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param string[][] $attributes
     *
     * @return boolean
     * @throws \Exception
     */
    public static function alter_storage_unit($type, $table_name, $property, $attributes = array())
    {
        return self::getStorageUnitRepository()->alter($type, $table_name, $property, $attributes);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string|null $name
     * @param string[] $columns
     *
     * @return boolean
     * @throws \Exception
     */
    public static function alter_storage_unit_index($type, $table_name, $name = null, $columns = array())
    {
        return self::getStorageUnitRepository()->alterIndex($type, $table_name, $name, $columns);
    }

    /**
     * Changes the display orders by a given mapping array
     *
     * @param string $class_name
     * @param string $display_order_property
     * @param int[] $display_order_mapping
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $display_order_condition
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
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters|null $parameters
     *
     * @return int
     * @throws \ReflectionException
     */
    public static function count($class, $parameters = null)
    {
        return self::getDataClassRepository()->count($class, $parameters);
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public static function create_storage_unit($name, $properties, $indexes)
    {
        return self::getStorageUnitRepository()->create($name, $properties, $indexes);
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
     * @throws \Exception
     */
    public static function deletes($class, Condition $condition)
    {
        return self::getDataClassRepository()->deletes($class, $condition);
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
     * @throws \Exception
     */
    public static function drop_storage_unit($name)
    {
        return self::getStorageUnitRepository()->drop($name);
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
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository
     * @throws \Exception
     */
    public static function getStorageUnitRepository()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\Storage\DataManager\Doctrine\Repository\StorageUnitRepository'
        );
    }

    /**
     * Get the alias of a storage unit in the storage layer
     *
     * @param $storage_unit_name string
     *
     * @return string
     * @throws \Exception
     */
    public static function get_alias($storage_unit_name)
    {
        return self::getDataClassRepository()->getAlias($storage_unit_name);
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
     * Generic function to move display orders Usage Start & End value: Subset of display orders Start value only: all
     * display orders from given start until the end
     *
     * @param string $class_name
     * @param string $display_order_property
     * @param integer $start
     * @param integer|null $end
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $display_order_condition
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
     * @throws \Exception
     */
    public static function optimize_storage_unit($name)
    {
        return self::getStorageUnitRepository()->optimize($name);
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
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters|null $parameters
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
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters|null $parameters
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
     * @throws \Exception
     */
    public static function rename_storage_unit($old_name, $new_name)
    {
        return self::getStorageUnitRepository()->rename($old_name, $new_name);
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by a set of parameters
     *
     * @param $class string
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters|null $parameters
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Exception
     */
    public static function retrieve($class, DataClassRetrieveParameters $parameters = null)
    {
        return self::getDataClassRepository()->retrieve($class, $parameters);
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by it's unique identifier
     *
     * @param $class string
     * @param $id int
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
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
     * @throws \Exception
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     * @throws \Exception
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     * @throws \Exception
     */
    public static function retrieve_next_value($class, $property, Condition $condition = null)
    {
        return self::getDataClassRepository()->retrieveNextValue($class, $property, $condition);
    }

    /**
     * Retrieve a DataClassIterator of DataClass object instances from the storage layer
     *
     * @param $class string
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters|null $parameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     * @throws \Exception
     */
    public static function retrieves($class, $parameters = null)
    {
        return self::getDataClassRepository()->retrieves($class, $parameters);
    }

    /**
     * Determine whether a storage unit exists in the storage layer
     *
     * @param $name string
     *
     * @return boolean
     * @throws \Exception
     */
    public static function storage_unit_exists($name)
    {
        return self::getStorageUnitRepository()->exists($name);
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return string
     * @throws \Exception
     */
    public static function translateCondition(Condition $condition = null)
    {
        return self::getDataClassRepository()->translateCondition($condition);
    }

    /**
     * Truncate a storage unit in the storage layer and optionally optimize it afterwards
     *
     * @param $name string
     * @param $optimize boolean
     *
     * @return boolean
     * @throws \Exception
     */
    public static function truncate_storage_unit($name, $optimize = true)
    {
        return self::getStorageUnitRepository()->truncate($name, $optimize);
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
     * @param integer|null $offset
     * @param integer|null $count
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
