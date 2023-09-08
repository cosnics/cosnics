<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

/**
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
interface StorageUnitDatabaseInterface
{

    /**
     * @param string[] $attributes
     */
    public function alter(int $type, string $storageUnitName, string $property, array $attributes = []): bool;

    public function alterIndex(int $type, string $storageUnitName, ?string $indexName = null, array $columns = []
    ): bool;

    /**
     * @param string[][] $properties
     * @param string[][][] $indexes
     */
    public function create(string $storageUnitName, array $properties = [], array $indexes = []): bool;

    public function drop(string $storageUnitName): bool;

    public function exists(string $storageUnitName): bool;

    public function initializeStorage(string $databaseName, bool $overwriteIfDatabaseAlreadyExists);

    public function optimize(string $storageUnitName): bool;

    public function rename(string $oldStorageUnitName, string $newStorageUnitName): bool;

    public function truncate(string $storageUnitName, ?bool $optimize = true): bool;
}