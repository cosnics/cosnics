<?php
namespace Chamilo\Libraries\Storage\DataManager\Repository;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Repository
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
     * Create a storage unit
     *
     * @param string $storageUnitName
     * @param string[] $properties
     * @param string[] $indexes
     * @return boolean
     */
    public function create($storageUnitName, $properties, $indexes)
    {
        return $this->getStorageUnitDatabase()->create($storageUnitName, $properties, $indexes);
    }

    /**
     * Determine whether a storage unit exists
     *
     * @param string $storageUnitName
     * @return boolean
     */
    public function exists($storageUnitName)
    {
        return $this->getStorageUnitDatabase()->exists($storageUnitName);
    }

    /**
     * Drop a storage unit
     *
     * @param string $storageUnitName
     * @return boolean
     */
    public function drop($storageUnitName)
    {
        return $this->getStorageUnitDatabase()->drop($storageUnitName);
    }

    /**
     * Rename a storage unit
     *
     * @param string $oldStorageUnitName
     * @param string $newStorageUnitName
     * @return boolean
     */
    public function rename($oldStorageUnitName, $newStorageUnitName)
    {
        return $this->getStorageUnitDatabase()->rename($oldStorageUnitName, $newStorageUnitName);
    }

    /**
     * Alter a storage unit
     *
     * @param integer $type
     * @param string $storageUnitName
     * @param string $property
     * @param string[] $attributes
     * @return boolean
     */
    public function alter($type, $storageUnitName, $property, $attributes = array())
    {
        return $this->getStorageUnitDatabase()->alter($type, $storageUnitName, $property, $attributes);
    }

    /**
     * Alter a storage unit index
     *
     * @param integer $type
     * @param string $storageUnitName
     * @param string $indexName
     * @param string[] $columns
     * @return boolean
     */
    public function alterIndex($type, $storageUnitName, $indexName = null, $columns = array())
    {
        return $this->getStorageUnitDatabase()->alterIndex($type, $storageUnitName, $indexName, $columns);
    }

    /**
     * Truncate a storage unit and optionally optimize it afterwards
     *
     * @param $storageUnitName string
     * @param $optimize boolean
     * @return boolean
     */
    public function truncate($storageUnitName, $optimize = true)
    {
        if (! $this->getStorageUnitDatabase()->truncate($storageUnitName))
        {
            return false;
        }

        if ($optimize && ! $this->optimize($storageUnitName))
        {
            return false;
        }

        return true;
    }

    /**
     * Optimize a storage unit
     *
     * @param $storageUnitName string
     * @return boolean
     */
    public function optimize($storageUnitName)
    {
        return $this->getStorageUnitDatabase()->optimize($storageUnitName);
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