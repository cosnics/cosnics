<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Database;

use ADOConnection;
use ADORecordSet;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Processor\RecordProcessor;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ParametersProcessor;
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
use Exception;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class DataClassDatabase implements DataClassDatabaseInterface
{
    use ClassContext;

    /**
     *
     * @var \ADOConnection
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
     * @var \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService
     */
    protected $conditionPartTranslatorService;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ParametersProcessor
     */
    protected $parametersProcessor;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\AdoDb\Processor\RecordProcessor
     */
    protected $recordProcessor;

    /**
     *
     * @param \ADOConnection $connection
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ParametersProcessor $parametersProcessor
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Processor\RecordProcessor $recordProcessor
     */
    public function __construct(
        ADOConnection $connection, StorageAliasGenerator $storageAliasGenerator,
        ExceptionLoggerInterface $exceptionLogger, ConditionPartTranslatorService $conditionPartTranslatorService,
        ParametersProcessor $parametersProcessor, RecordProcessor $recordProcessor = null
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
        $queryBuilder = new QueryBuilder();

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
     * @throws \Exception
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
     * @throws \Exception
     */
    protected function buildRetrievesSql($dataClassName, DataClassRetrievesParameters $parameters)
    {
        return $this->buildBasicRecordsSql(
            $dataClassName,
            $this->getParametersProcessor()->handleDataClassRetrievesParameters($dataClassName, $parameters)
        );
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return boolean|integer
     * @throws \ReflectionException
     */
    public function count($dataClassName, DataClassCountParameters $parameters)
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassCountParameters($parameters),
            $dataClassName
        );

        $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

        if ($statement instanceof ADORecordSet)
        {
            $record = $statement->FetchRow();

            return (int) array_shift($record);
        }
        else
        {
            $this->handleError(new Exception('Count Failed. Query: ' . $queryBuilder->getSQL()));

            return false;
        }
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return boolean|integer[]
     * @throws \ReflectionException
     */
    public function countGrouped($dataClassName, DataClassCountGroupedParameters $parameters)
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassCountGroupedParameters($parameters),
            $dataClassName
        );

        $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

        if ($statement instanceof ADORecordSet)
        {
            $counts = array();
            while ($record = $statement->FetchRow())
            {
                $counts[array_shift($record)] = array_pop($record);
            }

            $record = $statement->FetchRow();

            return $counts;
        }
        else
        {
            $this->handleError(new Exception('Count Grouped Failed. Query: ' . $queryBuilder->getSQL()));

            return false;
        }
    }

    /**
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
            $objectTableName = $dataClass->get_table_name();
        }
        else
        {
            $objectTableName = $dataClass->get_table_name();
        }

        $objectProperties = $dataClass->getDefaultProperties();

        if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->get_default_property_names()))
        {
            $objectProperties[DataClass::PROPERTY_ID] = null;
        }

        try
        {
            $insertSql = $this->getConnection()->GetInsertSQL($objectTableName, $objectProperties);
            $result = $this->getConnection()->Execute($insertSql);

            if ($result === false)
            {
                throw new Exception('Insert object Failed. Query: ' . $insertSql);
            }

            if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->get_default_property_names()))
            {
                $dataClass->setId($this->getConnection()->Insert_ID($objectTableName));
            }

            if ($dataClass instanceof CompositeDataClass && $dataClass->is_extended())
            {
                $objectProperties = $dataClass->get_additional_properties();
                $objectProperties[DataClass::PROPERTY_ID] = $dataClass->getId();

                $tableName = $dataClass->get_table_name();
                $insertSql = $this->getConnection()->GetInsertSQL($tableName, $objectProperties);
                $result = $this->getConnection()->Execute($insertSql);

                if ($result === false)
                {
                    throw new Exception('Insert Failed. Query: ' . $insertSql);
                }
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
     * @param string $dataClassName
     * @param string[] $record
     *
     * @return boolean
     */
    public function createRecord($dataClassName, $record)
    {
        try
        {
            $insertSql = $this->getConnection()->GetInsertSQL($dataClassName::get_table_name(), $record);
            $result = $this->getConnection()->Execute($insertSql);

            if ($result === false)
            {
                throw new Exception('Insert record Failed. Query: ' . $insertSql);
            }
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return boolean
     */
    public function delete($dataClassName, Condition $condition = null)
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->delete($dataClassName::get_table_name(), $this->getAlias($dataClassName::get_table_name()));

        if (isset($condition))
        {
            $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition));
        }

        $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

        if ($statement === false)
        {
            $this->handleError(new Exception('Delete Failed. Query: ' . $queryBuilder->getSQL()));

            return false;
        }

        return true;
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]|boolean
     * @throws \ReflectionException
     */
    public function distinct($dataClassName, DataClassDistinctParameters $parameters)
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassDistinctParameters($parameters),
            $dataClassName
        );

        $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

        if ($statement instanceof ADORecordSet)
        {
            $distinctElements = array();

            while ($record = $statement->FetchRow())
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
            $this->handleError(new Exception('Distinct Failed. Query: ' . $queryBuilder->getSQL()));

            return false;
        }
    }

    /**
     *
     * @param string $text
     * @param boolean $escapeWildcards
     *
     * @return string
     */
    protected function escape($text, $escapeWildcards = false)
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
     * @throws \ReflectionException
     */
    protected function fetchRecord($dataClassName, DataClassRetrieveParameters $parameters)
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->processParameters(
            $this, $queryBuilder, $parameters, $dataClassName
        );

        $sqlQuery = $queryBuilder->getSQL();

        /**
         *
         * @var \ADORecordSet $statement
         */
        $statement = $this->getConnection()->SelectLimit($sqlQuery, $parameters->getCount(), $parameters->getOffset());

        if ($statement instanceof ADORecordSet)
        {
            $record = $statement->FetchRow();
        }
        else
        {
            $this->handleError(new Exception('No record found. Query: ' . $sqlQuery));
            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }

        if ($record === false)
        {
            $this->handleError(new Exception('No record found. Query: ' . $sqlQuery));
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
     * @param \ADORecordSet $statement
     *
     * @return string[][]
     */
    protected function fetchRecords(ADORecordSet $statement)
    {
        $records = array();

        while ($record = $statement->FetchRow())
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
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService
     */
    public function getConditionPartTranslatorService()
    {
        return $this->conditionPartTranslatorService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService $conditionPartTranslatorService
     */
    public function setConditionPartTranslatorService(ConditionPartTranslatorService $conditionPartTranslatorService)
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
    }

    /**
     *
     * @return \ADOConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     *
     * @param \ADOConnection $connection
     */
    public function setConnection(ADOConnection $connection)
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
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ParametersProcessor
     */
    public function getParametersProcessor()
    {
        return $this->parametersProcessor;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ParametersProcessor $parametersProcessor
     */
    public function setParametersProcessor(ParametersProcessor $parametersProcessor)
    {
        $this->parametersProcessor = $parametersProcessor;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Processor\RecordProcessor
     */
    public function getRecordProcessor()
    {
        return $this->recordProcessor;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\Processor\RecordProcessor $recordProcessor
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
     * @return \ADORecordSet
     * @throws DataClassNoResultException
     */
    protected function getRecordsResult($sql, $dataClassName, $parameters)
    {
        $recordSet = $this->getConnection()->SelectLimit($sql, $parameters->getCount(), $parameters->getOffset());

        if ($recordSet === false)
        {
            $this->handleError(new Exception('No Records Found. Query: ' . $sql));
            throw new DataClassNoResultException($dataClassName, $parameters, $sql);
        }
        else
        {
            return $recordSet;
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
     * @param string $value
     * @param string $type
     *
     * @return string
     */
    public function quote($value, $type = null)
    {
        return $this->getConnection()->Quote($value);
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function records($dataClassName, RecordRetrievesParameters $parameters)
    {
        $statement = $this->getRecordsResult(
            $this->buildRecordsSql($dataClassName, $parameters), $dataClassName, $parameters
        );

        return $this->fetchRecords($statement);
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function retrieve($dataClassName, DataClassRetrieveParameters $parameters)
    {
        return $this->fetchRecord(
            $dataClassName,
            $this->getParametersProcessor()->handleDataClassRetrieveParameters($dataClassName, $parameters)
        );
    }

    /**
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return string[]|\string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function retrieves($dataClassName, DataClassRetrievesParameters $parameters)
    {
        $statement = $this->getRecordsResult(
            $this->buildRetrievesSql($dataClassName, $parameters), $dataClassName, $parameters
        );

        return $this->fetchRecords($statement);
    }

    /**
     * @param mixed $function
     *
     * @return mixed
     */
    public function transactional($function)
    {
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
            $this->getConnection()->StartTrans();

            try
            {
                $throwOnFalse();
                $this->getConnection()->CompleteTrans();
            }
            catch (Exception $e)
            {
                $this->getConnection()->RollbackTrans();
                throw $e;
            }

            return true;
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
     * @throws \Exception
     */
    public function update($dataClassStorageUnitName, Condition $condition, $propertiesToUpdate)
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->update($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));

        foreach ($propertiesToUpdate as $key => $value)
        {
            $queryBuilder->set($key, $this->escape($value));
        }

        if ($condition instanceof Condition)
        {
            $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition));
        }
        else
        {
            throw new Exception('Cannot update records without a condition');
        }

        $result = $this->getConnection()->Execute($queryBuilder->getSQL());

        if ($result === false)
        {
            $this->handleError(new Exception('Update Failed. Query: ' . $queryBuilder->getSQL()));

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
     * @throws \Exception
     */
    public function updates($dataClassName, DataClassProperties $properties, Condition $condition = null)
    {
        if (count($properties->get()) > 0)
        {
            $queryBuilder = new QueryBuilder();
            $queryBuilder->update($dataClassName::get_table_name(), $this->getAlias($dataClassName::get_table_name()));

            foreach ($properties->get() as $dataClassProperty)
            {
                $queryBuilder->set(
                    $this->getConditionPartTranslatorService()->translate(
                        $this, $dataClassProperty->get_property()
                    ), $this->getConditionPartTranslatorService()->translate(
                    $this, $dataClassProperty->get_value()
                )
                );
            }

            if ($condition)
            {
                $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition));
            }
            else
            {
                throw new Exception('Cannot update records without a condition');
            }

            $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

            if ($statement === false)
            {
                $this->handleError(new Exception('Insert Failed. Query: ' . $queryBuilder->getSQL()));

                return false;
            }
        }

        return true;
    }
}
