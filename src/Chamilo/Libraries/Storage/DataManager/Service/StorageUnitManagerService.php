<?php
namespace Chamilo\Libraries\Storage\DataManager\Service;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Cache\DataManagerCache;
use Chamilo\Libraries\Storage\DataManager\StorageUnitManagerRepositoryInterface;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class StorageUnitManagerService
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\StorageUnitManagerRepositoryInterface
     */
    private $storageUnitManagerRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\StorageUnitManagerRepositoryInterface $storageUnitManagerRepository
     */
    public function __construct(Configuration $configuration, DataManagerCache $dataManagerCache,
        StorageUnitManagerRepositoryInterface $storageUnitManagerRepository)
    {
        $this->storageUnitManagerRepository = $storageUnitManagerRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\StorageUnitManagerRepositoryInterface
     */
    public function getStorageUnitManagerRepository()
    {
        return $this->storageUnitManagerRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\StorageUnitManagerRepositoryInterface $storageUnitManagerRepository
     */
    public function setStorageUnitManagerRepository($storageUnitManagerRepository)
    {
        $this->storageUnitManagerRepository = $storageUnitManagerRepository;
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
        return $this->getStorageUnitManagerRepository()->createStorageUnit($name, $properties, $indexes);
    }

    /**
     * Determine whether a storage unit exists in the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function exists($name)
    {
        return $this->getStorageUnitManagerRepository()->storageUnitExists($name);
    }

    /**
     * Drop a storage unit from the storage layer
     *
     * @param $name string
     * @return boolean
     */
    public function drop($name)
    {
        return $this->getStorageUnitManagerRepository()->dropStorageUnit($name);
    }

    /**
     * Rename a storage unit
     *
     * @param string $old_name
     * @param string $new_name
     */
    public function rename($old_name, $new_name)
    {
        return $this->getStorageUnitManagerRepository()->renameStorageUnit($old_name, $new_name);
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
        return $this->getStorageUnitManagerRepository()->alterStorageUnit($type, $table_name, $property, $attributes);
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
        return $this->getStorageUnitManagerRepository()->alterStorageUnitIndex($type, $table_name, $name, $columns);
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
        if (! $this->getStorageUnitManagerRepository()->truncateStorageUnit($name))
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
        return $this->getStorageUnitManagerRepository()->optimizeStorageUnit($name);
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