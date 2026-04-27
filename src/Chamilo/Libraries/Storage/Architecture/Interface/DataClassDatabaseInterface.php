<?php
namespace Chamilo\Libraries\Storage\Architecture\Interface;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataClassDatabaseInterface
{
    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function count(string $dataClassStorageUnitName, StorageParameters $parameters): int;

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countGrouped(string $dataClassStorageUnitName, StorageParameters $parameters): array;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function create(string $dataClassStorageUnitName, array $record): bool;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function delete(string $dataClassStorageUnitName, ?ConditionInterface $condition = null): bool;

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function distinct(string $dataClassStorageUnitName, StorageParameters $parameters): array;

    public function escapeColumnName(string $columnName, ?string $storageUnitAlias = null): string;

    public function getAlias(string $dataClassStorageUnitName): string;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int|string;

    public function quote(mixed $value): string;

    /**
     * @return ?string[]
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieve(string $dataClassStorageUnitName, StorageParameters $parameters): ?array;

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieves(string $dataClassStorageUnitName, StorageParameters $parameters): array;

    /**
     * @param callable $function
     *
     * @return mixed
     */
    public function transactional(callable $function): mixed;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, ConditionInterface $condition
    ): bool;
}