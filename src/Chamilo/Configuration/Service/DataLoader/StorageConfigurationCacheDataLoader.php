<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\Repository\ConfigurationRepository;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Interfaces\CacheDataReaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheDataLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class StorageConfigurationCacheDataLoader implements CacheDataLoaderInterface, CacheDataReaderInterface
{
    use CacheDataLoaderTrait;

    protected ConfigurationRepository $configurationRepository;

    public function __construct(AdapterInterface $cacheAdapter, ConfigurationRepository $configurationRepository)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->configurationRepository = $configurationRepository;
    }

    public function getConfigurationRepository(): ConfigurationRepository
    {
        return $this->configurationRepository;
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getDataForCache(): array
    {
        $settings = [];
        $settingRecords = $this->getConfigurationRepository()->findSettingsAsRecords();

        foreach ($settingRecords as $settingRecord)
        {
            $settings[$settingRecord[Setting::PROPERTY_CONTEXT]][$settingRecord[Setting::PROPERTY_VARIABLE]] =
                $settingRecord[Setting::PROPERTY_VALUE];
        }

        return $settings;
    }
}
