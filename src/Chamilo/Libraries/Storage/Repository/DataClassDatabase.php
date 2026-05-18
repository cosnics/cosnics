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
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
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
        protected Connection $connection, protected ExceptionLoggerInterface $exceptionLogger,
        protected ConditionTranslatorRegistry $conditionTranslatorRegistry,
        protected QueryBuilderConfigurator $queryBuilderConfigurator
    )
    {
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $dataClassName
     *
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function buildFromQuery(string $dataClassName, StorageParameters $parameters): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->from($dataClassName::getStorageUnitName(), $dataClassName::getAlias());
        $this->queryBuilderConfigurator->applyParameters(
            $queryBuilder, $parameters, $dataClassName
        );

        return $queryBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function count(string $dataClassName, StorageParameters $parameters): int
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassName, $parameters);

            try {
                $record = $queryBuilder->fetchNumeric();

                return (int) $record[0];
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);

                throw new StorageMethodException(
                    __FUNCTION__, $dataClassName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassName, $exception->getMessage()
            );
        }
    }

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countGrouped(string $dataClassName, StorageParameters $parameters): array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassName, $parameters);

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
                    __FUNCTION__, $dataClassName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (Throwable $throwable) {
            $this->handleError($throwable);

            throw new StorageMethodException(
                __FUNCTION__, $dataClassName, $throwable->getMessage()
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function create(string $dataClassStorageUnitName, array $record): void
    {
        try {
            $this->connection->insert($dataClassStorageUnitName, $record);
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
    public function delete(string $dataClassStorageUnitName, ?ConditionInterface $condition = null): void
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
    public function distinct(string $dataClassName, StorageParameters $parameters): array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassName, $parameters);

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
                    __FUNCTION__, $dataClassName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassName, $exception->getMessage()
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
    public function retrieve(string $dataClassName, StorageParameters $parameters): ?array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassName, $parameters);

            try {
                $record = $queryBuilder->fetchAssociative();

                if ($record === false) {
                    throw new StorageNoResultException($dataClassName, $parameters, $queryBuilder->getSQL());
                }

                return $record;
            }
            catch (StorageNoResultException $storageNoResultException) {
                throw $storageNoResultException;
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);
                throw new StorageMethodException(
                    __FUNCTION__, $dataClassName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassName, $exception->getMessage()
            );
        }
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function retrieves(string $dataClassName, StorageParameters $parameters): array
    {
        try {
            $queryBuilder = $this->buildFromQuery($dataClassName, $parameters);

            try {
                return $queryBuilder->fetchAllAssociative();
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);
                throw new StorageMethodException(
                    __FUNCTION__, $dataClassName, $throwable->getMessage(), $queryBuilder->getSQL()
                );
            }
        }
        catch (\Doctrine\DBAL\Exception $exception) {
            $this->handleError($exception);
            throw new StorageMethodException(
                __FUNCTION__, $dataClassName, $exception->getMessage()
            );
        }
    }

    /**
     * @param callable $function
     *
     * @return mixed
     * @throws \Throwable
     */
    public function transactional(callable $function): mixed
    {
        return $this->connection->transactional($function);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, ConditionInterface $condition
    ): void
    {
        if ($properties->count() > 0) {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder->update($dataClassStorageUnitName);
            $this->queryBuilderConfigurator->applyUpdate($queryBuilder, $properties, $condition);
            $sqlQuery = $queryBuilder->getSQL();

            try {
                $queryBuilder->executeStatement();
            }
            catch (UniqueConstraintViolationException $exception) {
                throw new ObjectAlreadyExistsException(
                    $dataClassStorageUnitName, $properties->toArray(), $exception->getMessage(), $exception->getCode(),
                    $exception
                );
            }
            catch (Throwable $throwable) {
                $this->handleError($throwable);
                throw new StorageMethodException(
                    __FUNCTION__, $dataClassStorageUnitName, $throwable->getMessage(), $sqlQuery
                );
            }
        }
    }
}
