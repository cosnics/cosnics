<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor;
use Chamilo\Libraries\Storage\DataManager\Doctrine\QueryBuilder;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Exception;
use PDO;
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
 */
class DataClassDatabase implements DataClassDatabaseInterface
{
    use ClassContext;

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
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor
     */
    protected $parametersProcessor;

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
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor $parametersProcessor
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Processor\RecordProcessor $recordProcessor
     */
    public function __construct(
        Connection $connection, StorageAliasGenerator $storageAliasGenerator, ExceptionLoggerInterface $exceptionLogger,
        ConditionPartTranslatorService $conditionPartTranslatorService, ParametersProcessor $parametersProcessor,
        RecordProcessor $recordProcessor = null
    )
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->parametersProcessor = $parametersProcessor;
        $this->recordProcessor = $recordProcessor;
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function buildBasicRecordsSql($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $parameters, $dataClassName
        );

        return $queryBuilder->getSQL();
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function buildRecordsSql($dataClassName, RecordRetrievesParameters $parameters)
    {
        if (!$parameters->getDataClassProperties() instanceof DataClassProperties)
        {
            return $this->buildRetrievesSql($dataClassName, $parameters);
        }
        else
        {
            return $this->buildBasicRecordsSql($dataClassName, $parameters);
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function buildRetrievesSql($dataClassName, DataClassRetrievesParameters $parameters)
    {
        return $this->buildBasicRecordsSql(
            $dataClassName,
            $this->getParametersProcessor()->handleDataClassRetrievesParameters($dataClassName, $parameters)
        );
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function count($dataClassName, DataClassCountParameters $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassCountParameters($parameters),
            $dataClassName
        );

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if (!$statement instanceof PDOException)
        {
            $record = $statement->fetch(PDO::FETCH_NUM);

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
     *
     * @return integer[]|false
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function countGrouped($dataClassName, DataClassCountGroupedParameters $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassCountGroupedParameters($parameters),
            $dataClassName
        );

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if (!$statement instanceof PDOException)
        {
            $counts = array();

            while ($record = $statement->fetch(PDO::FETCH_NUM))
            {
                $counts[$record[0]] = $record[1];
            }

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
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @param boolean $autoAssignIdentifier
     *
     * @return boolean
     * @throws \ReflectionException
     */
    public function create(DataClass $dataClass, $autoAssignIdentifier = true)
    {
        if ($dataClass instanceof CompositeDataClass)
        {
            $parentClass = $dataClass->parent_class_name();
            $objectTableName = $parentClass::get_table_name();
        }
        else
        {
            $objectTableName = $dataClass->get_table_name();
        }

        $objectProperties = $dataClass->getDefaultProperties();

        if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->get_default_property_names()))
        {
            $objectProperties[DataClass::PROPERTY_ID] = null;
            unset($objectProperties[DataClass::PROPERTY_ID]);
        }

        try
        {
            $this->getConnection()->insert($objectTableName, $objectProperties);

            if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->get_default_property_names()))
            {
                $dataClass->setId($this->getConnection()->lastInsertId($objectTableName));
            }

            if ($dataClass instanceof CompositeDataClass && $dataClass->is_extended())
            {
                $objectProperties = $dataClass->get_additional_properties();
                $objectProperties[DataClass::PROPERTY_ID] = $dataClass->getId();

                $this->getConnection()->insert($dataClass->get_table_name(), $objectProperties);
            }

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param string[] $record
     *
     * @return boolean
     */
    public function createRecord($dataClassName, $record)
    {
        try
        {
            $this->getConnection()->insert($dataClassName::get_table_name(), $record);
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }

        return true;
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function delete($dataClassName, Condition $condition = null)
    {
        $queryBuilder = new QueryBuilder($this->getConnection());
        $queryBuilder->delete($dataClassName::get_table_name(), $this->getAlias($dataClassName::get_table_name()));

        if (isset($condition))
        {
            $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition));
        }

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if (!$statement instanceof PDOException)
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
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]|false
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function distinct($dataClassName, DataClassDistinctParameters $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassDistinctParameters($parameters),
            $dataClassName
        );

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if (!$statement instanceof PDOException)
        {
            $distinctElements = array();

            while ($record = $statement->fetch(PDO::FETCH_ASSOC))
            {
                if (count($record) > 1)
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
     * @param string $text
     *
     * @return string
     */
    protected function escape($text)
    {
        if (!is_null($text))
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
     * @param string $columnName
     * @param string $storageUnitAlias
     *
     * @return string
     */
    public function escapeColumnName($columnName, $storageUnitAlias = null)
    {
        if (!empty($storageUnitAlias))
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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    protected function fetchRecord($dataClassName, DataClassRetrieveParameters $parameters)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $parameters, $dataClassName
        );

        $sqlQuery = $queryBuilder->getSQL();

        $statement = $this->getConnection()->query($sqlQuery);

        if (!$statement instanceof PDOException)
        {
            $record = $statement->fetch(PDO::FETCH_ASSOC);
        }
        else
        {
            $this->handleError($statement);
            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }

        if ($record instanceof PDOException)
        {
            $this->handleError($record);
            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }

        if (is_null($record) || !is_array($record) || empty($record))
        {
            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }

        return $this->processRecord($record);
    }

    /**
     *
     * @param \Doctrine\DBAL\Driver\Statement $statement
     *
     * @return string[][]
     */
    protected function fetchRecords(Statement $statement)
    {
        $records = array();

        while ($record = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $records[] = $this->processRecord($record);
        }

        return $records;
    }

    /**
     * @param string $dataClassStorageUnitName
     *
     * @return string
     */
    public function getAlias($dataClassStorageUnitName)
    {
        return $this->getStorageAliasGenerator()->getTableAlias($dataClassStorageUnitName);
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
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
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
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor
     */
    public function getParametersProcessor()
    {
        return $this->parametersProcessor;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor $parametersProcessor
     */
    public function setParametersProcessor(ParametersProcessor $parametersProcessor)
    {
        $this->parametersProcessor = $parametersProcessor;
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
     * @param string $sql
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getRecordsResult($sql, $dataClassName, $parameters)
    {
        try
        {
            return $this->getConnection()->query($sql);
        }
        catch (PDOException $exception)
        {
            $this->handleError($exception);
            throw new DataClassNoResultException($dataClassName, $parameters, $sql);
        }
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
     * @param \Exception $exception
     */
    protected function handleError(Exception $exception)
    {
        $this->getExceptionLogger()->logException(
            new Exception('[Message: ' . $exception->getMessage() . '] [Information: {USER INFO GOES HERE}]')
        );
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
     * @param string $dataClassName
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function prepareTableName($dataClassName)
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class) &&
            get_parent_class($dataClassName) == CompositeDataClass::class)
        {
            $tableName = $dataClassName::get_table_name();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && $dataClassName::is_extended())
        {
            $tableName = $dataClassName::get_table_name();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && !$dataClassName::is_extended())
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
     * Processes a given record by transforming to the correct type
     *
     * @param mixed[] $record
     *
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
     * @param string $value
     * @param string $type
     *
     * @return string
     */
    public function quote($value, $type = null)
    {
        return $this->getConnection()->quote($value, $type);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function record($dataClassName, RecordRetrieveParameters $parameters)
    {
        if (!$parameters->getDataClassProperties() instanceof DataClassProperties)
        {
            return $this->retrieve($dataClassName, $parameters);
        }
        else
        {
            return $this->fetchRecord($dataClassName, $parameters);
        }
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function records($dataClassName, RecordRetrievesParameters $parameters)
    {
        $statement = $this->getRecordsResult(
            $this->buildRecordsSql($dataClassName, $parameters), $dataClassName, $parameters
        );

        return $this->fetchRecords($statement);
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function retrieve($dataClassName, DataClassRetrieveParameters $parameters)
    {
        return $this->fetchRecord(
            $dataClassName,
            $this->getParametersProcessor()->handleDataClassRetrieveParameters($dataClassName, $parameters)
        );
    }

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function retrieves($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $statement = $this->getRecordsResult(
            $this->buildRetrievesSql($dataClassName, $parameters), $dataClassName, $parameters
        );

        return $this->fetchRecords($statement);
    }

    /**
     *
     * @param mixed $function
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactional($function)
    {
        // Rather than directly using Doctrine's version of transactional, we implement
        // an intermediate function that throws an exception if the function returns #f.
        // This mediates between Chamilo's convention of returning #f to signal failure
        // versus Doctrine's use of Exceptions.
        $throwOnFalse = function ($connection) use ($function) {
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
            return $this->getConnection()->transactional($throwOnFalse);
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translateCondition(Condition $condition, bool $enableAliasing = true)
    {
        return $this->getConditionPartTranslatorService()->translate($this, $condition, $enableAliasing);
    }

    /**
     * @param string $dataClassStorageUnitName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string[] $propertiesToUpdate
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update($dataClassStorageUnitName, Condition $condition, $propertiesToUpdate)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->update($dataClassStorageUnitName);

        foreach ($propertiesToUpdate as $key => $value)
        {
            $queryBuilder->set($key, $this->escape($value));
        }

        $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition, false));

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if ($statement instanceof PDOException)
        {
            $this->handleError($statement);

            return false;
        }

        return true;
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updates($dataClassName, DataClassProperties $properties, Condition $condition)
    {
        if (count($properties->get()) > 0)
        {
            $conditionPartTranslatorService = $this->getConditionPartTranslatorService();
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->update($dataClassName::get_table_name());

            foreach ($properties->get() as $dataClassProperty)
            {
                $queryBuilder->set(
                    $conditionPartTranslatorService->translate(
                        $this, $dataClassProperty->get_property(), false
                    ), $conditionPartTranslatorService->translate(
                    $this, $dataClassProperty->get_value(), false
                )
                );
            }

            $queryBuilder->where($conditionPartTranslatorService->translate($this, $condition, false));

            $statement = $this->getConnection()->query($queryBuilder->getSQL());

            if (!$statement instanceof PDOException)
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
}
