<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
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
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Exception;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassDatabase
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    // Constants
    const ALIAS_MAX_SORT = 'max_sort';

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    protected $storageAliasGenerator;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory
     */
    protected $conditionPartTranslatorFactory;

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory $conditionPartTranslatorFactory
     */
    public function __construct(\Doctrine\DBAL\Connection $connection, StorageAliasGenerator $storageAliasGenerator,
        ExceptionLoggerInterface $exceptionLogger, ConditionPartTranslatorFactory $conditionPartTranslatorFactory)
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;
    }

    /**
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    public function getStorageAliasGenerator()
    {
        return $this->storageAliasGenerator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     */
    public function setStorageAliasGenerator($storageAliasGenerator)
    {
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function getExceptionLogger()
    {
        return $this->exceptionLogger;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function setExceptionLogger($exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory
     */
    public function getConditionPartTranslatorFactory()
    {
        return $this->conditionPartTranslatorFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConditionPartTranslatorFactory $conditionPartTranslatorFactory
     */
    public function setConditionPartTranslatorFactory($conditionPartTranslatorFactory)
    {
        $this->conditionPartTranslatorFactory = $conditionPartTranslatorFactory;
    }

    /**
     *
     * @param \Exception $exception
     */
    public function handleError(\Exception $exception)
    {
        $this->getExceptionLogger()->logException(
            '[Message: ' . $exception->getMessage() . '] [Information: {USER INFO GOES HERE}]');
    }

    /**
     * Escapes a column name in accordance with the database type.
     *
     * @param $name string The column name.
     * @param $table_alias String The alias of the table the coloumn is in
     * @return string The escaped column name.
     */
    public static function escapeColumnName($columnName, $tableAlias = null)
    {
        if (! empty($tableAlias))
        {
            return $tableAlias . '.' . $columnName;
        }
        else
        {
            return $columnName;
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param boolean $autoAssignIdentifier
     * @return boolean
     */
    public function create(DataClass $dataClass, $autoAssignIdentifier = true)
    {
        if ($dataClass instanceof CompositeDataClass)
        {
            $parentClass = $object::parent_class_name();
            $objectTableName = $parent_class::get_table_name();
        }
        else
        {
            $objectTableName = $dataClass->get_table_name();
        }

        $objectProperties = $dataClass->get_default_properties();

        if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->get_default_property_names()))
        {
            $objectProperties[DataClass::PROPERTY_ID] = null;
        }

        try
        {
            $this->getConnection()->insert($objectTableName, $objectProperties);

            if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->get_default_property_names()))
            {
                $dataClass->setId($this->getConnection()->lastInsertId($objectTableName));
            }

            if ($dataClass instanceof CompositeDataClass && $object::is_extended())
            {
                $objectProperties = $dataClass->get_additional_properties();
                $objectProperties[DataClass::PROPERTY_ID] = $dataClass->getId();

                $this->getConnection()->insert($dataClass->get_table_name(), $objectProperties);
            }

            return true;
        }
        catch (\Exception $exception)
        {
            $this->handleError($exception);
            return false;
        }
    }

    /**
     *
     * @param string $className
     * @param string[] $record
     * @return boolean
     */
    public function createRecord($className, $record)
    {
        try
        {
            $result = $this->getConnection()->insert($className::get_table_name(), $record);
        }
        catch (\Exception $exception)
        {
            $this->handleError($exception);
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string[] $propertiesToUpdate
     * @throws Exception
     * @return boolean
     */
    public function update($objectTableName, $condition, $propertiesToUpdate)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->update($objectTableName, $this->getAlias($objectTableName));

        foreach ($propertiesToUpdate as $key => $value)
        {
            $queryBuilder->set($key, $this->escape($value));
        }

        if ($condition instanceof Condition)
        {
            $queryBuilder->where(ConditionTranslator::render($condition));
        }
        else
        {
            throw new Exception('Cannot update records without a condition');
        }

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if ($statement instanceof \PDOException)
        {
            $this->handleError($statement);
            return false;
        }

        return true;
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @throws Exception
     * @return boolean
     */
    public function updates($class, $properties, $condition)
    {
        if (count($properties->get()) > 0)
        {
            $query_builder = $this->getConnection()->createQueryBuilder();
            $query_builder->update($class::get_table_name(), $this->get_alias($class::get_table_name()));

            foreach ($properties->get() as $data_class_property)
            {
                $query_builder->set(
                    ConditionVariableTranslator::render($data_class_property->get_property()),
                    ConditionVariableTranslator::render($data_class_property->get_value()));
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

            if (! $statement instanceof \PDOException)
            {
                return true;
            }
            else
            {
                $this->handleError($statement);
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return boolean
     */
    public function delete($class, $condition)
    {
        $query_builder = new QueryBuilder($this->getConnection());
        $query_builder->delete($class::get_table_name(), $this->get_alias($class::get_table_name()));
        if (isset($condition))
        {
            $query_builder->where(ConditionTranslator::render($condition));
        }

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (! $statement instanceof \PDOException)
        {
            return true;
        }
        else
        {
            $this->handleError($statement);
            return false;
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @return integer
     */
    public function count($class, $parameters)
    {
        $query_builder = $this->getConnection()->createQueryBuilder();

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

        if (! $statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_NUM);
            return (int) $record[0];
        }
        else
        {
            $this->handleError($statement);
            return false;
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @return integer
     */
    public function count_grouped($class, $parameters)
    {
        $query_builder = $this->getConnection()->createQueryBuilder();
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

        if (! $statement instanceof \PDOException)
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
            $this->handleError($statement);
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

                        $data_manager = ClassnameUtilities::getInstance()->getNamespaceParent($join_class::context(), 1) .
                             '\DataManager';

                        $alias = $data_manager::get_instance()->get_alias($join_class::get_table_name());

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
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\DataClassResultSet
     */
    public function retrieves($class, DataClassRetrievesParameters $parameters)
    {
        return new DataClassResultSet(
            $this->get_records_result($this->build_retrieves_sql($class, $parameters), $class, $parameters),
            $class);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\RecordResultSet
     */
    public function records($class, RecordRetrievesParameters $parameters)
    {
        return new RecordResultSet(
            $this->get_records_result($this->build_records_sql($class, $parameters), $class, $parameters));
    }

    /**
     *
     * @param string $sql
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
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
            $this->handleError($exception);
            throw new DataClassNoResultException($class, $parameters, $sql);
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return string
     */
    public function build_retrieves_sql($class, DataClassRetrievesParameters $parameters)
    {
        $query_builder = $this->getConnection()->createQueryBuilder();

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
     * @return string
     */
    public function build_records_sql($class, RecordRetrievesParameters $parameters)
    {
        $query_builder = $this->getConnection()->createQueryBuilder();

        $query_builder = $this->process_data_class_properties($query_builder, $class, $parameters->get_properties());
        $query_builder = $this->process_group_by($query_builder, $parameters->get_group_by());

        return $this->build_basic_records_sql($query_builder, $class, $parameters);
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query_builder
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
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
     * @return integer
     */
    public function retrieve_maximum_value($class, $property, $condition = null)
    {
        $query_builder = $this->getConnection()->createQueryBuilder();
        $query_builder->addSelect(
            'MAX(' . self::escape_column_name($property, $this->get_alias($class::get_table_name())) . ') AS ' .
                 self::ALIAS_MAX_SORT);
        $query_builder->from($class::get_table_name(), $this->get_alias($class::get_table_name()));

        if (isset($condition))
        {
            $query_builder->where(ConditionTranslator::render($condition, $this->get_alias($class::get_table_name())));
        }

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (! $statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_NUM);
            return (int) $record[0];
        }
        else
        {
            $this->handleError($statement);
            return false;
        }
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return string[]
     */
    public function retrieve($class, $parameters = null)
    {
        $query_builder = $this->getConnection()->createQueryBuilder();
        $query_builder->addSelect($this->get_alias($this->prepare_table_name($class)) . '.*');

        $this->process_composite_data_class_joins($query_builder, $class, $parameters);

        return $this->fetch_record($query_builder, $class, $parameters);
    }

    /**
     *
     * @param string $class
     * @return string
     */
    private function prepare_table_name($class)
    {
        if (is_subclass_of($class, CompositeDataClass::class_name()) &&
             get_parent_class($class) == CompositeDataClass::class_name())
        {
            $table_name = $class::get_table_name();
        }
        elseif (is_subclass_of($class, CompositeDataClass::class_name()) && $class::is_extended())
        {
            $table_name = $class::get_table_name();
        }
        elseif (is_subclass_of($class, CompositeDataClass::class_name()) && ! $class::is_extended())
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
     * @return string[]
     */
    public function record($class, $parameters = null)
    {
        $query_builder = $this->getConnection()->createQueryBuilder();

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

        if (! $statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_ASSOC);
        }
        else
        {
            $this->handleError($statement);
            throw new DataClassNoResultException($class, $parameters, $sqlQuery);
        }

        if ($record instanceof \PDOException)
        {
            $this->handleError($record);
            throw new DataClassNoResultException($class, $parameters, $sqlQuery);
        }

        if (is_null($record) || ! is_array($record) || empty($record))
        {
            throw new DataClassNoResultException($class, $parameters, $sqlQuery);
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

        return $record;
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     * @return string[]
     */
    public function distinct($class, DataClassDistinctParameters $parameters)
    {
        $properties = $parameters->get_property();

        if (! is_array($properties))
        {
            $properties = array($properties);
        }

        $select = array();

        foreach ($properties as $property)
        {
            $select[] = self::escape_column_name($property, $this->get_alias($class::get_table_name()));
        }

        $query_builder = $this->getConnection()->createQueryBuilder();
        $query_builder->addSelect('DISTINCT ' . implode(',', $select));
        $query_builder->from($class::get_table_name(), $this->get_alias($class::get_table_name()));

        $query_builder = $this->process_parameters($query_builder, $class, $parameters);
        $query_builder = $this->process_order_by($query_builder, $class, $parameters->getOrderBy());

        $statement = $this->get_connection()->query($query_builder->getSQL());

        if (! $statement instanceof \PDOException)
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
            $this->handleError($statement);
            return false;
        }
    }

    /**
     *
     * @param string $table_name
     * @return string
     */
    public function get_alias($table_name)
    {
        return StorageAliasGenerator::get_instance()->get_table_alias($table_name);
    }

    /**
     *
     * @param string $name
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
        return Connection::get_instance()->get_connection()->quote($value, $type, $quote, $escape_wildcards);
    }

    /**
     *
     * @param string $text
     * @param boolean $escape_wildcards
     * @return string
     */
    public function escape($text, $escape_wildcards = false)
    {
        if (! is_null($text))
        {
            return $this->getConnection()->quote($text);
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
     * @return boolean
     */
    public function exec($query)
    {
        return $this->getConnection()->exec($query);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\CompositeDataClass $object
     * @return string[]
     */
    public function retrieve_composite_data_class_additional_properties(CompositeDataClass $object)
    {
        if (! $object->is_extended())
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

        if (! $statement instanceof \PDOException)
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
                            while (! feof($record[$prop]))
                                $data .= fread($record[$prop], 1024);
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
            $this->handleError($statement);
            return false;
        }
    }

    /**
     *
     * @param unknown $function
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
            if (! $result)
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
            $this->getConnection()->transactional($throw_on_false);
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
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function process_order_by($query_builder, $class, $order_by)
    {
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
            $query_builder->addOrderBy(
                ConditionVariableTranslator::render($order->get_property()),
                ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC'));
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
                            $this->get_alias($class::get_table_name()),
                            $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()),
                            $join_condition);
                        break;
                    case Join::TYPE_RIGHT :
                        $query_builder->rightJoin(
                            $this->get_alias($class::get_table_name()),
                            $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()),
                            $join_condition);
                        break;
                    case Join::TYPE_LEFT :
                        $query_builder->leftJoin(
                            $this->get_alias($class::get_table_name()),
                            $data_class_name::get_table_name(),
                            $this->get_alias($data_class_name::get_table_name()),
                            $join_condition);
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
                ConditionTranslator::render($condition, $this->get_alias($this->prepare_table_name($class))));
        }

        return $query_builder;
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
