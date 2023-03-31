<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataManager\Mdb2\Condition\ConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Mdb2\ResultSet\DataClassResultSet;
use Chamilo\Libraries\Storage\DataManager\Mdb2\Variable\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use MDB2;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 * @package common.libraries
 */
class Database
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    
    // Constants
    const STORAGE_TYPE = 'mdb2';
    const ALIAS_MAX_SORT = 'max_sort';

    /**
     *
     * @var MDB2
     */
    private $connection;

    /**
     * Static error log so we don't open the error log every time an error is written
     * 
     * @var FileLogger
     */
    private static $error_log;

    /**
     * Used for debug
     * 
     * @var int
     */
    private static $query_counter;

    public function __call($name, $arguments)
    {
        $class = static::context() . '\\' .
             ClassnameUtilities::getInstance()->getPackageNameFromNamespace(static::context(), true) . 'DataManager';
        return call_user_func_array($class . '::' . $name, $arguments);
    }

    /**
     * Constructor
     */
    public function __construct($aliases = array())
    {
        $this->connection = Connection::getInstance()->get_connection();
        $this->connection->setOption('debug_handler', array(get_class($this), 'debug'));
        
        $initializer = 'initialize_' . $this->connection->phptype;
        if (method_exists($this, $initializer))
        {
            $this->$initializer();
        }
    }

    public function initialize_mysqli()
    {
        // $this->connection->exec("SET sql_mode='TRADITIONAL'");
    }

    /**
     *
     * @return MDB2
     */
    public function get_connection()
    {
        return $this->connection;
    }

    /**
     * Debug function Uncomment the lines if you want to debug
     */
    public static function debug()
    {
        $args = func_get_args();
        // Do something with the arguments
        if ($args[1] == 'query' || $args[1] == 'prepare')
        {
            // echo '<pre>';
            // echo($args[2]);
            // echo self :: $query_counter;
            // echo '</pre>';
            // self :: $query_counter++;
        }
    }

    /*
     * A workaround to the fact that MDB2 hardcodes a default PEAR_Error error mode of PEAR_ERROR_RETURN
     */
    public function mdb2_error_handling($err)
    {
        // I don't care about performance, this should not happen more than once
        // per request
        if (! self::$error_log)
        {
            $logfile = Path::getInstance()->getLogPath() . '/mdb2_errors.log';
            self::$error_log = new FileLogger($logfile, true);
        }
        
        $message = "[Message: {$err->getMessage()}] [Information: {$err->getUserInfo()}]";
        self::$error_log->log_message($message);
        
        // throw new Exception("[Message: ".$err->getMessage()."] [Information:
        // ".$err->getUserInfo()."]", $err->getCode());
    }

    /**
     * Escapes a column name in accordance with the database type.
     * 
     * @param $name string The column name.
     * @param $table_alias String The alias of the table the coloumn is in
     * @return string The escaped column name.
     */
    public static function escape_column_name($name, $table_alias = null)
    {
        if (! empty($table_alias))
        {
            return $table_alias . '.' . $name;
        }
        else
        {
            return $name;
        }
    }

    public function storage_unit_exists($name)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        $tables = $manager->listTables();
        
        if (MDB2::isError($tables))
        {
            $this->mdb2_error_handling($tables);
            return false;
        }
        return in_array($name, $tables);
    }

    /**
     * Creates a storage unit in the system
     * 
     * @param $name String the table name
     * @param $properties Array the table properties
     * @param $indexes Array the table indexes
     * @return true if the storage unit is succesfully created
     */
    public function create_storage_unit($name, $properties, $indexes)
    {
        $check_name = $this->prefix . $name;
        
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        // If table allready exists -> drop it
        // @todo This should change: no automatic table drop but warning to user
        $tables = $manager->listTables();
        
        if (in_array($check_name, $tables))
        {
            $manager->dropTable($name);
        }
        $options['charset'] = 'utf8';
        $options['collate'] = 'utf8_unicode_ci';
        
        if (strncmp($this->connection->phptype, 'mysql', 5) == 0)
        {
            $options['type'] = 'innodb';
        }
        
        $result = $manager->createTable($name, $properties, $options);
        
        $constraint_table_alias = $this->get_constraint_name($name);
        
        if (! MDB2::isError($result))
        {
            foreach ($indexes as $index_name => $index_info)
            {
                if ($index_info['type'] == 'primary')
                {
                    $index_info['primary'] = 1;
                    $primary_result = $manager->createConstraint(
                        $name, 
                        StorageAliasGenerator::getInstance()->get_constraint_name(
                            $name, 
                            $index_name, 
                            StorageAliasGenerator::TYPE_CONSTRAINT), 
                        $index_info);
                    if (MDB2::isError($primary_result))
                    {
                        $this->mdb2_error_handling($primary_result);
                        return false;
                    }
                }
                elseif ($index_info['type'] == 'unique')
                {
                    $index_info['unique'] = 1;
                    $unique_result = $manager->createConstraint(
                        $name, 
                        StorageAliasGenerator::getInstance()->get_constraint_name(
                            $name, 
                            $index_name, 
                            StorageAliasGenerator::TYPE_CONSTRAINT), 
                        $index_info);
                    if (MDB2::isError($unique_result))
                    {
                        $this->mdb2_error_handling($unique_result);
                        return false;
                    }
                }
                else
                {
                    $index_result = $manager->createIndex(
                        $name, 
                        StorageAliasGenerator::getInstance()->get_constraint_name(
                            $name, 
                            $index_name, 
                            StorageAliasGenerator::TYPE_CONSTRAINT), 
                        $index_info);
                    if (MDB2::isError($index_result))
                    {
                        $this->mdb2_error_handling($index_result);
                        return false;
                    }
                }
            }
            return true;
        }
        else
        {
            $this->mdb2_error_handling($result);
            return false;
        }
    }

    /**
     *
     * @return True if creation is successfull or false
     */
    public function create($object, $auto_id = true)
    {
        if ($object instanceof CompositeDataClass)
        {
            $parent_class = $object::parent_class_name();
            $object_table = $parent_class::get_table_name();
        }
        else
        {
            $object_table = $object->get_table_name();
        }
        
        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$key] = $value;
        }
        
        if ($auto_id && in_array('id', $object->get_default_property_names()))
        {
            $props['id'] = null;
        }
        
        $this->connection->loadModule('Extended');
        $result = $this->connection->extended->autoExecute($object_table, $props, MDB2_AUTOQUERY_INSERT);
        
        if (MDB2::isError($result))
        {
            $this->mdb2_error_handling($result);
            return false;
        }
        
        if ($auto_id && in_array('id', $object->get_default_property_names()))
        {
            $object->set_id($this->connection->extended->getAfterID($props['id'], $object_table));
        }
        
        if ($object instanceof CompositeDataClass && $object::is_extended())
        {
            $props = array();
            foreach ($object->get_additional_properties() as $key => $value)
            {
                $props[$key] = $value;
            }
            $props['id'] = $object->get_id();
            
            $result = $this->connection->extended->autoExecute($object->get_table_name(), $props, MDB2_AUTOQUERY_INSERT);
            
            if (MDB2::isError($result))
            {
                $this->mdb2_error_handling($result);
                return false;
            }
        }
        
        return true;
    }

    /**
     * Update functionality (can only be used when the storage unit has an ID property)
     * 
     * @param $object DataClass
     * @param $condition Condition
     * @return boolean
     */
    public function update($object, $condition = false)
    {
        if ($object instanceof CompositeDataClass)
        {
            $parent_class = $object::parent_class_name();
            $object_table = $parent_class::get_table_name();
        }
        else
        {
            $object_table = $object->get_table_name();
        }
        
        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$key] = $value;
        }
        
        if ($condition)
        {
            $condition = ConditionTranslator::render($condition);
        }
        else
        {
            throw new Exception('Cannot update records without a condition');
        }
        
        $this->connection->loadModule('Extended');
        $result = $this->connection->extended->autoExecute($object_table, $props, MDB2_AUTOQUERY_UPDATE, $condition);
        if (MDB2::isError($result))
        {
            $this->mdb2_error_handling($result);
            return false;
        }
        
        if ($object instanceof CompositeDataClass && $object::is_extended())
        {
            $props = array();
            foreach ($object->get_additional_properties() as $key => $value)
            {
                $props[$key] = $value;
            }
            
            $condition = new EqualityCondition(
                new PropertyConditionVariable($object::class_name(), $object::PROPERTY_ID), 
                new StaticConditionVariable($object->get_id()));
            
            $condition = ConditionTranslator::render($condition);
            
            $result = $this->connection->extended->autoExecute(
                $object->get_table_name(), 
                $props, 
                MDB2_AUTOQUERY_UPDATE, 
                $condition);
            
            if (MDB2::isError($result))
            {
                $this->mdb2_error_handling($result);
                return false;
            }
        }
        
        return true;
    }

    /**
     *
     * @param $table_name string
     * @param $properties multitype:string
     * @param $condition \libraries\storage\Condition
     * @param $offset int
     * @param $max_objects int
     * @param $order_by multitype:\common\libraries\ObjectTableOrder
     * @return boolean
     * @deprecated Use updates() in implementations, use the PackageDataManager :: updates() in applications
     */
    public function update_objects($table_name, $properties = array(), $condition, $offset = null, $max_objects = null, 
        $order_by = array())
    {
        if (count($properties) > 0)
        {
            $table_name_alias = $this->get_alias($table_name);
            
            $query = 'UPDATE ' . $table_name . ' AS ' . $table_name_alias . ' SET ';
            
            $updates = array();
            
            foreach ($properties as $column => $property)
            {
                $updates[] = $column . '=' . $property;
            }
            
            $query .= implode(", ", $updates);
            
            if (isset($condition))
            {
                $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($table_name));
            }
            
            $orders = array();
            
            if (is_null($order_by))
            {
                $order_by = array();
            }
            elseif (! is_array($order_by))
            {
                $order_by = array($order_by);
            }
            
            foreach ($order_by as $order)
            {
                if ($order)
                {
                    $orders[] = self::escape_column_name(
                        $order->get_property(), 
                        ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' .
                         ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                }
            }
            if (count($orders))
            {
                $query .= ' ORDER BY ' . implode(', ', $orders);
            }
            
            if ($max_objects > 0)
            {
                $query .= ' LIMIT ' . $max_objects;
            }
            
            $res = $this->connection->exec($query);
            
            if (MDB2::isError($res))
            {
                $this->mdb2_error_handling($res);
                return false;
            }
        }
        return true;
    }

    public function updates($class, $properties, $condition, $offset = null, $count = null, $order_by = array())
    {
        if ($properties instanceof DataClassProperties)
        {
            $old_style_properties = array();
            
            foreach ($properties->get() as $data_class_property)
            {
                $old_style_properties[ConditionVariableTranslator::render($data_class_property->get_property())] = ConditionVariableTranslator::render(
                    $data_class_property->get_value());
            }
            
            $properties = $old_style_properties;
        }
        
        return $this->update_objects($class::get_table_name(), $properties, $condition, $offset, $count, $order_by);
    }

    /**
     * Deletes an object from a table with a given condition
     * 
     * @param $table_name String
     * @param $condition Condition
     * @return true if deletion is successfull
     * @deprecated Use new_delete() in implementations, use the PackageDataManager :: delete() in applications
     */
    public function delete($class, $condition)
    {
        $table_name = $class::get_table_name();
        $alias = $this->get_alias($table_name);
        $query = 'DELETE ' . $alias . ' FROM ' . $table_name . ' AS ' . $alias;
        
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $table_name);
        }
        
        $res = $this->connection->exec($query);
        
        if (MDB2::isError($res))
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        return true;
    }

    /**
     * Drop a given storage unit
     * 
     * @param $table_name String
     * @return boolean
     */
    public function drop_storage_unit($table_name)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        
        $result = $manager->dropTable($table_name);
        
        if (MDB2::isError($result))
        {
            $this->mdb2_error_handling($result);
            return false;
        }
        else
        {
            return true;
        }
    }

    public function rename_storage_unit($old_name, $new_name)
    {
        $query = 'ALTER TABLE ' . $old_name . ' RENAME TO ' . $new_name;
        
        $res = $this->connection->exec($query);
        
        if (MDB2::isError($res))
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param multitype:mixed $attributes
     * @return boolean
     */
    public function alter_storage_unit($type, $table_name, $property, $attributes = array())
    {
        $column_name = $property;
        $changes = array();
        if ($type == DataManager::ALTER_STORAGE_UNIT_DROP)
        {
            $changes['remove'] = array($column_name => array());
        }
        else
        {
            
            if ($type == DataManager::ALTER_STORAGE_UNIT_CHANGE)
            {
                if (isset($attributes['name']))
                {
                    $new_column_name = $attributes['name'];
                    $changes['rename'] = array($column_name => array('name' => $new_column_name));
                    $column_name = $new_column_name;
                }
                $changes['change'] = array($column_name => array('definition' => $attributes));
            }
            elseif ($type == DataManager::ALTER_STORAGE_UNIT_ADD)
            {
                $changes['add'] = array($column_name => $attributes);
            }
        }
        
        // $res = $this->connection->exec($query);
        
        $this->connection->loadModule('Manager');
        $res = $this->connection->manager->alterTable($table_name, $changes, false);
        if (MDB2::isError($res))
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        return true;
    }

    /**
     * Counts the objects in a storage unit with a given condition
     * 
     * @param $class string
     * @param $parameters \libraries\storage\DataClassCountParameters
     */
    public function count($class, $parameters)
    {
        $table_name = $class::get_table_name();
        $query = 'SELECT COUNT(*) FROM ' . $table_name . ' AS ' . $this->get_alias($table_name);
        
        $condition = $parameters->get_condition();
        
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($table_name));
        }
        
        $res = $this->query($query);
        
        if (MDB2::isError($res))
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        else
        {
            $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
            $res->free();
            // dump($res);
            return $record[0];
        }
    }

    /**
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassCountGroupedParameters
     * @return multitype:int
     */
    public function count_grouped($class, $parameters)
    {
        $table_name = $class::get_table_name();
        $group = $parameters->get_property();
        $count = $parameters->get_property();
        $condition = $parameters->get_condition();
        
        $query = 'SELECT ' . self::escape_column_name($group, $this->get_alias($table_name)) . ', COUNT(' .
             self::escape_column_name($count, $this->get_alias($table_name)) . ') FROM ' . $table_name . ' AS ' .
             $this->get_alias($table_name);
        
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($table_name));
        }
        
        $query .= ' GROUP BY' . self::escape_column_name($group, $this->get_alias($table_name));
        
        $res = $this->query($query);
        
        if (MDB2::isError($res))
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        else
        {
            $counts = array();
            while ($record = $res->fetchRow(MDB2_FETCHMODE_ORDERED))
            {
                $counts[$record[0]] = $record[1];
            }
            $res->free();
            
            return $counts;
        }
    }

    /**
     * Retrieves the objects of a given table
     * 
     * @param $table_name String
     * @param $classname String The name of the class where the object has to be mapped to
     * @param $condition Condition the condition
     * @param $offset Int the starting offset
     * @param $max_objects Int the max amount of objects to be retrieved
     * @param $order_by Array(String) the list of column names that the objects have to be ordered by
     * @param $resultset String - Optional, the resultset to map the items to
     * @return ResultSet
     * @deprecated Use retrieves() in implementations, use the PackageDataManager :: retrieves() in applications
     */
    public function retrieve_objects($table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array(), 
        $class_name = null)
    {
        $query = 'SELECT * FROM ' . $table_name . ' AS ' . $this->get_alias($table_name);
        // echo $query . '<br />';
        return $this->retrieve_object_set(
            $query, 
            $table_name, 
            $condition, 
            $offset, 
            $max_objects, 
            $order_by, 
            $class_name);
    }

    /**
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassResultSetParameters
     * @return \libraries\storage\ResultSet
     */
    public function retrieves($class, DataClassRetrievesParameters $parameters)
    {
        return $this->retrieve_objects(
            $class::get_table_name(), 
            $parameters->get_condition(), 
            $parameters->get_offset(), 
            $parameters->get_count(), 
            $parameters->get_order_by(), 
            $class);
    }

    public function retrieve_result($query, $table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array())
    {
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($table_name));
        }
        
        $orders = array();
        
        // print_r('<strong>Statement</strong><br />' . $query . '<br /><br
        // /><br />');
        // dump($order_by);
        
        if (is_null($order_by))
        {
            $order_by = array();
        }
        elseif (! is_array($order_by))
        {
            $order_by = array($order_by);
        }
        
        foreach ($order_by as $order)
        {
            if ($order)
            {
                $orders[] = self::escape_column_name(
                    $order->get_property(), 
                    ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' .
                     ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
            }
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        if ($max_objects < 0)
        {
            $max_objects = null;
        }
        
        $this->set_limit(intval($max_objects), intval($offset));
        
        return $this->query($query);
    }

    public function retrieve_object_set($query, $table_name, $condition = null, $offset = null, $max_objects = null, 
        $order_by = array(), $class_name = null)
    {
        $res = $this->retrieve_result($query, $table_name, $condition, $offset, $max_objects, $order_by);
        
        if (is_null($class_name))
        {
            $called_class = get_called_class();
            $namespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname($called_class);
            
            $class_name = $namespace . '\\' .
                 (string) StringUtilities::getInstance()->createString($table_name)->upperCamelize();
        }
        
        if (MDB2::isError($res))
        {
            $this->mdb2_error_handling($res);
            return null;
        }
        return new DataClassResultSet($this, $res, $class_name);
    }

    /**
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return int
     */
    public function retrieve_maximum_value($class, $column, $condition = null)
    {
        $table_name = $class::get_table_name();
        
        $query = 'SELECT MAX(' . $column . ') as ' . self::ALIAS_MAX_SORT . ' FROM ' . $table_name . ' AS ' .
             $this->get_alias($table_name);
        
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($table_name));
        }
        
        $res = $this->query($query);
        if ($res->numRows() >= 1)
        {
            $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
            $res->free();
            return $record[0];
        }
        else
        {
            $res->free();
            return 0;
        }
    }

    public function truncate_storage_unit($table_name, $optimize = true)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        $result = $manager->truncateTable($table_name);
        if (! MDB2::isError($result))
        {
            if ($optimize)
            {
                return $this->optimize_storage_unit($table_name);
            }
            else
            {
                return true;
            }
        }
        else
        {
            $this->mdb2_error_handling($result);
            return false;
        }
    }

    public function optimize_storage_unit($table_name)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        if ($manager->vacuum($table_name))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function retrieve_row($query, $table_name, $condition = null, $order_by = array())
    {
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($table_name));
        }
        
        $orders = array();
        
        foreach ($order_by as $order)
        {
            if ($order)
            {
                $orders[] = self::escape_column_name(
                    $order->get_property(), 
                    ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' .
                     ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
            }
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        
        $this->set_limit(1, 0);
        $res = $this->query($query);
        
        if (! MDB2::isError($res))
        {
            $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
            $res->free();
        }
        else
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        
        if (MDB2::isError($record))
        {
            $this->mdb2_error_handling($record);
            return false;
        }
        if (is_null($record))
        {
            return false;
        }
        
        foreach ($record as &$field)
        {
            if (is_resource($field))
            {
                $data = '';
                while (! feof($field))
                    $data .= fread($field, 1024);
                $field = $data;
            }
        }
        
        if ($record)
        {
            return $record;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param $class string
     * @param $parameters \libraries\storage\DataClassResultParameters
     * @throws DataClassNoResultException
     * @return multitype:string
     */
    public function retrieve($class, $parameters = null)
    {
        $record = $this->retrieve_composite_record($class, $parameters);
        
        if (! is_array($record) || count($record) == 0)
        {
            throw new DataClassNoResultException($class, $parameters);
        }
        else
        {
            return $record;
        }
    }

    /**
     *
     * @param $class unknown_type
     * @param $parameters unknown_type
     */
    public function retrieve_composite_record($class, $parameters)
    {
        $condition = $parameters->get_condition();
        $order_by = $parameters->get_order_by();
        
        $table_name = $class::get_table_name();
        $table_alias = $this->get_alias($table_name);
        
        $query = 'SELECT * FROM ' . $table_name . ' AS ' . $table_alias;
        
        if (is_subclass_of($class, CompositeDataClass::class_name()))
        {
            $type_query = 'SELECT ' . self::escape_column_name(CompositeDataClass::PROPERTY_TYPE, $table_alias) .
                 ' FROM ' . $table_name . ' AS ' . $table_alias;
            $record = $this->retrieve_row($type_query, $table_name, $condition);
            $type = $record[CompositeDataClass::PROPERTY_TYPE];
            $type_alias = $this->get_alias($type::get_table_name());
            
            if ($type::is_extended())
            {
                $query .= ' JOIN ' . $type::get_table_name() . ' AS ' . $type_alias . ' ON ' .
                     self::escape_column_name(DataClass::PROPERTY_ID, $table_alias) . '=' .
                     self::escape_column_name(CompositeDataClass::PROPERTY_ID, $type_alias);
            }
        }
        
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($class::get_table_name()));
        }
        
        $orders = array();
        
        foreach ($order_by as $order)
        {
            if ($order)
            {
                $orders[] = self::escape_column_name(
                    $order->get_property(), 
                    ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($class::get_table_name()))) . ' ' .
                     ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
            }
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        
        $this->set_limit(1, 0);
        
        $res = $this->query($query);
        if ($res && ! MDB2::isError($res))
        {
            $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
            $res->free();
        }
        else
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        
        if (MDB2::isError($record))
        {
            $this->mdb2_error_handling($record);
            return false;
        }
        if (is_null($record))
        {
            return false;
        }
        
        foreach ($record as &$field)
        {
            if (is_resource($field))
            {
                $data = '';
                while (! feof($field))
                    $data .= fread($field, 1024);
                $field = $data;
            }
        }
        
        if ($record)
        {
            return $record;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param $table_name string
     * @param $column_name string
     * @param $condition \libraries\storage\Condition
     * @return multitype:string
     * @deprecated Use distinct() in implementations, use the PackageDataManager :: distinct() in applications
     */
    public function retrieve_distinct($table_name, $column_name, $condition = null)
    {
        if ($column_name instanceof DataClassDistinctParameters)
        {
            $column_name = $column_name->get_property();
        }
        
        $query = 'SELECT DISTINCT(' . $column_name . ') FROM ' . $table_name . ' AS ' . $this->get_alias($table_name);
        
        if (isset($condition))
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition, $this->get_alias($table_name));
        }
        
        $res = $this->query($query);
        if (MDB2::isError($res))
        {
            $this->mdb2_error_handling($res);
            return false;
        }
        
        $distinct_elements = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $distinct_elements[] = $record[$column_name];
        }
        $res->free();
        return $distinct_elements;
    }

    /**
     *
     * @param $class string
     * @param $property string
     * @param $condition \libraries\storage\Condition
     * @return multitype:string
     */
    public function distinct($class, $property, $condition = null)
    {
        return $this->retrieve_distinct($class::get_table_name(), $property, $condition);
    }

    /**
     * Returns the alias of the table name
     * 
     * @param $table_name String
     * @return String the alias
     */
    public function get_alias($table_name)
    {
        return StorageAliasGenerator::getInstance()->get_table_alias($table_name);
    }

    public function get_constraint_name($name)
    {
        $possible_name = '';
        $parts = explode('_', $name);
        foreach ($parts as & $part)
        {
            $possible_name .= $part[0];
        }
        
        return $possible_name;
    }

    public static function quote($value, $type = null, $quote = true, $escape_wildcards = false)
    {
        return Connection::getInstance()->get_connection()->quote($value, $type, $quote, $escape_wildcards);
    }

    public function escape($text, $escape_wildcards = false)
    {
        return $this->connection->escape($text, $escape_wildcards);
    }

    public function query($query, $types = true, $result_class = true, $result_wrap_class = false)
    {
        $result = $this->connection->query($query, $types, $result_class, $result_wrap_class);
        if (MDB2::isError($result))
        {
            $this->mdb2_error_handling($result);
            return $result;
        }
        return $result;
    }

    /**
     * Delegate method for the exec function Exec is used when the query does not expect a resultset but only true /
     * false
     * 
     * @param $query String
     * @return boolean
     */
    public function exec($query)
    {
        $result = $this->connection->exec($query);
        if (MDB2::isError($result))
        {
            $this->mdb2_error_handling($result);
            return false;
        }
        return $result;
    }

    public function set_limit($limit, $offset = 0)
    {
        if ($offset && ! $limit)
            throw new Exception('Supplying an offset without a limit is not supported by MDB2!');
        
        $result = $this->connection->setLimit($limit, $offset);
        if (MDB2::isError($result))
        {
            $this->mdb2_error_handling($result);
            return false;
        }
    }

    /**
     * Locks the given table
     * 
     * @param $table_name type
     */
    public function write_lock_table($table_name)
    {
        $query = 'LOCK TABLES ' . $table_name . ' WRITE, ' . $table_name . ' AS ' . $this->get_alias($table_name) .
             ' WRITE ';
        $this->exec($query);
    }

    /**
     * Unlocks all locked tables
     */
    public function unlock_tables()
    {
        $query = 'UNLOCK TABLES';
        $this->exec($query);
    }

    public function retrieve_composite_data_class_additional_properties(CompositeDataClass $object)
    {
        if (! $object->is_extended())
        {
            return array();
        }
        $array = array_map(array($this, 'escape_column_name'), $object->get_additional_property_names());
        
        if (count($array) == 0)
        {
            $array = array("*");
        }
        
        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $object::get_table_name() . ' WHERE ' .
             $object::PROPERTY_ID . '=' . $this->quote($object->get_id());
        
        $this->set_limit(1, 0);
        $res = $this->query($query);
        $row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $properties = $object->get_additional_property_names();
        
        $additional_properties = array();
        if (count($properties) > 0)
        {
            foreach ($properties as $prop)
            {
                if (is_resource($row[$prop]))
                {
                    $data = '';
                    while (! feof($row[$prop]))
                        $data .= fread($row[$prop], 1024);
                    $row[$prop] = $data;
                }
                $additional_properties[$prop] = $row[$prop];
            }
        }
        
        $res->free();
        
        return $additional_properties;
    }

    /**
     * The transaction nesting level.
     * 
     * @var integer
     */
    private $_transactionNestingLevel = 0;

    /**
     * Flag that indicates whether the current transaction is marked for rollback only.
     * The flag is set when nested
     * transactions attempt to rollback, which poisons the enclosing transactions, forcing them to rollback as well. The
     * flag is unset as soons as an actual rollback occurs.
     * 
     * @var boolean
     */
    private $_isRollbackOnly = false;

    public function transactional($function)
    {
        if ($this->connection->supports('transactions') && $this->connection->setTransactionIsolation(
            'REPEATABLE READ', 
            array('wait' => 'WAIT', 'rw' => 'READ WRITE')))
        {
            ++ $this->_transactionNestingLevel;
            
            if ($this->_transactionNestingLevel == 1)
            {
                $this->connection->beginTransaction();
            }
        }
        else
        {
            return false;
        }
        
        $result = $function($this->connection);
        
        if ($this->_isRollbackOnly || MDB2::isError($result) || ! $result)
        {
            $result = false; // return false to signal an error.
            
            if ($this->connection->in_transaction)
            {
                if ($this->_transactionNestingLevel == 1)
                {
                    $this->connection->rollback();
                    
                    // Since a rollback was performed and we are at the top level
                    $this->_isRollbackOnly = false;
                }
                else
                {
                    // Alert the enclosing transaction that it must be rolled back
                    // because one of its nested transactions has failed.
                    $this->_isRollbackOnly = true;
                }
            }
        }
        else
        {
            if ($this->connection->in_transaction && $this->_transactionNestingLevel == 1)
            {
                $this->connection->commit();
            }
        }
        
        -- $this->_transactionNestingLevel;
        
        return $result;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }

    /**
     *
     * @param Condition $condition
     * @return string
     */
    public function translateCondition(Condition $condition = null)
    {
        return ConditionTranslator::render($condition);
    }
}
