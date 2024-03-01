<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\QueryBuilderConfigurator;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseCountException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseCountGroupedException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseCreateException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseDeleteException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseDistinctException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseLastInsertedIdentifierException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseRetrieveException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseRetrievesException;
use Chamilo\Libraries\Storage\Exception\Database\DatabaseUpdateException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Doctrine\DBAL\Connection;
use Exception;
use Throwable;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassDatabase implements DataClassDatabaseInterface
{
    protected ConditionPartTranslatorService $conditionPartTranslatorService;

    protected Connection $connection;

    protected ExceptionLoggerInterface $exceptionLogger;

    protected QueryBuilderConfigurator $queryBuilderConfigurator;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        Connection $connection, StorageAliasGenerator $storageAliasGenerator, ExceptionLoggerInterface $exceptionLogger,
        ConditionPartTranslatorService $conditionPartTranslatorService, QueryBuilderConfigurator $parametersProcessor
    )
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
        $this->conditionPartTranslatorService = $conditionPartTranslatorService;
        $this->queryBuilderConfigurator = $parametersProcessor;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseCountException
     */
    public function count(string $dataClassStorageUnitName, DataClassCountParameters $parameters): int
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
            $this->getQueryBuilderConfigurator()->applyParameters(
                $this, $queryBuilder, $parameters, $dataClassStorageUnitName
            );

            $record = $this->getConnection()->executeQuery($queryBuilder->getSQL())->fetchNumeric();

            return (int) $record[0];
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new DatabaseCountException($dataClassStorageUnitName, $parameters, $throwable->getMessage());
        }
    }

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseCountGroupedException
     */
    public function countGrouped(string $dataClassStorageUnitName, DataClassCountGroupedParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
            $this->getQueryBuilderConfigurator()->applyParameters(
                $this, $queryBuilder, $parameters, $dataClassStorageUnitName
            );

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

            throw new DatabaseCountGroupedException($dataClassStorageUnitName, $parameters, $throwable->getMessage());
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseCreateException
     */
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

            throw new DatabaseCreateException($dataClassStorageUnitName, $record, $throwable->getMessage());
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseDeleteException
     */
    public function delete(string $dataClassStorageUnitName, ?Condition $condition = null): bool
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->delete($dataClassStorageUnitName);

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

            throw new DatabaseDeleteException($dataClassStorageUnitName, $condition, $throwable->getMessage());
        }
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseDistinctException
     */
    public function distinct(string $dataClassStorageUnitName, DataClassDistinctParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
            $this->getQueryBuilderConfigurator()->applyParameters(
                $this, $queryBuilder, $parameters, $dataClassStorageUnitName
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
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new DatabaseDistinctException($dataClassStorageUnitName, $parameters, $throwable->getMessage());
        }
    }

    protected function escape(mixed $text): mixed
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

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseLastInsertedIdentifierException
     */
    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int
    {
        try
        {
            return $this->getConnection()->lastInsertId($dataClassStorageUnitName);
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new DatabaseLastInsertedIdentifierException($dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    public function getQueryBuilderConfigurator(): QueryBuilderConfigurator
    {
        return $this->queryBuilderConfigurator;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    protected function handleError(Throwable $throwable): void
    {
        $this->getExceptionLogger()->logException(
            new Exception('[Message: ' . $throwable->getMessage() . ']')
        );
    }

    public function quote(mixed $value, ?string $type = null): mixed
    {
        return $this->getConnection()->quote($value, $type);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseRetrieveException
     */
    public function retrieve(string $dataClassStorageUnitName, DataClassRetrieveParameters $parameters): ?array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
            $this->getQueryBuilderConfigurator()->applyParameters(
                $this, $queryBuilder, $parameters, $dataClassStorageUnitName
            );

            $sqlQuery = $queryBuilder->getSQL();

            $statement = $this->getConnection()->executeQuery($sqlQuery);
            $record = $statement->fetchAssociative();

            if (!is_array($record) || empty($record))
            {
                $record = null;
            }

            return $record;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new DatabaseRetrieveException($dataClassStorageUnitName, $parameters, $throwable->getMessage());
        }
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseRetrievesException
     */
    public function retrieves(string $dataClassStorageUnitName, DataClassRetrievesParameters $parameters): array
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
            $this->getQueryBuilderConfigurator()->applyParameters(
                $this, $queryBuilder, $parameters, $dataClassStorageUnitName
            );

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

            throw new DatabaseRetrievesException($dataClassStorageUnitName, $parameters, $throwable->getMessage());
        }
    }

    /**
     * @param callable $function
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactional(callable $function): mixed
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
        catch (Exception)
        {
            return false;
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\DatabaseUpdateException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, Condition $condition): bool
    {
        try
        {
            if ($properties->count() > 0)
            {
                $conditionPartTranslatorService = $this->getConditionPartTranslatorService();
                $queryBuilder = $this->getConnection()->createQueryBuilder();

                $queryBuilder->update($dataClassStorageUnitName);

                foreach ($properties as $dataClassProperty)
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

            throw new DatabaseUpdateException(
                $dataClassStorageUnitName, $condition, $properties->toArray(), $throwable->getMessage()
            );
        }
    }
}
