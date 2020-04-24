<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface StorageUnitDatabaseInterface
{

    /**
     *
     * @param integer $type
     * @param string $storageUnitName
     * @param string $property
     * @param string[] $attributes
     *
     * @return boolean
     */
    public function alter($type, $storageUnitName, $property, $attributes);

    /**
     *
     * @param integer $type
     * @param string $storageUnitName
     * @param string $name
     * @param string[] $columns
     *
     * @return boolean
     */
    public function alterIndex($type, $storageUnitName, $name, $columns);

    /**
     *
     * @param string $storageUnitName
     * @param string[][] $properties
     * @param string[][][] $indexes
     *
     * @return boolean
     */
    public function create($storageUnitName, $properties, $indexes);

    /**
     *
     * @param string $storageUnitName
     *
     * @return boolean
     */
    public function drop($storageUnitName);

    /**
     *
     * @param string $storageUnitName
     *
     * @return boolean
     */
    public function exists($storageUnitName);

    /**
     *
     * @param string $storageUnitName
     *
     * @return boolean
     */
    public function optimize($storageUnitName);

    /**
     *
     * @param string $oldStorageUnitName
     * @param string $newStorageUnitName
     *
     * @return boolean
     */
    public function rename($oldStorageUnitName, $newStorageUnitName);

    /**
     *
     * @param string $storageUnitName
     *
     * @return boolean
     */
    public function truncate($storageUnitName);
}