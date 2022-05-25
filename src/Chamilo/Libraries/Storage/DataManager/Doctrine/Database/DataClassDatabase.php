<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Doctrine\QueryBuilder;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\RecordProcessor;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Types\Type;
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
 */
class DataClassDatabase implements DataClassDatabaseInterface
{
    use ClassContext;

    protected ConditionPartTranslatorService $conditionPartTranslatorService;

    protected Connection $connection;

    protected ExceptionLoggerInterface $exceptionLogger;

    protected ParametersProcessor $parametersProcessor;

    protected RecordProcessor $recordProcessor;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        Connection $connection, StorageAliasGenerator $storageAliasGenerator, ExceptionLoggerInterface $exceptionLogger,
        ConditionPartTranslatorService $conditionPartTranslatorService, ParametersProcessor $parametersProcessor,
        RecordProcessor $recordProcessor
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
     * @throws \ReflectionException
     */
    protected function buildBasicRecordsSql(string $dataClassName, DataClassParameters $parameters): string
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
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function buildRecordsSql(string $dataClassName, RecordRetrievesParameters $parameters): string
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
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function buildRetrievesSql(string $dataClassName, DataClassRetrievesParameters $parameters): string
    {
        return $this->buildBasicRecordsSql(
            $dataClassName,
            $this->getParametersProcessor()->handleDataClassRetrievesParameters($dataClassName, $parameters)
        );
    }

    public function count(string $dataClassName, DataClassCountParameters $parameters): int
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from(
                $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
            );

            $queryBuilder = $this->getParametersProcessor()->processParameters(
                $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassCountParameters($parameters),
                $dataClassName
            );

            $result = $this->getConnection()->executeQuery($queryBuilder->getSQL());
            $record = $result->fetchNumeric();

