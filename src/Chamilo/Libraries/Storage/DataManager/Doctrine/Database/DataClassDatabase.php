<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ParametersProcessor;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\RecordProcessor;
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
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Exception;
use Throwable;

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

    public function count(string $dataClassName, DataClassCountParameters $parameters): int
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);
            $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

            $result = $this->getConnection()->executeQuery($queryBuilder->getSQL());
            $record = $result->fetchNumeric();

            return (int) $record[0];
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

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

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);
            $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

            $result = $this->getConnection()->executeQuery($queryBuilder->getSQL());

            $counts = [];

            while ($record = $result->fetchNumeric())
            {
                $counts[$record[0]] = $record[1];
            }

            return $counts;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            // TODO: Do something more useful when DataClassDatabase::countGrouped() throws an error
            exit;
        }
    }

    public function create(string $dataClassStorageUnitName, array $record): bool
    {
        try
        {
            $this->getConnection()->insert($dataClassStorageUnitName, $record);

            return true;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            return false;
        }
    }

    /**
     * @template deleteDataClassName
     *
     * @param class-string<deleteDataClassName> $dataClassName
     */
    public function delete(string $dataClassName, ?Condition $condition = null): bool
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->delete(
                $dataClassName::getStorageUnitName()/*, $this->getAlias($dataClassName::getStorageUnitName())*/
            );

            if (isset($condition))
            {
                $queryBuilder->where($this->getConditionPartTranslatorService()->translate($this, $condition, false));
            }

            $this->getConnection()->executeQuery($queryBuilder->getSQL());

            return true;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

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

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);
            $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

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
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

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

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int
    {
        try
        {
            return $this->getConnection()->lastInsertId($dataClassStorageUnitName);
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            // TODO: Do something more useful when DataClassDatabase::getLastInsertedIdentifier() throws an error
            exit;
        }
    }

    public function getParametersProcessor(): ParametersProcessor
    {
        return $this->parametersProcessor;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Service\RecordProcessor
     */
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
            new Exception('[Message: ' . $exception->getMessage() . ']')
        );
    }

    protected function handleQueryBuilderFrom(QueryBuilder $queryBuilder, string $dataClassName): void
    {
        $preparedTableName = $this->prepareTableName($dataClassName);
        $queryBuilder->from($preparedTableName, $this->getAlias($preparedTableName));
    }

    /**
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }

    /**
     * @template prepareTableNameDataClassName
     *
     * @param class-string<prepareTableNameDataClassName> $dataClassName
     */
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
     */
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);
            $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

            $sqlQuery = $queryBuilder->getSQL();

            $statement = $this->getConnection()->executeQuery($sqlQuery);
            $record = $statement->fetchAssociative();

            if (!is_array($record) || empty($record))
            {
                throw new DataClassNoResultException($dataClassName, $parameters, $sqlQuery);
            }

            return $record;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new DataClassNoResultException($dataClassName, $parameters);
        }
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $this->handleQueryBuilderFrom($queryBuilder, $dataClassName);
            $this->getParametersProcessor()->run($this, $queryBuilder, $parameters, $dataClassName);

            $statement = $this->getConnection()->executeQuery($queryBuilder->getSQL());

            $records = [];

            while ($record = $statement->fetchAssociative())
            {
                $records[] = $record;
            }

            return $records;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);
            throw new DataClassNoResultException($dataClassName, $parameters);
        }
    }

    /**
     *
     * @param callable $function
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactional(callable $function)
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
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            return false;
        }
    }

    public function updates(string $dataClassStorageUnitName, UpdateProperties $properties, Condition $condition): bool
    {
        try
        {
            if (count($properties->get()) > 0)
            {
                $conditionPartTranslatorService = $this->getConditionPartTranslatorService();
                $queryBuilder = $this->getConnection()->createQueryBuilder();
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

                $queryBuilder->where($conditionPartTranslatorService->translate($this, $condition, false));

                $this->getConnection()->executeQuery($queryBuilder->getSQL());
            }

            return true;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            return false;
        }
    }
}
