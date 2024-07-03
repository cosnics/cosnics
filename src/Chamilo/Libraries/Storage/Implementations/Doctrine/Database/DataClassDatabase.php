<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine\Database;

use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\Architecture\Exceptions\StorageLastInsertedIdentifierException;
use Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException;
use Chamilo\Libraries\Storage\Architecture\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\Implementations\Doctrine\Service\QueryBuilderConfigurator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Service\StorageAliasGenerator;
use Chamilo\Libraries\Storage\StorageParameters;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Exception;
use Throwable;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 *
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine\Database
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    protected function __retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): Result
    {
        $sqlQuery = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

        try
        {
            return $this->getConnection()->executeQuery($sqlQuery);
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new StorageMethodException(
                __METHOD__, $dataClassStorageUnitName, $throwable->getMessage(), $sqlQuery
            );
        }
    }

    protected function buildFromQuery(string $dataClassStorageUnitName, StorageParameters $parameters): string
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
        $this->getQueryBuilderConfigurator()->applyParameters(
            $this, $queryBuilder, $parameters, $dataClassStorageUnitName
        );

        return $queryBuilder->getSQL();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function count(string $dataClassStorageUnitName, StorageParameters $parameters): int
    {
        $sqlQuery = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

        try
        {
            $record = $this->getConnection()->executeQuery($sqlQuery)->fetchNumeric();

            return (int) $record[0];
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new StorageMethodException(
                __METHOD__, $dataClassStorageUnitName, $throwable->getMessage(), $sqlQuery
            );
        }
    }

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function countGrouped(string $dataClassStorageUnitName, StorageParameters $parameters): array
    {
        $sqlQuery = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

        try
        {
            $result = $this->getConnection()->executeQuery($sqlQuery);

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

            throw new StorageMethodException(__METHOD__, $dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
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

            throw new StorageMethodException(__METHOD__, $dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
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

            throw new StorageMethodException(__METHOD__, $dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function distinct(string $dataClassStorageUnitName, StorageParameters $parameters): array
    {
        $sqlQuery = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

        try
        {
            $statement = $this->getConnection()->executeQuery($sqlQuery);

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

            throw new StorageMethodException(
                __METHOD__, $dataClassStorageUnitName, $throwable->getMessage(), $sqlQuery
            );
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageLastInsertedIdentifierException
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

            throw new StorageLastInsertedIdentifierException($dataClassStorageUnitName, $throwable->getMessage());
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): ?array
    {
        $statement = $this->__retrieve($dataClassStorageUnitName, $parameters);

        try
        {
            $record = $statement->fetchAssociative();

            if ($record === false)
            {
                throw new StorageNoResultException(__METHOD__, $dataClassStorageUnitName, $parameters);
            }

            return $record;
        }
        catch (\Doctrine\DBAL\Exception $exception)
        {
            throw new StorageMethodException(__METHOD__, $dataClassStorageUnitName, $exception->getMessage());
        }
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function retrieves(string $dataClassStorageUnitName, StorageParameters $parameters): array
    {
        $statement = $this->__retrieve($dataClassStorageUnitName, $parameters);

        try
        {
            $records = [];

            while ($record = $statement->fetchAssociative())
            {
                $records[] = $record;
            }

            return $records;
        }
        catch (\Doctrine\DBAL\Exception $exception)
        {
            throw new StorageMethodException(__METHOD__, $dataClassStorageUnitName, $exception->getMessage());
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, Condition $condition): bool
    {
        if ($properties->count() === 0)
        {
            return true;
        }

        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->update($dataClassStorageUnitName);
        $this->getQueryBuilderConfigurator()->applyUpdate($this, $queryBuilder, $properties, $condition);
        $sqlQuery = $queryBuilder->getSQL();

        try
        {
            $this->getConnection()->executeQuery($sqlQuery);

            return true;
        }
        catch (Throwable $throwable)
        {
            $this->handleError($throwable);

            throw new StorageMethodException(
                __METHOD__, $dataClassStorageUnitName, $throwable->getMessage(), $sqlQuery
            );
        }
    }
}
