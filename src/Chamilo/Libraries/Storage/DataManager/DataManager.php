<?php
namespace Chamilo\Libraries\Storage\DataManager;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * General and basic DataManager, providing basic functionality for all other DataManager objects
 *
 * @package    Chamilo\Libraries\Storage\DataManager
 * @author     Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author     Magali Gillard <magali.gillard@ehb.be>
 * @deprecated Replaced by the service-based DataClassRepository and StorageUnitRepository
 */
class DataManager
{

    /**
     * @param string[][] $attributes
     */
    public static function alter_storage_unit(
        int $type, string $storageUnitName, string $property, array $attributes = []
    ): bool
    {
        return self::getStorageUnitRepository()->alter($type, $storageUnitName, $property, $attributes);
    }

    public static function alter_storage_unit_index(
        int $type, string $storageUnitName, ?string $indexName = null, array $columns = []
    ): bool
    {
        return self::getStorageUnitRepository()->alterIndex($type, $storageUnitName, $indexName, $columns);
    }

    public static function count(string $dataClassName, DataClassCountParameters $parameters): int
    {
        return self::getDataClassRepository()->count($dataClassName, $parameters);
    }

    /**
     * @return int[]
     */
    public static function count_grouped(string $dataClassName, DataClassCountGroupedParameters $parameters): array
    {
        return self::getDataClassRepository()->countGrouped($dataClassName, $parameters);
    }

    public static function create(DataClass $dataClass): bool
    {
        return self::getDataClassRepository()->create($dataClass);
    }

    public static function create_record(string $dataClassName, array $record): bool
    {
        return self::getDataClassRepository()->createRecord($dataClassName, $record);
    }

    /**
     * @param string[][] $properties
     * @param string[][][] $indexes
     */
    public static function create_storage_unit(string $storageUnitName, array $properties = [], array $indexes = []
    ): bool
    {
        return self::getStorageUnitRepository()->create($storageUnitName, $properties, $indexes);
    }

    public static function delete(DataClass $dataClass): bool
    {
        return self::getDataClassRepository()->delete($dataClass);
    }

    public static function deletes(string $dataClassName, Condition $condition): bool
    {
        return self::getDataClassRepository()->deletes($dataClassName, $condition);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public static function determineDataClassType(string $dataClassName, int $identifier): string
    {
        return self::getDataClassRepository()->determineDataClassType($dataClassName, $identifier);
    }

    /**
     * @return string[]
     */
    public static function distinct(string $dataClassName, DataClassDistinctParameters $parameters): array
    {
        return self::getDataClassRepository()->distinct($dataClassName, $parameters);
    }

    public static function drop_storage_unit(string $storageUnitName): bool
    {
        return self::getStorageUnitRepository()->drop($storageUnitName);
    }

    public static function getDataClassRepository(): DataClassRepository
    {
        return self::getService(
            'Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository'
        );
    }

    public static function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    public static function getStorageUnitRepository(): StorageUnitRepository
    {
        return self::getService(
            'Chamilo\Libraries\Storage\DataManager\Doctrine\Repository\StorageUnitRepository'
        );
    }

    public static function move_display_orders(
        string $dataClassName, string $displayOrderProperty, ?int $start = 1, ?int $end = null,
        ?Condition $displayOrderCondition = null
    ): bool
    {
        return self::getDataClassRepository()->moveDisplayOrders(
            $dataClassName, $displayOrderProperty, $start, $end, $displayOrderCondition
        );
    }

    public static function optimize_storage_unit(string $storageUnitName): bool
    {
        return self::getStorageUnitRepository()->optimize($storageUnitName);
    }

    public static function record(string $dataClassName, RecordRetrieveParameters $parameters): array
    {
        return self::getDataClassRepository()->record($dataClassName, $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public static function records(string $dataClassName, RecordRetrievesParameters $parameters): ArrayCollection
    {
        return self::getDataClassRepository()->records($dataClassName, $parameters);
    }

    public static function rename_storage_unit(string $oldStorageUnitName, string $newStorageUnitName): bool
    {
        return self::getStorageUnitRepository()->rename($oldStorageUnitName, $newStorageUnitName);
    }

    /**
     * @template retrieveDataClassName
     * @param class-string<retrieveDataClassName> $dataClassName
     *
     * @return retrieveDataClassName
     */
    public static function retrieve(string $dataClassName, DataClassRetrieveParameters $parameters)
    {
        return self::getDataClassRepository()->retrieve($dataClassName, $parameters);
    }

    /**
     * @template retrieveById
     * @param class-string<retrieveById> $dataClassName
     * @param string $identifier
     *
     * @return retrieveById
     */
    public static function retrieve_by_id(string $dataClassName, string $identifier)
    {
        return self::getDataClassRepository()->retrieveById($dataClassName, $identifier);
    }

    public static function retrieve_composite_data_class_additional_properties(
        string $compositeDataClassName, string $compositeDataClassIdentifier
    ): array
    {
        return self::getDataClassRepository()->retrieveCompositeDataClassAdditionalProperties(
            $compositeDataClassName, $compositeDataClassIdentifier
        );
    }

    public static function retrieve_maximum_value(string $dataClassName, string $property, ?Condition $condition = null
    ): int
    {
        return self::getDataClassRepository()->retrieveMaximumValue($dataClassName, $property, $condition);
    }

    public static function retrieve_next_value(string $dataClassName, string $property, ?Condition $condition = null
    ): int
    {
        return self::getDataClassRepository()->retrieveNextValue($dataClassName, $property, $condition);
    }

    /**
     * @template tRetrieves
     * @param class-string<tRetrieves> $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return ArrayCollection<tRetrieves>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public static function retrieves(string $dataClassName, DataClassRetrievesParameters $parameters): ArrayCollection
    {
        return self::getDataClassRepository()->retrieves($dataClassName, $parameters);
    }

    public static function storage_unit_exists(string $storageUnitName): bool
    {
        return self::getStorageUnitRepository()->exists($storageUnitName);
    }

    /**
     * @param callable $function
     *
     * @return mixed
     * @throws \Throwable
     */
    public static function transactional(callable $function)
    {
        return self::getDataClassRepository()->transactional($function);
    }

    public static function truncate_storage_unit(string $storageUnitName, ?bool $optimize = true): bool
    {
        return self::getStorageUnitRepository()->truncate($storageUnitName, $optimize);
    }

    public static function update(DataClass $dataClass): bool
    {
        return self::getDataClassRepository()->update($dataClass);
    }

    public static function updates(string $dataClassName, UpdateProperties $properties, Condition $condition): bool
    {
        return self::getDataClassRepository()->updates($dataClassName, $properties, $condition);
    }
}
