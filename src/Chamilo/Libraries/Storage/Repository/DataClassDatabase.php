<?php
namespace Chamilo\Libraries\Storage\Repository;

use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorRegistry;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Service\QueryBuilderConfigurator;
use Chamilo\Libraries\Storage\Service\StorageAliasGenerator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
    public function __construct(
        protected Connection $connection, protected StorageAliasGenerator $storageAliasGenerator,
        protected ExceptionLoggerInterface $exceptionLogger,
        protected ConditionTranslatorRegistry $conditionTranslatorRegistry,
        protected QueryBuilderConfigurator $queryBuilderConfigurator
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function __retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): Result
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);
            $sqlQuery = $queryBuilder->getSQL();

            try {
                return $this->connection->executeQuery(
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function buildFromQuery(string $dataClassStorageUnitName, StorageParameters $parameters): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->from($dataClassStorageUnitName, $this->getAlias($dataClassStorageUnitName));
        $this->queryBuilderConfigurator->applyParameters(
            $queryBuilder, $parameters, $dataClassStorageUnitName
        );

        return $queryBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
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

            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage()
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function create(string $dataClassStorageUnitName, array $record): bool
    {
        try {
            $this->connection->insert($dataClassStorageUnitName, $record);

            return true;
        }
        catch (UniqueConstraintViolationException $exception) {
            throw new ObjectAlreadyExistsException(
                $dataClassStorageUnitName, $record, $exception->getMessage(), $exception->getCode(), $exception
            );
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);

            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage()
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function delete(string $dataClassStorageUnitName, ?ConditionInterface $condition = null): bool
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();

            $queryBuilder->delete($dataClassStorageUnitName);

            if (isset($condition)) {
                $queryBuilder->where(
                    $this->conditionTranslatorRegistry->translate($queryBuilder, $condition, false)
                );
            }

            $queryBuilder->executeStatement();

            return true;
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);

            throw new StorageMethodException(
                __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage()
            );
        }
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
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
        return $this->storageAliasGenerator->getTableAlias($dataClassStorageUnitName);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int|string
    {
        try {
            $lastInsertedId = $this->connection->lastInsertId();

            if (!$lastInsertedId) {
                $lastInsertedId = $this->connection->lastInsertId();
            }

            return $lastInsertedId;
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);

            throw new StorageLastInsertedIdentifierException($dataClassStorageUnitName, $throwable->getMessage());
        }
    }

    protected function handleError(Throwable $throwable): void
    {
        $this->exceptionLogger->logException(
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

        return $this->connection->quote($value);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): ?array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassStorageUnitName, $parameters);

            try {
                $record = $queryBuilder->fetchAssociative();

                if ($record === false) {
                    throw new StorageNoResultException($dataClassStorageUnitName, $parameters, $queryBuilder->getSQL());
                }

                return $record;
            }
            catch (StorageNoResultException $storageNoResultException) {
                throw $storageNoResultException;
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
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
     */
    public function transactional(callable $function): mixed
    {
        return $this->connection->transactional($function);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, ConditionInterface $condition
    ): bool
    {
        if ($properties->count() === 0) {
            return true;
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update($dataClassStorageUnitName);
        $this->queryBuilderConfigurator->applyUpdate($queryBuilder, $properties, $condition);
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
