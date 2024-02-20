<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
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

    public function count(string $dataClassName, DataClassCountParameters $parameters): int;

    /**
     * @return int[]
     */
    public function countGrouped(string $dataClassName, DataClassCountGroupedParameters $parameters): array;

    public function create(string $dataClassStorageUnitName, array $record): bool;

    public function delete(string $dataClassName, ?Condition $condition = null): bool;

    /**
     * @return string[]
     */
    public function distinct(string $dataClassName, DataClassDistinctParameters $parameters): array;

    public function escapeColumnName(string $columnName, ?string $storageUnitAlias = null): string;

    public function getAlias(string $dataClassStorageUnitName): string;

    public function getLastInsertedIdentifier(string $dataClassStorageUnitName): int;

    public function quote(mixed $value, ?string $type = null): mixed;

    /**
     * @return ?string[]
     */
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters): ?array;

    /**
     * @return string[][]
     */
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): array;

    /**
     *
     * @param callable $function
     */
    public function transactional(callable $function): mixed;

    public function update(string $dataClassName, UpdateProperties $properties, Condition $condition): bool;
}