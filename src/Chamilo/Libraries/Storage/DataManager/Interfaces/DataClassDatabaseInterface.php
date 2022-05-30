<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\DBAL\Types\Type;

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

    /**
     *
     * @param mixed $value
     * @param int|string|Type|null $type
     *
     * @return mixed
     */
    public function quote($value, ?string $type = null);

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters): array;

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    public function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): array;

    /**
     *
     * @param callable $function
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transactional(callable $function);

    public function translateCondition(Condition $condition, bool $enableAliasing = true): string;

    /**
     * @param string[] $propertiesToUpdate
     */
    public function update(string $dataClassStorageUnitName, Condition $condition, array $propertiesToUpdate): bool;

    public function updates(string $dataClassStorageUnitName, DataClassProperties $properties, Condition $condition): bool;
}