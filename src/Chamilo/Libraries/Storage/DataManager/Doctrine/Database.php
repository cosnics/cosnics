<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Condition\ConditionTranslator;
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
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;
use PDOException;

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
    use ClassContext;

    const ALIAS_MAX_SORT = 'max_sort';
    const STORAGE_TYPE = 'Doctrine';

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
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @param \Doctrine\DBAL\Connection|null $connection
     *
     * @throws \Exception
     */
    public function __construct($connection = null)
    {
        if (is_null($connection))
        {
            $this->connection =
                DependencyInjectionContainerBuilder::getInstance()->createContainer()->get('Doctrine\DBAL\Connection');
        }
        else
        {
            $this->connection = $connection;
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
        return $this->getStorageUnitDatabase()->alter($type, $table_name, $property, $attributes);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $name
     * @param string[] $columns
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     */
    public function alter_storage_unit_index($type, $table_name, $name = null, $columns = array())
    {
        return $this->getStorageUnitDatabase()->alterIndex($type, $table_name, $name, $columns);
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return string
     * @throws \ReflectionException
     */
    public function build_basic_records_sql($query_builder, $class, $parameters)
    {
        $query_builder->from($this->prepare_table_name($class), $this->get_alias($this->prepare_table_name($class)));
        $query_builder = $this->process_parameters($query_builder, $class, $parameters);
        $query_builder = $this->process_order_by($query_builder, $class, $parameters->getOrderBy());
        $query_builder = $this->process_limit($query_builder, $parameters->getCount(), $parameters->getOffset());

        return $query_builder->getSQL();
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string
     * @throws \ReflectionException
     */
    public function build_records_sql($class, RecordRetrievesParameters $parameters)
    {
        $query_builder = $this->connection->createQueryBuilder();

        $query_builder =
            $this->process_data_class_properties($query_builder, $class, $parameters->getDataClassProperties());
        $query_builder = $this->process_group_by($query_builder, $parameters->getGroupBy());

        return $this->build_basic_records_sql($query_builder, $class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function count($class, $parameters)
    {
        return $this->getDataClassDatabase()->count($class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return integer[]|false
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function count_grouped($class, $parameters)
    {
        return $this->getDataClassDatabase()->countGrouped($class, $parameters);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param boolean $auto_id
     *
     * @return boolean
     * @throws \Exception
     */
    public function create($object, $auto_id = true)
    {
        return $this->getDataClassDatabase()->create($object, $auto_id);
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
        return $this->getDataClassDatabase()->createRecord($class_name, $record);
    }

    /**
     * Creates a storage unit in the system
     *
     * @param string $name String the table name
     * @param string[][] $properties Array the table properties
     * @param string[][][] $indexes Array the table indexes
     *
     * @return true if the storage unit is succesfully created
     */
    public function create_storage_unit($name, $properties, $indexes)
    {
        return $this->getStorageUnitDatabase()->create($name, $properties, $indexes);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function delete($class, $condition)
    {
        return $this->getDataClassDatabase()->delete($class, $condition);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]|boolean
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function distinct($class, DataClassDistinctParameters $parameters)
    {
        return $this->getDataClassDatabase()->distinct($class, $parameters);
    }

    /**
     * Drop a given storage unit
     *
     * @param string $table_name
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     */
    public function drop_storage_unit($table_name)
    {
        return $this->getStorageUnitDatabase()->drop($table_name);
    }

    /**
     * @param \Exception $error
     */
    public function error_handling($error)
    {
        if (!self::$error_log)
        {
            $logfile = Path::getInstance()->getLogPath() . '/cosnics.error.doctrine.log';
            self::$error_log = new FileLogger($logfile, true);
        }

        $message = "[Message: {$error->getMessage()}] [Information: { USER INFO GOES HERE}]";
        self::$error_log->log_message($message);
    }

    /**
     *
     * @param string $text
     *
     * @return string
     */
    public function escape($text)
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
     * Escapes a column name in accordance with the database type.
     *
     * @param $name string The column name.
     * @param $tableAlias String The alias of the table the coloumn is in
     *
     * @return string The escaped column name.
     */
    public static function escape_column_name($name, $tableAlias = null)
    {
        if (!empty($tableAlias))
        {
            return $tableAlias . '.' . $name;
        }
        else
        {
            return $name;
        }
    }

    /**
     * Delegate method for the exec function Exec is used when the query does not expect a resultset but only true /
     * false
     *
     * @param string $query
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     */
    public function exec($query)
    {
        return $this->connection->exec($query);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase
     * @throws \Exception
     */
    protected function getDataClassDatabase()
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase');
    }

    /**
     * @param $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService($serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase
     */
    protected function getStorageUnitDatabase()
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase');
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
     * @return \Doctrine\DBAL\Connection
     */
    public function get_connection()
    {
        return $this->connection;
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
     * @param string $sql
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function get_records_result($sql, $class, $parameters)
    {
        try
        {
            return $this->get_connection()->query($sql);
        }
        catch (PDOException $exception)
        {
            $this->error_handling($exception);
            throw new DataClassNoResultException($class, $parameters, $sql);
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
        return $this->getStorageUnitDatabase()->optimize($table_name);
    }

    /**
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }

    /**
     *
     * @param string $class
     *
     * @return string
     * @throws \ReflectionException
     */
    private function prepare_table_name($class)
    {
        if (is_subclass_of($class, CompositeDataClass::class) && get_parent_class($class) == CompositeDataClass::class)
        {
            $table_name = $class::get_table_name();
        }
        elseif (is_subclass_of($class, CompositeDataClass::class) && $class::is_extended())
        {
            $table_name = $class::get_table_name();
        }
        elseif (is_subclass_of($class, CompositeDataClass::class) && !$class::is_extended())
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
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     */
    private function process_composite_data_class_joins($query_builder, $class, $parameters)
    {
        if ($parameters->getJoins() instanceof Joins)
        {
            foreach ($parameters->getJoins()->get() as $join)
            {
                if (is_subclass_of($join->get_data_class(), CompositeDataClass::class))
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
            $query_builder->where(ConditionTranslator::render($condition));
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
     * @throws \ReflectionException
     */
    protected function process_joins($query_builder, $class, $joins)
    {
        if (is_subclass_of($class, CompositeDataClass::class) &&
            get_parent_class($class) != CompositeDataClass::class && !$class::is_extended())
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
                            $classAlias, $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()), $join_condition
                        );
                        break;
                    case Join::TYPE_RIGHT :
                        $query_builder->rightJoin(
                            $classAlias, $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()), $join_condition
                        );
                        break;
                    case Join::TYPE_LEFT :
                        $query_builder->leftJoin(
                            $classAlias, $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()), $join_condition
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
                ConditionVariableTranslator::render($order->getConditionVariable()),
                ($order->getDirection() == SORT_DESC ? 'DESC' : 'ASC')
            );
        }

        return $query_builder;
    }

    /**
     * Processes the parameters
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     * @throws \ReflectionException
     */
    protected function process_parameters($query_builder, $class, $parameters)
    {
        $query_builder = $this->process_joins($query_builder, $class, $parameters->getJoins());
        $query_builder = $this->process_condition($query_builder, $class, $parameters->getCondition());

        return $query_builder;
    }

    /**
     *
     * @param string $value
     * @param string|null $type
     *
     * @return string
     */
    public static function quote($value, $type = null)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase'
        )->quote($value, $type);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters|null $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function record($class, $parameters = null)
    {
        return $this->getDataClassDatabase()->record($class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function records($class, RecordRetrievesParameters $parameters)
    {
        return $this->getDataClassDatabase()->records($class, $parameters);
    }

    /**
     *
     * @param string $old_name
     * @param string $new_name
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     */
    public function rename_storage_unit($old_name, $new_name)
    {
        return $this->getStorageUnitDatabase()->rename($old_name, $new_name);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function retrieve($class, $parameters = null)
    {
        return $this->getDataClassDatabase()->retrieve($class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function retrieves($class, DataClassRetrievesParameters $parameters)
    {
        return $this->getDataClassDatabase()->retrieves($class, $parameters);
    }

    /**
     *
     * @param string $name
     *
     * @return boolean
     */
    public function storage_unit_exists($name)
    {
        return $this->getStorageUnitDatabase()->optimize($name);
    }

    /**
     *
     * @param mixed $function
     *
     * @return mixed
     * @throws \Throwable
     */
    public function transactional($function)
    {
        return $this->getDataClassDatabase()->transactional($function);
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
     *
     * @param string $table_name
     * @param boolean $optimize
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncate_storage_unit($table_name, $optimize = true)
    {
        return $this->getStorageUnitDatabase()->truncate($table_name, $optimize);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|false $condition
     *
     * @return boolean
     * @throws Exception
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
                $parent_class = $object::parent_class_name();

                $composite_condition = new EqualityCondition(
                    new PropertyConditionVariable($parent_class, $parent_class::PROPERTY_ID),
                    new StaticConditionVariable($object->getId())
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

        if ($statement instanceof PDOException)
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

            if ($statement instanceof PDOException)
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
     * @return boolean
     * @throws Exception
     */
    public function updates($class, $properties, $condition)
    {
        return $this->getDataClassDatabase()->updates($class, $properties, $condition);
    }
}
