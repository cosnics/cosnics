<?php
namespace Chamilo\Libraries\Storage\DataManager;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataManagerRepositoryInterface
{

    public function create($object);

    public function createRecord($className, $record);

    public function retrieve($objectClass, $parameters);

    public function record($class, $parameters);

    public function retrieves($objectClass, $parameters);

    public function records($class, $parameters);

    public function distinct($class, $parameters);

    public function update($object, $condition);

    public function updates($class, $propertiesClass, $condition, $offset, $count, $orderBy);

    public function delete($className, $condition);

    public function count($objectClass, $parameters);

    public function countGrouped($class, $parameters);

    public function retrieveMaximumValue($class, $property, $condition);

    public function createStorageUnit($name, $properties, $indexes);

    public function storageUnitExists($name);

    public function dropStorageUnit($name);

    public function renameStorageUnit($old_name, $new_name);

    public function alterStorageUnit($type, $tableName, $property, $attributes);

    public function alterStorageUnitIndex($type, $tableName, $name, $columns);

    public function truncateStorageUnit($name);

    public function optimizeStorageUnit($name);

    public function getAlias($storageUnitName);

    public function retrieveCompositeDataClassAdditionalProperties($object);

    public function transactional($function);

    public function translateCondition($condition);
}