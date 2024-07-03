<?php
namespace Chamilo\Libraries\Storage\Architecture\Interfaces;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\StorageParameters;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataClassDatabaseInterface
{

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function count(string $dataClassStorageUnitName, StorageParameters $parameters): int;

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function countGrouped(string $dataClassStorageUnitName, StorageParameters $parameters): array;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function create(string $dataClassStorageUnitName, array $record): bool;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function delete(string $dataClassStorageUnitName, ?Condition $condition = null): bool;

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function distinct(string $dataClassStorageUnitName, StorageParameters $parameters): array;

    public function escapeColumnName(string $columnName, ?string $storageUnitAlias = null): string;

    public function getAlias(string $dataClassStorageUnitName): string;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageLastInsertedIdentifierException
     */
    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int;

    public function quote(mixed $value, ?string $type = null): mixed;

    /**
     * @return ?string[]
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): ?array;

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function retrieves(string $dataClassStorageUnitName, StorageParameters $parameters): array;

    /**
     * @param callable $function
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactional(callable $function): mixed;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, Condition $condition): bool;
}