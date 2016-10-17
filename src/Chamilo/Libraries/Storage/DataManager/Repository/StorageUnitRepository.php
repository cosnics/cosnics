<?php
namespace Chamilo\Libraries\Storage\DataManager\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class StorageUnitRepository
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    // Storage unit actions
    const ALTER_STORAGE_UNIT_ADD = 1;
    const ALTER_STORAGE_UNIT_CHANGE = 2;
    const ALTER_STORAGE_UNIT_DROP = 3;
    const ALTER_STORAGE_UNIT_DROP_PRIMARY_KEY = 4;
    const ALTER_STORAGE_UNIT_ADD_PRIMARY_KEY = 5;
    const ALTER_STORAGE_UNIT_DROP_INDEX = 6;
    const ALTER_STORAGE_UNIT_ADD_INDEX = 7;
    const ALTER_STORAGE_UNIT_ADD_UNIQUE = 8;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface
     */
    private $storageUnitDatabase;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface $storageUnitDatabase
     */
    public function __construct(StorageUnitDatabaseInterface $storageUnitDatabase)
    {
        $this->storageUnitDatabase = $storageUnitDatabase;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface
     */
    public function getStorageUnitDatabase()
    {
        return $this->storageUnitDatabase;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface $storageUnitDatabase
     */
    public function setStorageUnitDatabase($storageUnitDatabase)
    {
        $this->storageUnitDatabase = $storageUnitDatabase;
    }

    /**
     * Create a storage unit in the storage layer
     *
     * @param $name string
     * @param $properties multitype:mixed
     * @param $indexes multitype:mixed
     * @return boolean
     */
    public function create($name, $properties, $indexes)
    {
        return $this->getStorageUnitDatabase()->createStorageUnit($name, $properties, $indexes);
    }

    /**
     * Determine whether a storage unit exists in the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function exists($name)
    {
        return $this->getStorageUnitDatabase()->storageUnitExists($name);
    }

    /**
     * Drop a storage unit from the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function drop($name)
    {
        return $this->getStorageUnitDatabase()->dropStorageUnit($name);
    }

    /**
     * Rename a storage unit
     *
     * @param string $old_name
     * @param string $new_name
     */
    public function rename($old_name, $new_name)
    {
        return $this->getStorageUnitDatabase()->renameStorageUnit($old_name, $new_name);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param multitype:mixed $attributes
     * @return boolean
     */
    public function alter($type, $table_name, $property, $attributes = array())
    {
        return $this->getStorageUnitDatabase()->alterStorageUnit($type, $table_name, $property, $attributes);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $name
     * @param multitype:string $columns
     * @return boolean
     */
    public function alterIndex($type, $table_name, $name = null, $columns = array())
    {
        return $this->getStorageUnitDatabase()->alterStorageUnitIndex($type, $table_name, $name, $columns);
    }

    /**
     * Truncate a storage unit in the storage layer and optionally optimize it afterwards
     *
     * @param $name string
     * @param $optimize boolean
     * @return boolean
     */
    public function truncate($name, $optimize = true)
    {
        if (! $this->getStorageUnitDatabase()->truncateStorageUnit($name))
        {
            return false;
        }

        if ($optimize && ! $this->optimizeStorageUnit($name))
        {
            return false;
        }

        return true;
    }

    /**
     * Optimize a storage unit in the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function optimize($name)
    {
        return $this->getStorageUnitDatabase()->optimizeStorageUnit($name);
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context());
    }
}