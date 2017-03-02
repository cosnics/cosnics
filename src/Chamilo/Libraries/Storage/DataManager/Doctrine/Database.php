<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Condition\ConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\DataClassResultSet;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\RecordResultSet;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the service-based DataClassDatabase and StorageUnitDatabase
 */
class Database
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    // Constants
    const STORAGE_TYPE = 'Doctrine';
    const ALIAS_MAX_SORT = 'max_sort';

    /**
     *
     * @var \Doctrine\DBAL\Connection
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

    /**
     * Constructor
     */
    public function __construct($connection = null)
    {
        if (is_null($connection))
        {
            $this->connection = Connection::getInstance()->get_connection();
        }
        else
        {
            $this->connection = $connection;
        }
    }

    /**
     *
     * @return \Doctrine\DBAL\Connection
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
    public function error_handling($error)
    {
        if (!self::$error_log)
        {
            $logfile = Path::getInstance()->getLogPath() . '/doctrine_errors.log';
            self::$error_log = new FileLogger($logfile, true);
        }

        $message = "[Message: {$error->getMessage()}] [Information: { USER INFO GOES HERE}]";
        self::$error_log->log_message($message);
    }

    /**
     * Escapes a column name in accordance with the database type.
     *
     * @param $name string The column name.
     * @param $table_alias String The alias of the table the coloumn is in
     *
     * @return string The escaped column name.
     */
    public static function escape_column_name($name, $table_alias = null)
    {
        if (!empty($table_alias))
        {
            return $table_alias . '.' . $name;
        }
        else
        {
            return $name;
        }
    }

    /**
     *
     * @param string $name
     *
     * @return boolean
     */
    public function storage_unit_exists($name)
    {
        return $this->get_connection()->getSchemaManager()->tablesExist(array($name));
    }

    /**
     * Creates a storage unit in the system
     *
     * @param $name String the table name
     * @param $properties Array the table properties
     * @param $indexes Array the table indexes
     *
     * @return true if the storage unit is succesfully created
     */
    public function create_storage_unit($name, $properties, $indexes)
    {
        try
        {
            $table_name = $name;

            if ($this->get_connection()->getSchemaManager()->tablesExist(array($table_name)))
            {
                $this->drop_storage_unit($name);
            }

            $schema = new \Doctrine\DBAL\Schema\Schema();
            $table = $schema->createTable($table_name);

            foreach ($properties as $property => $attributes)
            {
                // $type = self :: parse_storage_unit_property_type($attributes);
                // $options = self :: parse_storage_unit_attributes($attributes);

                // $table->addColumn($property, $type, $options);

                switch ($attributes['type'])
                {
                    case 'text' :
                        if ($attributes['length'] && $attributes['length'] <= 255)
                        {
                            $attributes['type'] = 'string';
                        }
                        break;
                }

                $type = self::parse_storage_unit_property_type($attributes);
                $options = self::parse_storage_unit_attributes($attributes);

                $table->addColumn($property, $attributes['type'], $options);

                if ($options['autoincrement'] == true)
                {
                    $name = StorageAliasGenerator::getInstance()->get_constraint_name(
                        $table_name,
                        $name,
                        StorageAliasGenerator::TYPE_CONSTRAINT
                    );
                    $table->setPrimaryKey(array($property), $name);
                }
            }

            foreach ($indexes as $index => $attributes)
            {
                $name = StorageAliasGenerator::getInstance()->get_constraint_name(
                    $table_name,
                    $index,
                    StorageAliasGenerator::TYPE_CONSTRAINT
                );

                switch ($attributes['type'])
                {
                    case 'primary' :
                        $table->setPrimaryKey(array_keys($attributes['fields']), $name);
                        break;
                    case 'unique' :
                        $table->addUniqueIndex(array_keys($attributes['fields']), $name);
                        break;
                    default :
                        $table->addIndex(array_keys($attributes['fields']), $name);
                        break;
                }
            }

            foreach ($schema->toSql($this->get_connection()->getDatabasePlatform()) as $query)
            {
                $statement = $this->get_connection()->query($query);

                if ($statement instanceof \PDOException)
                {
                    $this->error_handling($statement);

                    return false;
                }
            }

            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }

    /**
     *
     * @param string[] $attributes
     *
     * @return string
     */
    public static function parse_storage_unit_property_type($attributes)
    {
        switch ($attributes['type'])
        {
            case 'text' :
                if ($attributes['length'] && $attributes['length'] <= 255)
                {
                    return 'string';
                }
                else
                {
                    return $attributes['type'];
                }
                break;
            case 'integer' :
                if (is_null($attributes['length']))
                {
                    return 'integer';
                }
                elseif ($attributes['length'] <= 2)
                {
                    return 'smallint';
                }
                elseif ($attributes['length'] <= 9)
                {
                    return 'integer';
                }
                else
                {
                    return 'bigint';
                }
            default :
                return $attributes['type'];
                break;
        }
    }

    /**
     *
     * @param string[] $attributes
     *
     * @return string[]
     */
    public static function parse_storage_unit_attributes($attributes)
    {
        $options = array();

        foreach ($attributes as $attribute => $value)
        {
            switch ($attribute)
            {
                case 'length' :
                    $options[$attribute] = (is_numeric($value) ? (int) $value : null);
                    break;
                case 'unsigned' :
                    $options[$attribute] = ($value == 1 ? true : false);
                    break;
                case 'fixed' :
                    if ($attributes['type'] != 'string')
                    {
                        $options[$attribute] = ($value == 1 ? true : false);
                    }
                    break;
                case 'notnull' :
                    $options[$attribute] = ($value == 1 ? true : false);
                    break;
                case 'default' :
                    $options[$attribute] = (!is_numeric($value) && empty($value) ? null : $value);
                    break;
                case 'autoincrement' :
                    $options[$attribute] = ($value == 'true' ? true : false);
                    break;
            }
        }

        if (!isset($options['notnull']))
        {
            $options['notnull'] = false;
        }

        return $options;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param boolean $auto_id
     *
     * @return boolean
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

        try
        {
            $result = $this->connection->insert($object_table, $props);

            if ($auto_id && in_array('id', $object->get_default_property_names()))
            {
                $object->set_id($this->connection->lastInsertId($object_table));
            }

            if ($object instanceof CompositeDataClass && $object::is_extended())
            {
                $props = array();
                foreach ($object->get_additional_properties() as $key => $value)
                {
                    $props[$key] = $value;
                }
                $props['id'] = $object->get_id();

                try
                {
                    $result = $this->connection->insert($object->get_table_name(), $props);
                }
                catch (\Exception $exception)
                {
                    $this->error_handling($exception);

                    return false;
                }
            }

            return true;
        }
        catch (\Exception $exception)
        {
            $this->error_handling($exception);

            return false;
        }
    }

    /**
     *
     * @param string $class_name
     * @param string[] $record
     *
     * @return boolean
     */
    public function create_record($class_name, $record)
    {
        try
        {
            $result = $this->connection->insert($class_name::get_table_name(), $record);
        }
        catch (\Exception $exception)
        {
            $this->error_handling($exception);

            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @throws Exception
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

        $query_builder = $this->connection->createQueryBuilder();
        $query_builder->update($object_table, $this->get_alias($object_table));

        foreach ($object->get_default_properties() as $key => $value)
        {
            $query_builder->set($key, $this->escape($value));
        }

        if ($condition)
        {
            if ($object instanceof CompositeDataClass)
            {
                $composite_condition = new EqualityCondition(
                    new PropertyConditionVariable($parent_class, $parent_class::PROPERTY_ID),
                    new StaticConditionVariable($object->get_id())
                );
                $query_builder->where(ConditionTranslator::render($composite_condition));
            }
            else
            {
                $query_builder->where(ConditionTranslator::render($condition));
            }
        }
        else
        {
            throw new Exception('Cannot update records without a condition');
        }

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if ($statement instanceof \PDOException)
        {
            $this->error_handling($statement);

            return false;
        }

        if ($object instanceof CompositeDataClass && $object::is_extended())
        {
            $query_builder = $this->connection->createQueryBuilder();
            $query_builder->update($object->get_table_name(), $this->get_alias($object->get_table_name()));

            $props = array();
            foreach ($object->get_additional_properties() as $key => $value)
            {
                $query_builder->set($key, $this->escape($value));
            }

            if ($condition)
            {
                $query_builder->where(ConditionTranslator::render($condition));
            }
            else
            {
                throw new Exception('Cannot update records without a condition');
            }

            $statement = $this->get_connection()->query($query_builder->getSQL());

            if ($statement instanceof \PDOException)
            {
                $this->error_handling($statement);

                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @throws Exception
     * @return boolean
     */
    public function updates($class, $properties, $condition)
    {
        if (count($properties->get()) > 0)
        {
            $query_builder = $this->connection->createQueryBuilder();
            $query_builder->update($class::get_table_name(), $this->get_alias($class::get_table_name()));

            foreach ($properties->get() as $data_class_property)
            {
                $query_builder->set(
                    ConditionVariableTranslator::render($data_class_property->get_property()),
                    ConditionVariableTranslator::render($data_class_property->get_value())
                );
            }

            if ($condition)
            {
                $query_builder->where(ConditionTranslator::render($condition));
            }
            else
            {
                throw new Exception('Cannot update records without a condition');
            }

            $statement = $this->get_connection()->query($query_builder->getSQL());

            if (!$statement instanceof \PDOException)
            {
                return true;
            }
            else
            {
                $this->error_handling($statement);

                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     */
    public function delete($class, $condition)
    {
        $query_builder = new QueryBuilder($this->connection);
        $query_builder->delete($class::get_table_name(), $this->get_alias($class::get_table_name()));
        if (isset($condition))
        {
            $query_builder->where(ConditionTranslator::render($condition));
        }

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (!$statement instanceof \PDOException)
        {
            return true;
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     * Drop a given storage unit
     *
     * @param string $table_name
     *
     * @return boolean
     */
    public function drop_storage_unit($table_name)
    {
        $schema = new \Doctrine\DBAL\Schema\Schema(array(new \Doctrine\DBAL\Schema\Table($table_name)));

        $new_schema = clone $schema;
        $new_schema->dropTable($table_name);

        $sql = $schema->getMigrateToSql($new_schema, $this->get_connection()->getDatabasePlatform());

        foreach ($sql as $query)
        {
            $statement = $this->get_connection()->query($query);

            if ($statement instanceof \PDOException)
            {
                $this->error_handling($statement);

                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param string $old_name
     * @param string $new_name
     *
     * @return boolean
     */
    public function rename_storage_unit($old_name, $new_name)
    {
        $query = 'ALTER TABLE ' . $old_name . ' RENAME TO ' . $new_name;

        $statement = $this->get_connection()->query($query);

        if (!$statement instanceof \PDOException)
        {
            return true;
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param string[] $attributes
     *
     * @return boolean
     */
    public function alter_storage_unit($type, $table_name, $property, $attributes = array())
    {
        try
        {
            if ($type == DataManager::ALTER_STORAGE_UNIT_DROP)
            {
                $column = new \Doctrine\DBAL\Schema\Column($property);
                $query = 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' .
                    $column->getQuotedName($this->get_connection()->getDatabasePlatform());
            }
            else
            {
                $column = new \Doctrine\DBAL\Schema\Column(
                    $property,
                    \Doctrine\DBAL\Types\Type::getType(self::parse_storage_unit_property_type($attributes)),
                    self::parse_storage_unit_attributes($attributes)
                );

                // Column declaration translation-code more or less directly from Doctrine since it doesn't support
                // altering tables (yet)
                $columns = array();
                /* @var \Doctrine\DBAL\Schema\Column $column */
                $columnData = array();

                if (isset($attributes['name']) && $type == DataManager::ALTER_STORAGE_UNIT_CHANGE)
                {
                    $old_name = $column->getQuotedName($this->get_connection()->getDatabasePlatform());
                    $columnData['name'] = $old_name . ' ' . $attributes['name'];
                }
                elseif ($type == DataManager::ALTER_STORAGE_UNIT_CHANGE)
                {
                    $name = $column->getQuotedName($this->get_connection()->getDatabasePlatform());
                    $columnData['name'] = $name . ' ' . $name;
                }
                elseif ($type == DataManager::ALTER_STORAGE_UNIT_ADD)
                {
                    $name = $column->getQuotedName($this->get_connection()->getDatabasePlatform());
                    $columnData['name'] = $name;
                }

                $columnData['type'] = $column->getType();
                $columnData['length'] = $column->getLength();
                $columnData['notnull'] = $column->getNotNull();
                $columnData['fixed'] = $column->getFixed();
                $columnData['unique'] = false; // TODO: what do we do about this?
                $columnData['version'] =
                    ($column->hasPlatformOption("version")) ? $column->getPlatformOption('version') : false;

                if (strtolower($columnData['type']) == "string" && $columnData['length'] === null)
                {
                    $columnData['length'] = 255;
                }

                $columnData['unsigned'] = $column->getUnsigned();
                $columnData['precision'] = $column->getPrecision();
                $columnData['scale'] = $column->getScale();
                $columnData['default'] = $column->getDefault();
                $columnData['columnDefinition'] = $column->getColumnDefinition();
                $columnData['autoincrement'] = $column->getAutoincrement();

                $columnData['comment'] = $column->getComment();
                if ($this->get_connection()->getDatabasePlatform()->isCommentedDoctrineType($column->getType()))
                {
                    $columnData['comment'] .= $this->get_connection()->getDatabasePlatform()->getDoctrineTypeComment(
                        $column->getType()
                    );
                }

                $columns = array($columnData['name'] => $columnData);

                $fields_query = $this->get_connection()->getDatabasePlatform()->getColumnDeclarationListSQL($columns);

                $action = $type == DataManager::ALTER_STORAGE_UNIT_CHANGE ? 'CHANGE' : 'ADD';
                $query = 'ALTER TABLE ' . $table_name . ' ' . $action . ' COLUMN ' . $fields_query;

                if ($type == DataManager::ALTER_STORAGE_UNIT_ADD && $columnData['autoincrement'])
                {
                    $query .= ', ADD PRIMARY KEY(' . $columnData['name'] . ')';
                }
            }

            $statement = $this->get_connection()->query($query);

            if (!$statement instanceof \PDOException)
            {
                return true;
            }
            else
            {
                $this->error_handling($statement);

                return false;
            }
        }
        catch (\Exception $exception)
        {
            $this->error_handling($exception);

            return false;
        }
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
    public function alter_storage_unit_index($type, $table_name, $name = null, $columns = array())
    {
        $query = 'ALTER TABLE ' . $table_name . ' ';

        switch ($type)
        {
            case DataManager::ALTER_STORAGE_UNIT_DROP_PRIMARY_KEY :
                $query .= 'DROP PRIMARY KEY';
                break;
            case DataManager::ALTER_STORAGE_UNIT_DROP_INDEX :

                if (is_null($name))
                {
                    return false;
                }

                $query .= 'DROP INDEX ' . $name;
                break;
            case DataManager::ALTER_STORAGE_UNIT_ADD_PRIMARY_KEY :
                $query .= 'ADD PRIMARY KEY(' . implode(', ', array_unique($columns)) . ')';
                break;
            case DataManager::ALTER_STORAGE_UNIT_ADD_INDEX :
                $query .= 'ADD ' . $this->get_connection()->getDatabasePlatform()->getIndexDeclarationSQL(
                        $name,
                        new \Doctrine\DBAL\Schema\Index($name, $columns, false, false)
                    );
                break;
            case DataManager::ALTER_STORAGE_UNIT_ADD_UNIQUE :
                $query .= 'ADD ' . $this->get_connection()->getDatabasePlatform()->getIndexDeclarationSQL(
                        $name,
                        new \Doctrine\DBAL\Schema\Index($name, $columns, true, false)
                    );
                break;
        }

        $statement = $this->get_connection()->query($query);

        if (!$statement instanceof \PDOException)
        {
            return true;
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     */
    public function count($class, $parameters)
    {
        $query_builder = $this->connection->createQueryBuilder();

        if ($parameters->get_property() instanceof ConditionVariable)
        {
            $property = ConditionVariableTranslator::render($parameters->get_property());
        }
        else
        {
            $property = '1';
        }

        $query_builder->addSelect('COUNT(' . $property . ')');
        $query_builder->from($this->prepare_table_name($class), $this->get_alias($this->prepare_table_name($class)));

        $query_builder = $this->process_parameters($query_builder, $class, $parameters);

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (!$statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_NUM);

            return (int) $record[0];
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return integer
     */
    public function count_grouped($class, $parameters)
    {
        $query_builder = $this->connection->createQueryBuilder();
        foreach ($parameters->get_property()->get() as $property)
        {

            $query_builder->addSelect(ConditionVariableTranslator::render($property));
        }
        $query_builder->addSelect('COUNT(1)');
        $query_builder->from($class::get_table_name(), $this->get_alias($class::get_table_name()));

        $query_builder = $this->process_parameters($query_builder, $class, $parameters);
        foreach ($parameters->get_property()->get() as $property)
        {
            $query_builder->addGroupBy(ConditionVariableTranslator::render($property));
        }
        $query_builder->having(ConditionTranslator::render($parameters->get_having()));
        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (!$statement instanceof \PDOException)
        {
            $counts = array();
            while ($record = $statement->fetch(\PDO::FETCH_NUM))
            {
                $counts[$record[0]] = $record[1];
            }

            $record = $statement->fetch(\PDO::FETCH_NUM);

            return $counts;
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     */
    private function process_composite_data_class_joins($query_builder, $class, $parameters)
    {
        if ($parameters->get_joins() instanceof Joins)
        {
            foreach ($parameters->get_joins()->get() as $join)
            {
                if (is_subclass_of($join->get_data_class(), CompositeDataClass::class_name()))
                {
                    if (is_subclass_of($class, $join->get_data_class()))
                    {
                        $join_class = $join->get_data_class();

                        $data_manager =
                            ClassnameUtilities::getInstance()->getNamespaceParent($join_class::context(), 1) .
                            '\DataManager';

                        $alias = $data_manager::getInstance()->get_alias($join_class::get_table_name());

                        $query_builder->addSelect($alias . '.*');
                    }
                }
            }
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\DataClassResultSet
     */
    public function retrieves($class, DataClassRetrievesParameters $parameters)
    {
        return new DataClassResultSet(
            $this->get_records_result($this->build_retrieves_sql($class, $parameters), $class, $parameters),
            $class
        );
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\RecordResultSet
     */
    public function records($class, RecordRetrievesParameters $parameters)
    {
        return new RecordResultSet(
            $this->get_records_result($this->build_records_sql($class, $parameters), $class, $parameters)
        );
    }

    /**
     *
     * @param string $sql
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @throws DataClassNoResultException
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function get_records_result($sql, $class, $parameters)
    {
        try
        {
            return $this->get_connection()->query($sql);
        }
        catch (\PDOException $exception)
        {
            $this->error_handling($exception);
            throw new DataClassNoResultException($class, $parameters, $sql);
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return string
     */
    public function build_retrieves_sql($class, DataClassRetrievesParameters $parameters)
    {
        $query_builder = $this->connection->createQueryBuilder();

        $select = $this->get_alias($this->prepare_table_name($class)) . '.*';

        if ($parameters->get_distinct())
        {
            $select = 'DISTINCT ' . $select;
        }

        $query_builder->addSelect($select);

        $this->process_composite_data_class_joins($query_builder, $class, $parameters);

        return $this->build_basic_records_sql($query_builder, $class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string
     */
    public function build_records_sql($class, RecordRetrievesParameters $parameters)
    {
        $query_builder = $this->connection->createQueryBuilder();

        $query_builder = $this->process_data_class_properties($query_builder, $class, $parameters->get_properties());
        $query_builder = $this->process_group_by($query_builder, $parameters->get_group_by());

        return $this->build_basic_records_sql($query_builder, $class, $parameters);
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return string
     */
    public function build_basic_records_sql($query_builder, $class, $parameters)
    {
        $query_builder->from($this->prepare_table_name($class), $this->get_alias($this->prepare_table_name($class)));
        $query_builder = $this->process_parameters($query_builder, $class, $parameters);
        $query_builder = $this->process_order_by($query_builder, $class, $parameters->get_order_by());
        $query_builder = $this->process_limit($query_builder, $parameters->get_count(), $parameters->get_offset());

        return $query_builder->getSQL();
    }

    /**
     *
     * @param string $class
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function retrieve_maximum_value($class, $property, $condition = null)
    {
        $query_builder = $this->connection->createQueryBuilder();
        $query_builder->addSelect(
            'MAX(' . self::escape_column_name($property, $this->get_alias($class::get_table_name())) . ') AS ' .
            self::ALIAS_MAX_SORT
        );
        $query_builder->from($class::get_table_name(), $this->get_alias($class::get_table_name()));

        if (isset($condition))
        {
            $query_builder->where(ConditionTranslator::render($condition, $this->get_alias($class::get_table_name())));
        }

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (!$statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_NUM);

            return (int) $record[0];
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param string $table_name
     * @param boolean $optimize
     *
     * @return boolean
     */
    public function truncate_storage_unit($table_name, $optimize = true)
    {
        $query_builder = $this->connection->createQueryBuilder();
        $query_builder->delete($table_name);

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (!$statement instanceof \PDOException)
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
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param string $table_name
     *
     * @return boolean
     */
    public function optimize_storage_unit($table_name)
    {
        return true;
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return string[]
     */
    public function retrieve($class, $parameters = null)
    {
        $query_builder = $this->connection->createQueryBuilder();
        $query_builder->addSelect($this->get_alias($this->prepare_table_name($class)) . '.*');

        $this->process_composite_data_class_joins($query_builder, $class, $parameters);

        return $this->fetch_record($query_builder, $class, $parameters);
    }

    /**
     *
     * @param string $class
     *
     * @return string
     */
    private function prepare_table_name($class)
    {
        if (is_subclass_of($class, CompositeDataClass::class_name()) &&
            get_parent_class($class) == CompositeDataClass::class_name()
        )
        {
            $table_name = $class::get_table_name();
        }
        elseif (is_subclass_of($class, CompositeDataClass::class_name()) && $class::is_extended())
        {
            $table_name = $class::get_table_name();
        }
        elseif (is_subclass_of($class, CompositeDataClass::class_name()) && !$class::is_extended())
        {
            $parent = $class::parent_class_name();
            $table_name = $parent::get_table_name();
        }
        else
        {
            $table_name = $class::get_table_name();
        }

        return $table_name;
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return string[]
     */
    public function record($class, $parameters = null)
    {
        $query_builder = $this->connection->createQueryBuilder();

        $group_by = $parameters->get_group_by();
        if ($group_by instanceof GroupBy)
        {
            foreach ($group_by->get_group_by() as $group_by_variable)
            {
                $query_builder->addGroupBy(ConditionVariableTranslator::render($group_by_variable));
            }
        }

        if ($parameters->get_properties() instanceof DataClassProperties)
        {

            foreach ($parameters->get_properties()->get() as $condition_variable)
            {
                $query_builder->addSelect(ConditionVariableTranslator::render($condition_variable));
            }
        }
        else
        {

            return $this->retrieve($class, $parameters);
        }

        return $this->fetch_record($query_builder, $class, $parameters);
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @throws DataClassNoResultException
     * @return string[]
     */
    private function fetch_record($query_builder, $class, $parameters)
    {
        $query_builder->from($this->prepare_table_name($class), $this->get_alias($this->prepare_table_name($class)));

        $query_builder = $this->process_parameters($query_builder, $class, $parameters);
        $query_builder = $this->process_order_by($query_builder, $class, $parameters->get_order_by());

        $query_builder->setFirstResult(0);
        $query_builder->setMaxResults(1);

        $sqlQuery = $query_builder->getSQL();

        $statement = $this->get_connection()->query($sqlQuery);

        if (!$statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_ASSOC);
        }
        else
        {
            $this->error_handling($statement);
            throw new DataClassNoResultException($class, $parameters, $sqlQuery);
        }

        if ($record instanceof \PDOException)
        {
            $this->error_handling($record);
            throw new DataClassNoResultException($class, $parameters, $sqlQuery);
        }

        if (is_null($record) || !is_array($record) || empty($record))
        {
            throw new DataClassNoResultException($class, $parameters, $sqlQuery);
        }

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
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]
     */
    public function distinct($class, DataClassDistinctParameters $parameters)
    {
        $properties = $parameters->get_property();

        if (!is_array($properties))
        {
            $properties = array($properties);
        }

        $select = array();

        foreach ($properties as $property)
        {
            $select[] = self::escape_column_name($property, $this->get_alias($class::get_table_name()));
        }

        $query_builder = $this->connection->createQueryBuilder();
        $query_builder->addSelect('DISTINCT ' . implode(',', $select));
        $query_builder->from($class::get_table_name(), $this->get_alias($class::get_table_name()));

        $query_builder = $this->process_parameters($query_builder, $class, $parameters);
        $query_builder = $this->process_order_by($query_builder, $class, $parameters->getOrderBy());

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (!$statement instanceof \PDOException)
        {
            $distinct_elements = array();
            while ($record = $statement->fetch(\PDO::FETCH_ASSOC))
            {
                if (count($properties) > 1)
                {
                    $distinct_elements[] = $record;
                }
                else
                {
                    $distinct_elements[] = array_pop($record);
                }
            }

            return $distinct_elements;
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param string $table_name
     *
     * @return string
     */
    public function get_alias($table_name)
    {
        return StorageAliasGenerator::getInstance()->get_table_alias($table_name);
    }

    /**
     *
     * @param string $name
     *
     * @return string
     */
    public function get_constraint_name($name)
    {
        $possible_name = '';
        $parts = explode('_', $name);
        foreach ($parts as & $part)
        {
            $possible_name .= $part{0};
        }

        return $possible_name;
    }

    /**
     *
     * @param string $value
     * @param string $type
     * @param boolean $quote
     * @param boolean $escape_wildcards
     *
     * @return string
     */
    public static function quote($value, $type = null, $quote = true, $escape_wildcards = false)
    {
        return Connection::getInstance()->get_connection()->quote($value, $type, $quote, $escape_wildcards);
    }

    /**
     *
     * @param string $text
     * @param boolean $escape_wildcards
     *
     * @return string
     */
    public function escape($text, $escape_wildcards = false)
    {
        if (!is_null($text))
        {
            return $this->connection->quote($text);
        }
        else
        {
            return 'NULL';
        }
    }

    /**
     * Delegate method for the exec function Exec is used when the query does not expect a resultset but only true /
     * false
     *
     * @param string $query
     *
     * @return boolean
     */
    public function exec($query)
    {
        return $this->connection->exec($query);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\CompositeDataClass $object
     *
     * @return string[]
     */
    public function retrieve_composite_data_class_additional_properties(CompositeDataClass $object)
    {
        if (!$object->is_extended())
        {
            return array();
        }
        $array = $object->get_additional_property_names();

        if (count($array) == 0)
        {
            $array = array("*");
        }

        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $object::get_table_name() . ' WHERE ' .
            $object::PROPERTY_ID . '=' . $this->quote($object->get_id());

        $statement = $this->get_connection()->query($query);

        if (!$statement instanceof \PDOException)
        {
            $distinct_elements = array();
            $record = $statement->fetch(\PDO::FETCH_ASSOC);

            $additional_properties = array();

            if (is_array($record))
            {
                $properties = $object->get_additional_property_names();

                if (count($properties) > 0)
                {
                    foreach ($properties as $prop)
                    {
                        if (is_resource($record[$prop]))
                        {
                            $data = '';
                            while (!feof($record[$prop]))
                            {
                                $data .= fread($record[$prop], 1024);
                            }
                            $record[$prop] = $data;
                        }
                        $additional_properties[$prop] = $record[$prop];
                    }
                }
            }

            return $additional_properties;
        }
        else
        {
            $this->error_handling($statement);

            return false;
        }
    }

    /**
     *
     * @param unknown $function
     *
     * @throws Exception
     * @return mixed
     */
    public function transactional($function)
    {
        // Rather than directly using Doctrine's version of transactional, we implement
        // an intermediate function that throws an exception if the function returns #f.
        // This mediates between Chamilo's convention of returning #f to signal failure
        // versus Doctrine's use of Exceptions.
        $throw_on_false = function ($connection) use ($function)
        {
            $result = call_user_func($function, $connection);
            if (!$result)
            {
                throw new Exception();
            }
            else
            {
                return $result;
            }
        };

        try
        {
            $this->connection->transactional($throw_on_false);

            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_by
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_order_by($query_builder, $class, $order_by)
    {
        if (is_null($order_by))
        {
            $order_by = array();
        }
        elseif (!is_array($order_by))
        {
            $order_by = array($order_by);
        }

        foreach ($order_by as $order)
        {
            $query_builder->addOrderBy(
                ConditionVariableTranslator::render($order->get_property()),
                ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC')
            );
        }

        return $query_builder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param int $count
     * @param int $offset
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_limit($query_builder, $count = null, $offset = null)
    {
        if (intval($count) > 0)
        {
            $query_builder->setMaxResults(intval($count));
        }

        if (intval($offset) > 0)
        {
            $query_builder->setFirstResult(intval($offset));
        }

        return $query_builder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_data_class_properties($query_builder, $class, $properties)
    {
        if ($properties instanceof DataClassProperties)
        {
            foreach ($properties->get() as $condition_variable)
            {
                $query_builder->addSelect(ConditionVariableTranslator::render($condition_variable));
            }
        }
        else
        {
            $query_builder->addSelect($this->get_alias($class::get_table_name()) . '.*');
        }

        return $query_builder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $group_by
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_group_by($query_builder, $group_by)
    {
        if ($group_by instanceof GroupBy)
        {
            foreach ($group_by->get() as $group_by_variable)
            {
                $query_builder->addGroupBy(ConditionVariableTranslator::render($group_by_variable));
            }
        }

        return $query_builder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_joins($query_builder, $class, $joins)
    {
        if (is_subclass_of($class, CompositeDataClass::class_name()) && get_parent_class($class) !=
            CompositeDataClass::class_name() && !$class::is_extended()
        )
        {
            $class = $class::parent_class_name();
        }

        $classAlias = $this->get_alias($class::get_table_name());

        if ($joins instanceof Joins)
        {
            foreach ($joins->get() as $join)
            {
                $join_condition = ConditionTranslator::render($join->get_condition());
                $data_class_name = $join->get_data_class();

                switch ($join->get_type())
                {
                    case Join::TYPE_NORMAL :
                        $query_builder->join(
                            $classAlias,
                            $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()),
                            $join_condition
                        );
                        break;
                    case Join::TYPE_RIGHT :
                        $query_builder->rightJoin(
                            $classAlias,
                            $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()),
                            $join_condition
                        );
                        break;
                    case Join::TYPE_LEFT :
                        $query_builder->leftJoin(
                            $classAlias,
                            $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()),
                            $join_condition
                        );
                        break;
                }
            }
        }

        return $query_builder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_condition($query_builder, $class, $condition)
    {
        if ($condition instanceof Condition)
        {
            $query_builder->where(
                ConditionTranslator::render($condition, $this->get_alias($this->prepare_table_name($class)))
            );
        }

        return $query_builder;
    }

    /**
     *
     * @param Condition $condition
     *
     * @return string
     */
    public function translateCondition(Condition $condition = null)
    {
        return ConditionTranslator::render($condition);
    }

    /**
     * Processes the parameters
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_parameters($query_builder, $class, $parameters)
    {
        $query_builder = $this->process_joins($query_builder, $class, $parameters->get_joins());
        $query_builder = $this->process_condition($query_builder, $class, $parameters->get_condition());

        return $query_builder;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }
}
