<?php
namespace Chamilo\Libraries\Storage\DataManager;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface StorageUnitManagerRepositoryInterface
{

    public function createStorageUnit($name, $properties, $indexes);

    public function storageUnitExists($name);

    public function dropStorageUnit($name);

    public function renameStorageUnit($old_name, $new_name);

    public function alterStorageUnit($type, $tableName, $property, $attributes);

    public function alterStorageUnitIndex($type, $tableName, $name, $columns);

    public function truncateStorageUnit($name);

    public function optimizeStorageUnit($name);
}