            return (int) $record[0];
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            // TODO: Do something more useful when DataClassDatabase::count() throws an error
            exit;
        }
    }

    /**
     * @return int[]
     */
    public function countGrouped(string $dataClassName, DataClassCountGroupedParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from(
                $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
            );

            $queryBuilder = $this->getParametersProcessor()->processParameters(
                $this, $queryBuilder,
                $this->getParametersProcessor()->handleDataClassCountGroupedParameters($parameters), $dataClassName
            );

            $result = $this->getConnection()->executeQuery($queryBuilder->getSQL());

            $counts = [];

            while ($record = $result->fetchNumeric())
            {
                $counts[$record[0]] = $record[1];
            }

            return $counts;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            // TODO: Do something more useful when DataClassDatabase::countGrouped() throws an error
            exit;
        }
    }

    public function create(DataClass $dataClass, ?bool $autoAssignIdentifier = true): bool
    {
        try
        {
            if ($dataClass instanceof CompositeDataClass)
            {
                $parentClass = $dataClass->parentClassName();
                $objectTableName = $parentClass::getTableName();
            }
            else
            {
                $objectTableName = $dataClass->getTableName();
            }

            $objectProperties = $dataClass->getDefaultProperties();

            if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->getDefaultPropertyNames()))
            {
                $objectProperties[DataClass::PROPERTY_ID] = null;
                unset($objectProperties[DataClass::PROPERTY_ID]);
            }

            $this->getConnection()->insert($objectTableName, $objectProperties);

            if ($autoAssignIdentifier && in_array(DataClass::PROPERTY_ID, $dataClass->getDefaultPropertyNames()))
            {
                $dataClass->setId($this->getConnection()->lastInsertId($objectTableName));
            }

            if ($dataClass instanceof CompositeDataClass && $dataClass->isExtended())
            {
                $objectProperties = $dataClass->getAdditionalProperties();
                $objectProperties[DataClass::PROPERTY_ID] = $dataClass->getId();

                $this->getConnection()->insert($dataClass->getTableName(), $objectProperties);
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
     * @param mixed[] $record
     */
    public function createRecord(string $dataClassName, array $record): bool
    {
        try
        {
            $this->getConnection()->insert($dataClassName::getTableName(), $record);

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    public function delete(string $dataClassName, ?Condition $condition = null): bool
    {
        try
        {
            $queryBuilder = new QueryBuilder($this->getConnection());
            $queryBuilder->delete($dataClassName::getTableName(), $this->getAlias($dataClassName::getTableName()));

            if (isset($condition))
            {
                $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition));
            }

            $this->getConnection()->executeQuery($queryBuilder->getSQL());

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    /**
     * @return string[]
     */
    public function distinct(string $dataClassName, DataClassDistinctParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from(
                $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
            );

            $queryBuilder = $this->getParametersProcessor()->processParameters(
                $this, $queryBuilder, $this->getParametersProcessor()->handleDataClassDistinctParameters($parameters),
                $dataClassName
            );

            $statement = $this->getConnection()->executeQuery($queryBuilder->getSQL());

            $distinctElements = [];

            while ($record = $statement->fetchAssociative())
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
        catch (Exception $exception)
        {
            $this->handleError($exception);

            // TODO: Do something more useful when DataClassDatabase::distinct() throws an error
            exit;
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

    public function escapeColumnName(string $columnName, ?string $storageUnitAlias = null): string
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
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function fetchRecord(string $dataClassName, DataClassParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from(
                $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
            );

            $queryBuilder = $this->getParametersProcessor()->processParameters(
                $this, $queryBuilder, $parameters, $dataClassName
            );

            $sqlQuery = $queryBuilder->getSQL();

            $statement = $this->getConnection()->executeQuery($sqlQuery);
            $record = $statement->fetchAssociative();

            if (!is_array($record) || empty($record))
            {
                throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
            }

            return $record;
        }

        catch (Exception $exception)
        {
            $this->handleError($exception);

            throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchRecords(Result $result): array
    {
        $records = [];

        while ($record = $result->fetchAssociative())
        {
            $records[] = $record;
        }

        return $records;
    }

    public function getAlias(string $dataClassStorageUnitName): string
    {
        return $this->getStorageAliasGenerator()->getTableAlias($dataClassStorageUnitName);
    }

    public function getConditionPartTranslatorService(): ConditionPartTranslatorService
    {
        return $this->conditionPartTranslatorService;
    }

    public function setConditionPartTranslatorService(ConditionPartTranslatorService $conditionPartTranslatorService
    ): DataClassDatabase
    {
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;

        return $this;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function setConnection(Connection $connection): DataClassDatabase
    {
        $this->connection = $connection;

        return $this;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger): DataClassDatabase
    {
        $this->exceptionLogger = $exceptionLogger;

        return $this;
    }

    public function getParametersProcessor(): ParametersProcessor
    {
        return $this->parametersProcessor;
    }

    public function setParametersProcessor(ParametersProcessor $parametersProcessor): DataClassDatabase
    {
        $this->parametersProcessor = $parametersProcessor;

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\RecordProcessor
     */
    public function getRecordProcessor(): RecordProcessor
    {
        return $this->recordProcessor;
    }

    public function setRecordProcessor(RecordProcessor $recordProcessor): DataClassDatabase
    {
        $this->recordProcessor = $recordProcessor;

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function getRecordsResult(string $sql, string $dataClassName, DataClassParameters $parameters): Result
    {
        try
        {
            return $this->getConnection()->executeQuery($sql);
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);
            throw new DataClassNoResultException($dataClassName, $parameters, $sql);
        }
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    public function setStorageAliasGenerator(StorageAliasGenerator $storageAliasGenerator): DataClassDatabase
    {
        $this->storageAliasGenerator = $storageAliasGenerator;

        return $this;
    }

    protected function handleError(Exception $exception)
    {
        $this->getExceptionLogger()->logException(
            new Exception('[Message: ' . $exception->getMessage() . ']')
        );
    }

    /**
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }

    protected function prepareTableName(string $dataClassName): string
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class) &&
            get_parent_class($dataClassName) == CompositeDataClass::class)
        {
            $tableName = $dataClassName::getTableName();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && $dataClassName::isExtended())
        {
            $tableName = $dataClassName::getTableName();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && !$dataClassName::isExtended())
        {
            $parent = $dataClassName::parentClassName();
            $tableName = $parent::getTableName();
        }
        else
        {
            $tableName = $dataClassName::getTableName();
        }

        return $tableName;
    }

    protected function processRecord(array $record): array
    {
        return $this->getRecordProcessor()->processRecord($record);
    }

    /**
     *
     * @param mixed $value
     * @param int|string|Type|null $type
     *
     * @return mixed
     */
    public function quote($value, ?string $type = null)
    {
        return $this->getConnection()->quote($value, $type);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     */
    public function record(string $dataClassName, RecordRetrieveParameters $parameters): array
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
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \ReflectionException
     */
    public function records(string $dataClassName, RecordRetrievesParameters $parameters): array
    {
        $statement = $this->getRecordsResult(
            $this->buildRecordsSql($dataClassName, $parameters), $dataClassName, $parameters
        );

        return $this->fetchRecords($statement);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters): array
    {
        return $this->fetchRecord(
            $dataClassName,
            $this->getParametersProcessor()->handleDataClassRetrieveParameters($dataClassName, $parameters)
        );
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): array
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
        try
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

            return $this->getConnection()->transactional($throwOnFalse);
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function translateCondition(Condition $condition, bool $enableAliasing = true): string
    {
        return $this->getConditionPartTranslatorService()->translate($this, $condition, $enableAliasing);
    }

    /**
     * @param string[] $propertiesToUpdate
     */
    public function update(string $dataClassStorageUnitName, Condition $condition, array $propertiesToUpdate): bool
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();
            $queryBuilder->update($dataClassStorageUnitName);

            foreach ($propertiesToUpdate as $key => $value)
            {
                $queryBuilder->set($key, $this->escape($value));
            }

            $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition, false));

            $this->getConnection()->executeQuery($queryBuilder->getSQL());

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    public function updates(string $dataClassName, DataClassProperties $properties, Condition $condition): bool
    {
        try
        {
            if (count($properties->get()) > 0)
            {
                $conditionPartTranslatorService = $this->getConditionPartTranslatorService();
                $queryBuilder = $this->getConnection()->createQueryBuilder();

                $queryBuilder->update($dataClassName::getTableName());

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

                $this->getConnection()->executeQuery($queryBuilder->getSQL());
            }

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }
}
