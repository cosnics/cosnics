<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\UpdateProperties;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataClassDatabaseInterface
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function count(string $dataClassStorageUnitName, DataClassCountParameters $parameters): int;

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function countGrouped(string $dataClassStorageUnitName, DataClassCountGroupedParameters $parameters): array;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function create(string $dataClassStorageUnitName, array $record): bool;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function delete(string $dataClassStorageUnitName, ?Condition $condition = null): bool;

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function distinct(string $dataClassStorageUnitName, DataClassDistinctParameters $parameters): array;

    public function escapeColumnName(string $columnName, ?string $storageUnitAlias = null): string;

    public function getAlias(string $dataClassStorageUnitName): string;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageLastInsertedIdentifierException
     */
    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int;

    public function quote(mixed $value, ?string $type = null): mixed;

    /**
     * @return ?string[]
     *
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function retrieve(string $dataClassStorageUnitName, RetrieveParameters $parameters): ?array;

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function retrieves(string $dataClassStorageUnitName, RetrievesParameters $parameters): array;

    /**
     * @param callable $function
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactional(callable $function): mixed;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\Database\StorageMethodException
     */
    public function update(string $dataClassStorageUnitName, UpdateProperties $properties, Condition $condition): bool;
}