<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Database;

use ADOConnection;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;

/**
 * This class provides basic functionality for storage unit manipulations via AdoDb
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @todo Not adapted to AdoDb yet
 */
class StorageUnitDatabase implements StorageUnitDatabaseInterface
{
    use ClassContext;

    protected ADOConnection $connection;

    protected ExceptionLoggerInterface $exceptionLogger;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        ADOConnection $connection, StorageAliasGenerator $storageAliasGenerator,
        ExceptionLoggerInterface $exceptionLogger
    )
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
    }

    public function alter(int $type, string $storageUnitName, string $property, array $attributes = []): bool
    {
        // TODO: Implement alter() method.
        return false;
    }

    public function alterIndex(int $type, string $storageUnitName, ?string $indexName = null, array $columns = []): bool
    {
        // TODO: Implement alterIndex() method.
        return false;
    }

    public function create(string $storageUnitName, array $properties = [], array $indexes = []): bool
    {
        // TODO: Implement create() method.
        return false;
    }

    public function drop(string $storageUnitName): bool
    {
        // TODO: Implement drop() method.
        return false;
    }

    public function exists(string $storageUnitName): bool
    {
        // TODO: Implement exists() method.
        return false;
    }

    public function getConnection(): ADOConnection
    {
        return $this->connection;
    }

    public function setConnection(ADOConnection $connection): StorageUnitDatabase
    {
        $this->connection = $connection;

        return $this;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger): StorageUnitDatabase
    {
        $this->exceptionLogger = $exceptionLogger;

        return $this;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    public function setStorageAliasGenerator(StorageAliasGenerator $storageAliasGenerator): StorageUnitDatabase
    {
        $this->storageAliasGenerator = $storageAliasGenerator;

        return $this;
    }

    public function optimize(string $storageUnitName): bool
    {
        // TODO: Implement optimize() method.
        return false;
    }

    public function rename(string $oldStorageUnitName, string $newStorageUnitName): bool
    {
        // TODO: Implement rename() method.
        return false;
    }

    public function truncate(string $storageUnitName, ?bool $optimize = true): bool
    {
        // TODO: Implement truncate() method.
        return false;
    }
}
