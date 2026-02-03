<?php
namespace Chamilo\Libraries\Storage\Repository;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorCollection;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Chamilo\Libraries\Storage\Service\StorageAliasGenerator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Exception;
use Throwable;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 *
 * @package Chamilo\Libraries\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassDatabase implements DataClassDatabaseInterface
{
    protected ConditionTranslatorCollection $conditionTranslatorCollection;

    protected Connection $connection;

    protected ExceptionLoggerInterface $exceptionLogger;

    protected QueryBuilderConfigurator $queryBuilderConfigurator;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        Connection $connection, StorageAliasGenerator $storageAliasGenerator, ExceptionLoggerInterface $exceptionLogger,
        ConditionTranslatorCollection $conditionPartTranslatorService, QueryBuilderConfigurator $parametersProcessor
    )
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
        $this->conditionTranslatorCollection = $conditionPartTranslatorService;
        $this->queryBuilderConfigurator = $parametersProcessor;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): Result
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);
            $sqlQuery = $queryBuilder->getSQL();

            try {
                return $this->getConnection()->executeQuery(
                    $sqlQuery, $queryBuilder->getParameters(), $queryBuilder->getParameterTypes()
                );
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);
                throw new StorageMethodException(
                    __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $sqlQuery
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $exception->getMessage()
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    protected function buildFromQuery(string $dataClassStorageUnitName, StorageParameters $parameters): QueryBuilder
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();

        $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
        $this->getQueryBuilderConfigurator()->applyParameters(
            $queryBuilder, $parameters, $dataClassStorageUnitName
        );

        return $queryBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function count(string $dataClassStorageUnitName, StorageParameters $parameters): int
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

            try {
                $record = $queryBuilder->fetchNumeric();

                return (int) $record[0];
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);

                throw new StorageMethodException(
                    __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $exception->getMessage()
            );
        }
    }

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countGrouped(string $dataClassStorageUnitName, StorageParameters $parameters): array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

            try {
                $counts = [];

                $records = $queryBuilder->fetchAllNumeric();

                foreach ($records as $record) {
                    $counts[$record[0]] = $record[1];
                }

                return $counts;
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);

                throw new StorageMethodException(
                    __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);

            throw new StorageMethodException(__FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function create(string $dataClassStorageUnitName, array $record): bool
    {
        try {
            $this->getConnection()->insert($dataClassStorageUnitName, $record);

            return true;
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);

            throw new StorageMethodException(__FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function delete(string $dataClassStorageUnitName, ?ConditionInterface $condition = null): bool
    {
        try {
            $queryBuilder = $this->getConnection()->createQueryBuilder();

            $queryBuilder->delete($dataClassStorageUnitName);

            if (isset($condition)) {
                $queryBuilder->where(
                    $this->getConditionTranslatorCollection()->translate($queryBuilder, $condition, false)
                );
            }

            $queryBuilder->executeStatement();

            return true;
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);

            throw new StorageMethodException(__FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function distinct(string $dataClassStorageUnitName, StorageParameters $parameters): array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

            try {
                $distinctElements = [];
                $records = $queryBuilder->fetchAllAssociative();

                foreach ($records as $record) {
                    if (count($record) > 1) {
                        $distinctElements[] = $record;
                    }
                    else {
                        $distinctElements[] = array_pop($record);
                    }
                }

                return $distinctElements;
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);

                throw new StorageMethodException(
                    __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $exception->getMessage()
            );
        }
    }

    public function escapeColumnName(string $columnName, ?string $storageUnitAlias = null): string
    {
        if (!empty($storageUnitAlias)) {
            return $storageUnitAlias . '.' . $columnName;
        }
        else {
            return $columnName;
        }
    }

    public function getAlias(string $dataClassStorageUnitName): string
    {
        return $this->getStorageAliasGenerator()->getTableAlias($dataClassStorageUnitName);
    }

    public function getConditionTranslatorCollection(): ConditionTranslatorCollection
    {
        return $this->conditionTranslatorCollection;
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int|string
    {
        try {
            $lastInsertedId = $this->getConnection()->lastInsertId();

            if (!$lastInsertedId) {
                $lastInsertedId = $this->getConnection()->lastInsertId();
            }

            return $lastInsertedId;
        }
        catch (Throwable $throwable) {
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

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function quote(mixed $value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        return $this->getConnection()->quote($value);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): ?array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

            try {
                $record = $queryBuilder->fetchAssociative();

                if ($record === false) {
                    throw new StorageNoResultException(
                        __FUNCTION__, $dataClassStorageUnitName, $parameters,
                        'No result for query: ' . $queryBuilder->getSQL()
                    );
                }

                return $record;
            }
            catch (StorageNoResultException $exception) {
                throw $exception;
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);
                throw new StorageMethodException(
                    __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $exception->getMessage()
            );
        }
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function retrieves(string $dataClassStorageUnitName, StorageParameters $parameters): array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

            try {
                return $queryBuilder->fetchAllAssociative();
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);
                throw new StorageMethodException(
                    __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $exception->getMessage()
            );
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
        try {
            // Rather than directly using Doctrine's version of transactional, we implement
            // an intermediate function that throws an exception if the function returns #f.
            // This mediates between Chamilo's convention of returning #f to signal failure
            // versus Doctrine's use of Exceptions.
            $throwOnFalse = function ($connection) use ($function) {
                $result = call_user_func($function, $connection);
                if (!$result) {
                    throw new Exception();
                }
                else {
                    return $result;
                }
            };

            return $this->getConnection()->transactional($throwOnFalse);
        }
        catch (Exception) {
            return false;
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, ConditionInterface $condition
    ): bool
    {
        if ($properties->count() === 0) {
            return true;
        }

        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->update($dataClassStorageUnitName);
        $this->getQueryBuilderConfigurator()->applyUpdate($queryBuilder, $properties, $condition);
        $sqlQuery = $queryBuilder->getSQL();

        try {
            $queryBuilder->executeStatement();

            return true;
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $sqlQuery
            );
        }
    }
}
