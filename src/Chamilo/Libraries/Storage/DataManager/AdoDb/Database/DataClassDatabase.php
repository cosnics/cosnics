<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Database;

use ADOConnection;
use ADORecordSet;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ParametersProcessor;
use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\RecordProcessor;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Doctrine\DBAL\Types\Type;
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

    protected ConditionPartTranslatorService $conditionPartTranslatorService;

    protected ADOConnection $connection;

    protected ExceptionLoggerInterface $exceptionLogger;

    protected ParametersProcessor $parametersProcessor;

    protected RecordProcessor $recordProcessor;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        ADOConnection $connection, StorageAliasGenerator $storageAliasGenerator,
        ExceptionLoggerInterface $exceptionLogger, ConditionPartTranslatorService $conditionPartTranslatorService,
        ParametersProcessor $parametersProcessor, RecordProcessor $recordProcessor
    )
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->parametersProcessor = $parametersProcessor;
        $this->recordProcessor = $recordProcessor;
    }

    public function count(string $dataClassName, DataClassCountParameters $parameters): int
    {
        try
        {
            $queryBuilder = new QueryBuilder();

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);

            $queryBuilder = $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

            $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

            if ($statement instanceof ADORecordSet)
            {
                $record = $statement->FetchRow();

                return (int) array_shift($record);
            }
            else
            {
                throw new Exception('Count Failed. Query: ' . $queryBuilder->getSQL());
            }
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
            $queryBuilder = new QueryBuilder();

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);

            $queryBuilder = $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

            $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

            if ($statement instanceof ADORecordSet)
            {
                $counts = [];
                while ($record = $statement->FetchRow())
                {
                    $counts[array_shift($record)] = array_pop($record);
                }

                $record = $statement->FetchRow();

                return $counts;
            }
            else
            {
                throw new Exception('Count Grouped Failed. Query: ' . $queryBuilder->getSQL());
            }
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            // TODO: Do something more useful when DataClassDatabase::countGrouped() throws an error
            exit;
        }
    }

    public function create(string $dataClassStorageUnitName, array $record): bool
    {
        try
        {
            $insertSql = $this->getConnection()->GetInsertSQL($dataClassStorageUnitName, $record);
            $result = $this->getConnection()->Execute($insertSql);

            if ($result === false)
            {
                throw new Exception('Insert record Failed. Query: ' . $insertSql);
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
     * @throws \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryException
     */
    public function delete(string $dataClassName, ?Condition $condition = null): bool
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->delete($dataClassName::getStorageUnitName(), $this->getAlias($dataClassName::getStorageUnitName()));

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
     * @return string[]
     */
    public function distinct(string $dataClassName, DataClassDistinctParameters $parameters): array
    {
        try
        {
            $queryBuilder = new QueryBuilder();

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);

            $queryBuilder = $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

            $statement = $this->getConnection()->Execute($queryBuilder->getSQL());

            if ($statement instanceof ADORecordSet)
            {
                $distinctElements = [];

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
                throw new Exception('Distinct Failed. Query: ' . $queryBuilder->getSQL());
            }
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
     * @param mixed $text
     *
     * @return mixed
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

    public function getAlias(string $dataClassStorageUnitName): string
    {
        return $this->getStorageAliasGenerator()->getTableAlias($dataClassStorageUnitName);
    }

    public function getConditionPartTranslatorService(): ConditionPartTranslatorService
    {
        return $this->conditionPartTranslatorService;
    }

    public function getConnection(): ADOConnection
    {
        return $this->connection;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int
    {
        return $this->getConnection()->Insert_ID($dataClassStorageUnitName);
    }

    public function getParametersProcessor(): ParametersProcessor
    {
        return $this->parametersProcessor;
    }

    public function getRecordProcessor(): RecordProcessor
    {
        return $this->recordProcessor;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    protected function handleError(Exception $exception)
    {
        $this->getExceptionLogger()->logException(
            new Exception('[Message: ' . $exception->getMessage() . '] [Information: {USER INFO GOES HERE}]')
        );
    }

    protected function handleQueryBuilderFrom(QueryBuilder $queryBuilder, string $dataClassName): void
    {
        $preparedTableName = $this->prepareTableName($dataClassName);
        $queryBuilder->from($preparedTableName, $this->getAlias($preparedTableName));
    }

    protected function prepareTableName(string $dataClassName): string
    {
        if (is_subclass_of($dataClassName, CompositeDataClass::class) &&
            get_parent_class($dataClassName) == CompositeDataClass::class)
        {
            $tableName = $dataClassName::getStorageUnitName();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && $dataClassName::isExtended())
        {
            $tableName = $dataClassName::getStorageUnitName();
        }
        elseif (is_subclass_of($dataClassName, CompositeDataClass::class) && !$dataClassName::isExtended())
        {
            $parent = $dataClassName::parentClassName();
            $tableName = $parent::getStorageUnitName();
        }
        else
        {
            $tableName = $dataClassName::getStorageUnitName();
        }

        return $tableName;
    }

    protected function processRecord(array $record): array
    {
        if ($this->getRecordProcessor() instanceof RecordProcessor)
        {
            return $this->getRecordProcessor()->processRecord($record);
        }

        return $record;
    }

    /**
     *
     * @param mixed $value
     * @param int|string|Type|null $type
     */
    public function quote($value, ?string $type = null)
    {
        return $this->getConnection()->Quote($value);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters): array
    {
        try
        {
            $queryBuilder = new QueryBuilder();

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);

            $queryBuilder = $this->getParametersProcessor()->run(
                $this, $queryBuilder, $parameters, $dataClassName
            );

            $sqlQuery = $queryBuilder->getSQL();

            /**
             *
             * @var \ADORecordSet $statement
             */
            $statement =
                $this->getConnection()->SelectLimit($sqlQuery, $parameters->getCount(), $parameters->getOffset());

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

            if (!is_array($record) || empty($record))
            {
                throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
            }

            return $this->processRecord($record);
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            throw new DataClassNoResultException($dataClassName, $parameters);
        }
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryException
     */
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): array
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->from(
            $this->prepareTableName($dataClassName), $this->getAlias($this->prepareTableName($dataClassName))
        );

        $queryBuilder = $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);
        $sql = $queryBuilder->getSQL();

        $recordSet = $this->getConnection()->SelectLimit(
            $sql, $parameters->getCount(), $parameters->getOffset()
        );

        if ($recordSet === false)
        {
            $this->handleError(new Exception('No Records Found. Query: ' . $sql));
            throw new DataClassNoResultException($dataClassName, $parameters, $sql);
        }
        else
        {
            $records = [];

            while ($record = $recordSet->FetchRow())
            {
                $records[] = $this->processRecord($record);
            }

            return $records;
        }
    }

    /**
     * @param callable $function
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactional(callable $function)
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
                $throwOnFalse($this->getConnection());

                return $this->getConnection()->CompleteTrans();
            }
            catch (Exception $e)
            {
                $this->getConnection()->RollbackTrans();
                throw $e;
            }
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * @param string[] $propertiesToUpdate
     *
     * @throws \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryException
     */
    public function update(string $dataClassStorageUnitName, Condition $condition, array $propertiesToUpdate): bool
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->update($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));

        foreach ($propertiesToUpdate as $key => $value)
        {
            $queryBuilder->set($key, $this->escape($value));
        }

        $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition));

        $result = $this->getConnection()->Execute($queryBuilder->getSQL());

        if ($result === false)
        {
            $this->handleError(new Exception('Update Failed. Query: ' . $queryBuilder->getSQL()));

            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryException
     */
    public function updates(string $dataClassStorageUnitName, UpdateProperties $properties, Condition $condition): bool
    {
        if (count($properties->get()) > 0)
        {
            $conditionPartTranslatorService = $this->getConditionPartTranslatorService();
            $queryBuilder = new QueryBuilder();
            $queryBuilder->update($dataClassStorageUnitName);

            foreach ($properties->get() as $dataClassProperty)
            {
                $queryBuilder->set(
                    $conditionPartTranslatorService->translate(
                        $this, $dataClassProperty->getPropertyConditionVariable(), false
                    ), $conditionPartTranslatorService->translate(
                    $this, $dataClassProperty->getValueConditionVariable(), false
                )
                );
            }

            $queryBuilder->where($conditionPartTranslatorService->translate($this, $condition));

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
