<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Doctrine\QueryBuilder;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
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
use Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor;

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
class DataClassDatabase implements DataClassDatabaseInterface
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
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService
     */
    protected $conditionPartTranslatorService;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor
     */
    protected $recordProcessor;

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor $recordProcessor
     */
    public function __construct(\Doctrine\DBAL\Connection $connection, StorageAliasGenerator $storageAliasGenerator,
        ExceptionLoggerInterface $exceptionLogger, ConditionPartTranslatorService $conditionPartTranslatorService,
        RecordProcessor $recordProcessor = null)
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->recordProcessor = $recordProcessor;
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
    public function setConnection(\Doctrine\DBAL\Connection $connection)
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
    public function setStorageAliasGenerator(StorageAliasGenerator $storageAliasGenerator)
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
    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService
     */
    public function getConditionPartTranslatorService()
    {
        return $this->conditionPartTranslatorService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     */
    public function setConditionPartTranslatorService(ConditionPartTranslatorService $conditionPartTranslatorService)
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor
     */
    public function getRecordProcessor()
    {
        return $this->recordProcessor;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor $recordProcessor
     */
    public function setRecordProcessor(RecordProcessor $recordProcessor)
    {
        $this->recordProcessor = $recordProcessor;
    }

    /**
     *
     * @param \Exception $exception
     */
    protected function handleError(\Exception $exception)
    {
        $this->getExceptionLogger()->logException(
            '[Message: ' . $exception->getMessage() . '] [Information: {USER INFO GOES HERE}]');
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
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
     * @param string $dataClassName
     * @param string[] $record
     * @return boolean
     */
    public function createRecord($dataClassName, $record)
    {
        try
        {
            $this->getConnection()->insert($dataClassName::get_table_name(), $record);
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
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return string[]
     */
    public function retrieve($dataClassName, $parameters = null)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->addSelect($this->getAlias($this->prepareTableName($dataClassName)) . '.*');

        $this->processCompositeDataClassJoins($queryBuilder, $dataClassName, $parameters);

        return $this->fetchRecord($queryBuilder, $dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\DataClassResultSet
     */
    public function retrieves($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $statement = $this->getRecordsResult(
            $this->buildRetrievesSql($dataClassName, $parameters),
            $dataClassName,
            $parameters);

        return $this->fetchRecords($statement);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return string[]
     */
    public function record($dataClassName, $parameters = null)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $groupBy = $parameters->get_group_by();

        if ($groupBy instanceof GroupBy)
        {
            foreach ($groupBy->get_group_by() as $groupByVariable)
            {
                $queryBuilder->addGroupBy(
                    $this->getConditionPartTranslatorService()->translateConditionVariable($this, $groupByVariable));
            }
        }

        if ($parameters->get_properties() instanceof DataClassProperties)
        {
            foreach ($parameters->get_properties()->get() as $conditionVariable)
            {
                $queryBuilder->addSelect(
                    $this->getConditionPartTranslatorService()->translateConditionVariable($this, $conditionVariable));
            }
        }
        else
        {

            return $this->retrieve($dataClassName, $parameters);
        }

        return $this->fetchRecord($queryBuilder, $dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\RecordResultSet
     */
    public function records($dataClassName, RecordRetrievesParameters $parameters)
    {
        $statement = $this->getRecordsResult(
            $this->buildRecordsSql($dataClassName, $parameters),
            $dataClassName,
            $parameters);

        return $this->fetchRecords($statement);
    }

    /**
     *
     * @param string $dataClassStorageUnitName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string[] $propertiesToUpdate
     * @throws Exception
     * @return boolean
     */
    public function update($dataClassStorageUnitName, Condition $condition, $propertiesToUpdate)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->update($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));

        foreach ($propertiesToUpdate as $key => $value)
        {
            $queryBuilder->set($key, $this->escape($value));
        }

        if ($condition instanceof Condition)
        {
            $queryBuilder->where($this->getConditionPartTranslatorService()->translateCondition($this, $condition));
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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @throws Exception
     * @return boolean
     */
    public function updates($dataClassName, DataClassProperties $properties, $condition)
    {
        if (count($properties->get()) > 0)
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();
            $queryBuilder->update($dataClassName::get_table_name(), $this->get_alias($dataClassName::get_table_name()));

            foreach ($properties->get() as $dataClassProperty)
            {
                $queryBuilder->set(
                    $this->getConditionPartTranslatorService()->translateConditionVariable(
                        $this,
                        $dataClassProperty->get_property()),
                    $this->getConditionPartTranslatorService()->translateConditionVariable(
                        $this,
                        $dataClassProperty->get_value()));
            }

            if ($condition)
            {
                $queryBuilder->where($this->getConditionPartTranslatorService()->translateCondition($this, $condition));
            }
            else
            {
                throw new Exception('Cannot update records without a condition');
            }

            $statement = $this->getConnection()->query($queryBuilder->getSQL());

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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return boolean
     */
    public function delete($dataClassName, $condition)
    {
        $queryBuilder = new QueryBuilder($this->getConnection());
        $queryBuilder->delete($dataClassName::get_table_name(), $this->getAlias($dataClassName::get_table_name()));

        if (isset($condition))
        {
            $queryBuilder->where($this->getConditionPartTranslatorService()->translateCondition($this, $condition));
        }

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @return integer
     */
    public function count($dataClassName, $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        if ($parameters->get_property() instanceof ConditionVariable)
        {
            $property = $this->getConditionPartTranslatorService()->translateConditionVariable(
                $this,
                $parameters->get_property());
        }
        else
        {
            $property = '1';
        }

        $queryBuilder->addSelect('COUNT(' . $property . ')');
        $queryBuilder->from(
            $this->prepareTableName($dataClassName),
            $this->getAlias($this->prepareTableName($dataClassName)));

        $queryBuilder = $this->processParameters($queryBuilder, $dataClassName, $parameters);

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @return integer
     */
    public function countGrouped($dataClassName, $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        foreach ($parameters->get_property()->get() as $property)
        {
            $queryBuilder->addSelect(
                $this->getConditionPartTranslatorService()->translateConditionVariable($this, $property));
        }

        $queryBuilder->addSelect('COUNT(1)');
        $queryBuilder->from($dataClassName::get_table_name(), $this->getAlias($dataClassName::get_table_name()));

        $queryBuilder = $this->processParameters($queryBuilder, $dataClassName, $parameters);

        foreach ($parameters->get_property()->get() as $property)
        {
            $queryBuilder->addGroupBy(
                $this->getConditionPartTranslatorService()->translateConditionVariable($this, $property));
        }

        $queryBuilder->having(
            $this->getConditionPartTranslatorService()->translateCondition($this, $parameters->get_having()));
        $statement = $this->getConnection()->query($queryBuilder->getSQL());

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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     * @return string[]
     */
    public function distinct($dataClassName, DataClassDistinctParameters $parameters)
    {
        $properties = $parameters->get_property();

        if (! is_array($properties))
        {
            $properties = array($properties);
        }

        $select = array();

        foreach ($properties as $property)
        {
            $select[] = $this->escapeColumnName($property, $this->getAlias($dataClassName::get_table_name()));
        }

        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->addSelect('DISTINCT ' . implode(',', $select));
        $queryBuilder->from($dataClassName::get_table_name(), $this->getAlias($dataClassName::get_table_name()));

        $queryBuilder = $this->processParameters($queryBuilder, $dataClassName, $parameters);
        $queryBuilder = $this->processOrderBy($queryBuilder, $parameters->getOrderBy());

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if (! $statement instanceof \PDOException)
        {
            $distinctElements = array();
            while ($record = $statement->fetch(\PDO::FETCH_ASSOC))
            {
                if (count($properties) > 1)
                {
                    $distinctElements[] = $record;
                }
                else
                {
                    $distinctElements[] = array_pop($record);
                }
            }
            return $distinctElements;
        }
        else
        {
            $this->handleError($statement);
            return false;
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return integer
     */
    public function retrieveMaximumValue($dataClassName, $property, $condition = null)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->addSelect(
            'MAX(' . $this->escapeColumnName($property, $this->getAlias($dataClassName::get_table_name())) . ') AS ' .
                 self::ALIAS_MAX_SORT);
        $queryBuilder->from($dataClassName::get_table_name(), $this->getAlias($dataClassName::get_table_name()));

        if (isset($condition))
        {
            $queryBuilder->where(
                $this->getConditionPartTranslatorService()->translateCondition(
                    $this,
                    $condition,
                    $this->getAlias($dataClassName::get_table_name())));
        }

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

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
     * @param \Chamilo\Libraries\Storage\DataClass\CompositeDataClass $compositeDataClass
     * @return string[]
     */
    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $compositeDataClass)
    {
        if (! $compositeDataClass->is_extended())
        {
            return array();
        }

        $array = $compositeDataClass->get_additional_property_names();

        if (count($array) == 0)
        {
            $array = array("*");
        }

        $query = 'SELECT ' . implode(',', $array) . ' FROM ' . $object::get_table_name() . ' WHERE ' .
             $object::PROPERTY_ID . '=' . $this->quote($compositeDataClass->getId());

        $statement = $this->getConnection()->query($query);

        if (! $statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_ASSOC);
            $additionalProperties = array();

            if (is_array($record))
            {
                $properties = $compositeDataClass->get_additional_property_names();

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

                        $additionalProperties[$prop] = $record[$prop];
                    }
                }
            }
            return $additionalProperties;
        }
        else
        {
            $this->handleError($statement);
            return false;
        }
    }

    /**
     *
     * @param mixed $function
     * @throws Exception
     * @return mixed
     */
    public function transactional($function)
    {
        // Rather than directly using Doctrine's version of transactional, we implement
        // an intermediate function that throws an exception if the function returns #f.
        // This mediates between Chamilo's convention of returning #f to signal failure
        // versus Doctrine's use of Exceptions.
        $throwOnFalse = function ($connection) use ($function)
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
            $this->getConnection()->transactional($throwOnFalse);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     *
     * @param string $dataClassStorageUnitName
     * @return string
     */
    public function getAlias($dataClassStorageUnitName)
    {
        return $this->getStorageAliasGenerator()->get_table_alias($dataClassStorageUnitName);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return string
     */
    public function translateCondition(Condition $condition = null)
    {
        return $this->getConditionPartTranslatorService()->translateCondition($this, $condition);
    }

    /**
     *
     * @param string $value
     * @param string $type
     * @param boolean $quote
     * @param boolean $escapeWildcards
     *
     * @return string
     */
    public function quote($value, $type = null, $quote = true, $escapeWildcards = false)
    {
        return $this->getConnection()->quote($value, $type, $quote, $escapeWildcards);
    }

    /**
     *
     * @param string $columnName
     * @param string $storageUnitAlias
     * @return string
     */
    public function escapeColumnName($columnName, $storageUnitAlias = null)
    {
        if (! empty($storageUnitAlias))
        {
            return $storageUnitAlias . '.' . $columnName;
        }
        else
        {
            return $columnName;
        }
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     */
    protected function processCompositeDataClassJoins($queryBuilder, $dataClassName, $parameters)
    {
        if ($parameters->get_joins() instanceof Joins)
        {
            foreach ($parameters->get_joins()->get() as $join)
            {
                if (is_subclass_of($join->get_data_class(), CompositeDataClass::class_name()))
                {
                    if (is_subclass_of($dataClassName, $join->get_data_class()))
                    {
                        $joinClassName = $join->get_data_class();
                        $alias = $this->getAlias($joinClassName::get_table_name());
                        $queryBuilder->addSelect($alias . '.*');
                    }
                }
            }
        }
    }

    /**
     *
     * @param string $sql
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @throws DataClassNoResultException
     * @return \Doctrine\DBAL\Driver\Statement
     */
    protected function getRecordsResult($sql, $dataClassName, $parameters)
    {
        try
        {
            return $this->getConnection()->query($sql);
        }
        catch (\PDOException $exception)
        {
            $this->handleError($exception);
            throw new DataClassNoResultException($dataClassName, $parameters, $sql);
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return string
     */
    protected function buildRetrievesSql($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $select = $this->getAlias($this->prepareTableName($dataClassName)) . '.*';

        if ($parameters->get_distinct())
        {
            $select = 'DISTINCT ' . $select;
        }

        $queryBuilder->addSelect($select);

        $this->processCompositeDataClassJoins($queryBuilder, $dataClassName, $parameters);

        return $this->buildBasicRecordsSql($queryBuilder, $dataClassName, $parameters);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     * @return string
     */
    protected function buildRecordsSql($dataClassName, RecordRetrievesParameters $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder = $this->processDataClassProperties($queryBuilder, $dataClassName, $parameters->get_properties());
        $queryBuilder = $this->processGroupBy($queryBuilder, $parameters->get_group_by());

        return $this->buildBasicRecordsSql($queryBuilder, $dataClassName, $parameters);
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return string
     */
    protected function buildBasicRecordsSql($queryBuilder, $dataClassName, $parameters)
    {
        $queryBuilder->from(
            $this->prepareTableName($dataClassName),
            $this->getAlias($this->prepareTableName($dataClassName)));
        $queryBuilder = $this->processParameters($queryBuilder, $dataClassName, $parameters);
        $queryBuilder = $this->processOrderBy($queryBuilder, $parameters->get_order_by());
        $queryBuilder = $this->processLimit($queryBuilder, $parameters->get_count(), $parameters->get_offset());
        return $queryBuilder->getSQL();
    }

    /**
     *
     * @param string $dataClassName
     * @return string
     */
    protected function prepareTableName($dataClassName)
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class_name()) &&
             get_parent_class($dataClassName) == CompositeDataClass::class_name())
        {
            $tableName = $dataClassName::get_table_name();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class_name()) && $dataClassName::is_extended())
        {
            $tableName = $dataClassName::get_table_name();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class_name()) && ! $dataClassName::is_extended())
        {
            $parent = $dataClassName::parent_class_name();
            $tableName = $parent::get_table_name();
        }
        else
        {
            $tableName = $dataClassName::get_table_name();
        }

        return $tableName;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @throws DataClassNoResultException
     * @return string[]
     */
    protected function fetchRecord($queryBuilder, $dataClassName, $parameters)
    {
        $queryBuilder->from(
            $this->prepareTableName($dataClassName),
            $this->getAlias($this->prepareTableName($dataClassName)));

        $queryBuilder = $this->processParameters($queryBuilder, $dataClassName, $parameters);
        $queryBuilder = $this->processOrderBy($queryBuilder, $parameters->get_order_by());

        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults(1);

        $sqlQuery = $queryBuilder->getSQL();

        $statement = $this->getConnection()->query($sqlQuery);

        if (! $statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_ASSOC);
        }
        else
        {
            $this->handleError($statement);
            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }

        if ($record instanceof \PDOException)
        {
            $this->handleError($record);
            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }

        if (is_null($record) || ! is_array($record) || empty($record))
        {
            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }

        return $this->processRecord($record);
    }

    /**
     *
     * @param \Doctrine\DBAL\Driver\Statement $statement
     * @return string[]
     */
    protected function fetchRecords(\Doctrine\DBAL\Driver\Statement $statement)
    {
        $records = array();

        while ($record = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $records[] = $this->processRecord($record);
        }

        return $records;
    }

    /**
     * Processes a given record by transforming to the correct type
     *
     * @param mixed[] $record
     * @return mixed[]
     */
    protected function processRecord($record)
    {
        if ($this->getRecordProcessor() instanceof RecordProcessor)
        {
            return $this->getRecordProcessor()->processRecord($record);
        }

        return $record;
    }

    /**
     *
     * @param string $text
     * @param boolean $escape_wildcards
     * @return string
     */
    protected function escape($text, $escapeWildcards = false)
    {
        if (! is_null($text))
        {
            return $this->quote($text);
        }
        else
        {
            return 'NULL';
        }
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processOrderBy($queryBuilder, $orderBy)
    {
        if (is_null($orderBy))
        {
            $orderBy = array();
        }
        elseif (! is_array($orderBy))
        {
            $orderBy = array($orderBy);
        }

        foreach ($orderBy as $order)
        {
            $queryBuilder->addOrderBy(
                $this->getConditionPartTranslatorService()->translateConditionVariable($this, $order->get_property()),
                ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC'));
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param int $count
     * @param int $offset
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processLimit($queryBuilder, $count = null, $offset = null)
    {
        if (intval($count) > 0)
        {
            $queryBuilder->setMaxResults(intval($count));
        }

        if (intval($offset) > 0)
        {
            $queryBuilder->setFirstResult(intval($offset));
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processDataClassProperties($queryBuilder, $dataClassName, $properties)
    {
        if ($properties instanceof DataClassProperties)
        {
            foreach ($properties->get() as $conditionVariable)
            {
                $queryBuilder->addSelect(
                    $this->getConditionPartTranslatorService()->translateConditionVariable($this, $conditionVariable));
            }
        }
        else
        {
            $queryBuilder->addSelect($this->getAlias($dataClassName::get_table_name()) . '.*');
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processGroupBy($queryBuilder, $groupBy)
    {
        if ($groupBy instanceof GroupBy)
        {
            foreach ($groupBy->get() as $groupByVariable)
            {
                $queryBuilder->addGroupBy(
                    $this->getConditionPartTranslatorService()->translateConditionVariable($this, $groupByVariable));
            }
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Joins $joins
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processJoins($queryBuilder, $dataClassName, $joins)
    {
        if ($joins instanceof Joins)
        {
            foreach ($joins->get() as $join)
            {
                $joinCondition = $this->getConditionPartTranslatorService()->translateCondition(
                    $this,
                    $join->get_condition());
                $joinDataClassName = $join->get_data_class();

                switch ($join->get_type())
                {
                    case Join::TYPE_NORMAL :
                        $queryBuilder->join(
                            $this->getAlias($dataClassName::get_table_name()),
                            $joinDataClassName::get_table_name(),
                            $this->getAlias($joinDataClassName::get_table_name()),
                            $joinCondition);
                        break;
                    case Join::TYPE_RIGHT :
                        $queryBuilder->rightJoin(
                            $this->getAlias($dataClassName::get_table_name()),
                            $joinDataClassName::get_table_name(),
                            $this->getAlias($joinDataClassName::get_table_name()),
                            $joinCondition);
                        break;
                    case Join::TYPE_LEFT :
                        $queryBuilder->leftJoin(
                            $this->getAlias($dataClassName::get_table_name()),
                            $joinDataClassName::get_table_name(),
                            $this->getAlias($joinDataClassName::get_table_name()),
                            $joinCondition);
                        break;
                }
            }
        }
        return $queryBuilder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processCondition($queryBuilder, $dataClassName, $condition)
    {
        if ($condition instanceof Condition)
        {
            $queryBuilder->where($this->getConditionPartTranslatorService()->translateCondition($this, $condition));
        }

        return $queryBuilder;
    }

    /**
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function processParameters($queryBuilder, $dataClassName, $parameters)
    {
        $queryBuilder = $this->processJoins($queryBuilder, $dataClassName, $parameters->get_joins());
        $queryBuilder = $this->processCondition($queryBuilder, $dataClassName, $parameters->get_condition());
        return $queryBuilder;
    }

    /**
     *
     * @param string $dataClassName
     * @param string[] $record
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    protected function getDataClass($dataClassName, $record)
    {
        $baseClassName = (is_subclass_of($dataClassName, CompositeDataClass::class_name()) ? CompositeDataClass::class_name() : DataClass::class_name());
        $dataClassName = (is_subclass_of($dataClassName, CompositeDataClass::class_name()) ? $record[CompositeDataClass::PROPERTY_TYPE] : $dataClassName);
        return $baseClassName::factory($dataClassName, $record);
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
