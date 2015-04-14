<?php
namespace Chamilo\Libraries\Storage\DataManager;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Cache\DataClassCountCache;
use Chamilo\Libraries\Storage\Cache\DataClassCountGroupedCache;
use Chamilo\Libraries\Storage\Cache\DataClassDistinctCache;
use Chamilo\Libraries\Storage\Cache\DataClassResultCache;
use Chamilo\Libraries\Storage\Cache\DataClassResultSetCache;
use Chamilo\Libraries\Storage\Cache\RecordCache;
use Chamilo\Libraries\Storage\Cache\RecordResultCache;
use Chamilo\Libraries\Storage\Cache\RecordResultSetCache;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Exception\NoRecordException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 * General and basic DataManager, providing basic functionality for all other DataManager objects
 *
 * @author Hans De Bisschop
 * @package common.libraries
 */
class DataManager
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    // Storage access layer types
    const TYPE_DOCTRINE = 'Doctrine';
    const TYPE_MDB2 = 'Mdb2';

    // Storage unit actions
    const ALTER_STORAGE_UNIT_ADD = 1;
    const ALTER_STORAGE_UNIT_CHANGE = 2;
    const ALTER_STORAGE_UNIT_DROP = 3;
    const ALTER_STORAGE_UNIT_DROP_PRIMARY_KEY = 4;
    const ALTER_STORAGE_UNIT_ADD_PRIMARY_KEY = 5;
    const ALTER_STORAGE_UNIT_DROP_INDEX = 6;
    const ALTER_STORAGE_UNIT_ADD_INDEX = 7;
    const ALTER_STORAGE_UNIT_ADD_UNIQUE = 8;

    /**
     * Instance of this class for the singleton pattern
     *
     * @var \libraries\storage\data_manager\DataManager
     */
    public static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data manager.
     * The configuration determines which
     * data manager class is to be instantiated
     *
     * @return \libraries\storage\DoctrineDatabase
     */
    public static function get_instance()
    {
        $type = static :: get_type();

        if (! isset(self :: $instance[$type]))
        {
            $class = '\Chamilo\Libraries\Storage\DataManager\\' . $type . '\Database';
            self :: $instance[$type] = new $class();
        }
        return self :: $instance[$type];
    }

    /**
     * Set the instance of the DataManager
     *
     * @param $instance \libraries\storage\data_manager\DataManager
     */
    public static function set_instance($instance)
    {
        self :: $instance[\Chamilo\Libraries\Architecture\ClassnameUtilities :: getInstance()->getNamespaceFromObject(
            $instance)] = $instance;
    }

    /**
     * Gets the type of DataManager to be instantiated, by default configured in the main Chamilo configuration file
     *
     * @return string
     */
    public static function get_type()
    {
        return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database :: STORAGE_TYPE;
    }

    /**
     * Write an instance of a DataClass object to the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     * @return boolean
     */
    public static function create(DataClass $object)
    {
        if (! self :: get_instance()->create($object))
        {
            return false;
        }
        return DataClassResultCache :: add($object);
    }

    public static function create_record($class_name, $record)
    {
        return self :: get_instance()->create_record($class_name, $record);
    }

    /**
     * Retrieve an instance of a DataClass object from the storage layer by a set of parameters
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassRetrieveParameters
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public static function retrieve($class, $parameters = null)
    {
        if (! $parameters instanceof DataClassRetrieveParameters)
        {
            $parameters = DataClassRetrieveParameters :: generate($parameters);
        }

        $composite_class = CompositeDataClass :: class_name();

        if (is_subclass_of($class, $composite_class) && get_parent_class($class) !== $composite_class)
        {
            $base_class = $class :: parent_class_name();
        }
        else
        {
            $base_class = $class;
        }

        if (! DataClassCache :: exists($base_class, $parameters))
        {
            try
            {
                $base = (is_subclass_of($class, $composite_class) ? $composite_class : DataClass :: class_name());
                $record = self :: process_record(self :: get_instance()->retrieve($class, $parameters));

                DataClassResultCache :: add($base :: factory($class, $record), $parameters);
            }
            catch (DataClassNoResultException $exception)
            {
                DataClassResultCache :: no_result($exception);
            }
        }

        return DataClassCache :: get($base_class, $parameters);
    }

    public static function record($class, $parameters = null)
    {
        if (! $parameters instanceof RecordRetrieveParameters)
        {
            $parameters = RecordRetrieveParameters :: generate($parameters);
        }
        if (! RecordCache :: exists($parameters))
        {
            try
            {
                $record = self :: process_record(self :: get_instance()->record($class, $parameters));
                RecordResultCache :: add($record, $parameters);
            }
            catch (NoRecordException $exception)
            {
                RecordResultCache :: no_result($exception);
            }
        }
        return RecordCache :: get($parameters);
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
    public static function retrieve_by_id($class, $id)
    {
        // if (! is_numeric($id))
        // {
        // throw new \InvalidArgumentException(
        // Translation :: get('NoValidIdentifier', array('CLASS' => $class, 'ID' => $id)));
        // }
        return self :: retrieve(
            $class,
            new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($class :: class_name(), $class :: PROPERTY_ID),
                    new StaticConditionVariable($id))));
    }

    /**
     * Retrieve a ResultSet of DataClass object instances from the storage layer
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassRetrievesParameters
     * @return \libraries\storage\ResultSet
     */
    public static function retrieves($class, $parameters = null)
    {
        if (! $parameters instanceof DataClassRetrievesParameters)
        {
            $parameters = DataClassRetrievesParameters :: generate($parameters);
        }

        if (! DataClassResultSetCache :: exists($class, $parameters))
        {
            DataClassResultSetCache :: add(self :: get_instance()->retrieves($class, $parameters), $parameters);
        }

        $result_set = DataClassResultSetCache :: get($class, $parameters);
        $result_set->reset();
        return $result_set;
    }

    public static function records($class, $parameters = null)
    {
        if (! $parameters instanceof RecordRetrievesParameters)
        {
            $parameters = RecordRetrievesParameters :: generate($parameters);
        }

        if (! RecordResultSetCache :: exists($parameters))
        {
            RecordResultSetCache :: add(self :: get_instance()->records($class, $parameters), $parameters);
        }

        $result_set = RecordResultSetCache :: get($parameters);
        $result_set->reset();

        return $result_set;
    }

    /**
     * Retrieve all distinct values of a specific DataClass' property from the storage layer
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return multitype:string
     */
    public static function distinct($class, $parameters)
    {
        if (! $parameters instanceof DataClassDistinctParameters)
        {
            $parameters = DataClassDistinctParameters :: generate($parameters);
        }

        if (! DataClassDistinctCache :: exists($class, $parameters))
        {
            DataClassDistinctCache :: add($class, $parameters, self :: get_instance()->distinct($class, $parameters));
        }
        return DataClassDistinctCache :: get($class, $parameters);
    }

    /**
     * Update an instance of a DataClass object in the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     * @return boolean
     */
    public static function update(DataClass $object)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($object :: class_name(), $object :: PROPERTY_ID),
            new StaticConditionVariable($object->get_id()));
        return self :: get_instance()->update($object, $condition);
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
            $properties_class = $properties;
        }
        else
        {
            $properties_class = new DataClassProperties($properties);
        }

        if (! self :: get_instance()->updates($class, $properties_class, $condition, $offset, $count, $order_by))
        {
            return false;
        }

        return DataClassCache :: truncate($class);
    }

    /**
     * Delete an instance of a DataClass object from the storage layer
     *
     * @param $object \Chamilo\Libraries\Storage\DataClass\DataClass
     * @return boolean
     */
    public static function delete(DataClass $object)
    {
        $class_name = ($object instanceof CompositeDataClass ? $object :: parent_class_name() : $object :: class_name());

        $condition = new EqualityCondition(
            new PropertyConditionVariable($class_name, $class_name :: PROPERTY_ID),
            new StaticConditionVariable($object->get_id()));

        if (! self :: get_instance()->delete($class_name, $condition))
        {
            return false;
        }

        if ($object instanceof CompositeDataClass && $object :: is_extended())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($object :: class_name(), $object :: PROPERTY_ID),
                new StaticConditionVariable($object->get_id()));
            if (! self :: get_instance()->delete($object :: class_name(), $condition))
            {
                return false;
            }
        }

        return DataClassCache :: truncate($class_name);
        // return DataClassResultCache :: delete($object);
    }

    /**
     * Deletes any given number of instance of the DataClass object from the storage layer
     *
     * @param $class string
     * @param $condition \libraries\storage\Condition
     * @return boolean
     */
    public static function deletes($class, Condition $condition)
    {
        if (! self :: get_instance()->delete($class, $condition))
        {
            return false;
        }
        return DataClassCache :: truncate($class);
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassCountParameters
     * @return int
     */
    public static function count($class, $parameters = null)
    {
        if (! $parameters instanceof DataClassCountParameters)
        {
            $parameters = DataClassCountParameters :: generate($parameters);
        }

        if (! DataClassCountCache :: exists($class, $parameters))
        {
            DataClassCountCache :: add($class, $parameters, self :: get_instance()->count($class, $parameters));
        }
        return DataClassCountCache :: get($class, $parameters);
    }

    /**
     * Count the number of instances of a DataClass object in the storage layer, based on a specific property ad grouped
     * by another property in the storage layer
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassCountGroupedParameters
     * @return multitype:int
     */
    public static function count_grouped($class, $parameters)
    {
        if (! $parameters instanceof DataClassCountGroupedParameters)
        {
            $parameters = DataClassCountGroupedParameters :: generate($parameters);
        }

        if (! DataClassCountGroupedCache :: exists($class, $parameters))
        {
            DataClassCountGroupedCache :: add(
                $class,
                $parameters,
                self :: get_instance()->count_grouped($class, $parameters));
        }
        return DataClassCountGroupedCache :: get($class, $parameters);
    }

    /**
     * Count the number of distinct values of a specific property of the DataClass in the storage layer
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassCountDistinctParameters
     * @return int
     */
    public static function count_distinct($class, $parameters)
    {
        if (! $parameters instanceof DataClassCountDistinctParameters)
        {
            $parameters = DataClassCountDistinctParameters :: generate($parameters);
        }

        if (! DataClassCountCache :: exists($class, $parameters))
        {
            DataClassCountCache :: add($class, $parameters, self :: get_instance()->count_distinct($class, $parameters));
        }
        return DataClassCountCache :: get($class, $parameters);
    }

    /**
     * Get the highest value of a specific property of a DataClass in the storage layer
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return int
     */
    public static function retrieve_maximum_value($class, $property, Condition $condition = null)
    {
        return self :: get_instance()->retrieve_maximum_value($class, $property, $condition);
    }

    /**
     * Return the next value - based on the highest value - of a specific property of a DataClass in the storage layer
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return int
     */
    public static function retrieve_next_value($class, $property, Condition $condition = null)
    {
        return static :: retrieve_maximum_value($class, $property, $condition) + 1;
    }

    /**
     * Create a storage unit in the storage layer
     *
     * @param $name string
     * @param $properties multitype:mixed
     * @param $indexes multitype:mixed
     * @return boolean
     */
    public static function create_storage_unit($name, $properties, $indexes)
    {
        return self :: get_instance()->create_storage_unit($name, $properties, $indexes);
    }

    /**
     * Determine whether a storage unit exists in the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public static function storage_unit_exists($name)
    {
        return self :: get_instance()->storage_unit_exists($name);
    }

    /**
     * Drop a storage unit from the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public static function drop_storage_unit($name)
    {
        return self :: get_instance()->drop_storage_unit($name);
    }

    /**
     * Rename a storage unit
     *
     * @param string $old_name
     * @param string $new_name
     */
    public static function rename_storage_unit($old_name, $new_name)
    {
        return self :: get_instance()->rename_storage_unit($old_name, $new_name);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param multitype:mixed $attributes
     * @return boolean
     */
    public static function alter_storage_unit($type, $table_name, $property, $attributes = array())
    {
        return self :: get_instance()->alter_storage_unit($type, $table_name, $property, $attributes);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $name
     * @param multitype:string $columns
     * @return boolean
     */
    public static function alter_storage_unit_index($type, $table_name, $name = null, $columns = array())
    {
        return self :: get_instance()->alter_storage_unit_index($type, $table_name, $name, $columns);
    }

    /**
     * Truncate a storage unit in the storage layer and optionally optimize it afterwards
     *
     * @param $name string
     * @param $optimize boolean
     * @return boolean
     */
    public static function truncate_storage_unit($name, $optimize = true)
    {
        if (! self :: get_instance()->truncate_storage_unit($name))
        {
            return false;
        }

        if ($optimize && ! static :: optimize_storage_unit($name))
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
    public static function optimize_storage_unit($name)
    {
        return self :: get_instance()->optimize_storage_unit($name);
    }

    /**
     * Get the alias of a storage unit in the storage layer
     *
     * @param $storage_unit_name string
     * @return string
     */
    public static function get_alias($storage_unit_name)
    {
        return self :: get_instance()->get_alias($storage_unit_name);
    }

    /**
     * Retrieve the additional properties for a specific CompositeDataClass instance
     *
     * @param $object \libraries\storage\CompositeDataClass
     * @return multitype:string
     */
    public static function retrieve_composite_data_class_additional_properties(CompositeDataClass $object)
    {
        return self :: get_instance()->retrieve_composite_data_class_additional_properties($object);
    }

    /**
     * Executes the function within the context of a transaction.
     */
    public static function transactional($function)
    {
        return self :: get_instance()->transactional($function);
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
    public static function change_display_orders_by_mapping_array($class_name, $display_order_property,
        $display_order_mapping = array(), $display_order_condition = null)
    {
        foreach ($display_order_mapping as $old_display_order => $new_display_order)
        {
            if (! self :: move_display_orders($class_name, $old_display_order, $new_display_order))
            {
                return false;
            }

            $display_order_property_variable = new PropertyConditionVariable($class_name, $display_order_property);

            $properties = new DataClassProperties(array());

            $properties->add(
                new DataClassProperty($display_order_property_variable, new StaticConditionVariable($new_display_order)));

            $conditions = array();

            if ($display_order_condition)
            {
                $conditions[] = $display_order_condition;
            }

            $conditions[] = new EqualityCondition(
                $display_order_property_variable,
                new StaticConditionVariable($old_display_order));

            $condition = new AndCondition($conditions);

            return self :: updates($class_name, $properties, $condition);
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
    public static function move_display_orders($class_name, $display_order_property, $start = 1, $end = null,
        $display_order_condition = null)
    {
        if ($start == $end)
        {
            return false;
        }

        $display_order_property_variable = new PropertyConditionVariable($class_name, $display_order_property);

        $conditions = array();

        if (is_null($end) || $start < $end)
        {
            $start_operator = InequalityCondition :: GREATER_THAN;

            $direction = - 1;
        }

        if (! is_null($end))
        {
            if ($start < $end)
            {
                $end_operator = InequalityCondition :: LESS_THAN_OR_EQUAL;
            }
            else
            {
                $start_operator = InequalityCondition :: LESS_THAN;
                $end_operator = InequalityCondition :: GREATER_THAN_OR_EQUAL;

                $direction = 1;
            }
        }

        $start_variable = new StaticConditionVariable($start);

        $conditions[] = new InequalityCondition($display_order_property_variable, $start_operator, $start_variable);

        if (! is_null($end))
        {
            $end_variable = new StaticConditionVariable($end);

            $conditions[] = new InequalityCondition($display_order_property_variable, $end_operator, $end_variable);
        }

        if ($display_order_condition)
        {
            $conditions[] = $display_order_condition;
        }

        $condition = new AndCondition($conditions);

        $update_variable = new OperationConditionVariable(
            $display_order_property_variable,
            OperationConditionVariable :: ADDITION,
            new StaticConditionVariable($direction));

        $properties = new DataClassProperties(array());

        $properties->add(new DataClassProperty($display_order_property_variable, $update_variable));

        return self :: updates($class_name, $properties, $condition);
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities :: getInstance()->getNamespaceParent(static :: context());
    }
}
