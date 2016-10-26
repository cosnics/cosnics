<?php
namespace Chamilo\Configuration\Factory;

use Chamilo\Configuration\Service\CacheableAggregatedDataLoader;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\StorageConfigurationLoader;

/**
 *
 * @package Chamilo\Configuration\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationLoaderFactory
{

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    private $fileConfigurationLoader;

    /**
     *
     * @var \Chamilo\Configuration\Service\StorageConfigurationLoader
     */
    private $storageConfigurationLoader;

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLoader $fileConfigurationLoader
     * @param \Chamilo\Configuration\Service\StorageConfigurationLoader $storageConfigurationLoader
     */
    public function __construct(FileConfigurationLoader $fileConfigurationLoader,
        StorageConfigurationLoader $storageConfigurationLoader)
    {
        $this->fileConfigurationLoader = $fileConfigurationLoader;
        $this->storageConfigurationLoader = $storageConfigurationLoader;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    public function getFileConfigurationLoader()
    {
        return $this->fileConfigurationLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLoader $fileConfigurationLoader
     */
    public function setFileConfigurationLoader(FileConfigurationLoader $fileConfigurationLoader)
    {
        $this->fileConfigurationLoader = $fileConfigurationLoader;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\StorageConfigurationLoader
     */
    public function getStorageConfigurationLoader()
    {
        return $this->storageConfigurationLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\StorageConfigurationLoader $storageConfigurationLoader
     */
    public function setStorageConfigurationLoader(StorageConfigurationLoader $storageConfigurationLoader)
    {
        $this->storageConfigurationLoader = $storageConfigurationLoader;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\CacheableAggregatedDataLoader
     */
    public function getConfigurationLoader()
    {
        $dataLoaders = array();
        $dataLoaders[] = $this->getFileConfigurationLoader();
        $dataLoaders[] = $this->getStorageConfigurationLoader();

        return new CacheableAggregatedDataLoader($dataLoaders);
    }
}