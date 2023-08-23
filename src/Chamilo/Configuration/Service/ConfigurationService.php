<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Service\DataLoader\AggregatedCacheDataPreLoader;
use Chamilo\Configuration\Service\DataLoader\StorageConfigurationCacheDataPreLoader;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\Repository\ConfigurationRepository;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationService
{
    use CacheAdapterHandlerTrait;

    protected AdapterInterface $configurationCacheAdapter;

    protected AdapterInterface $storageConfigurationCacheAdapter;

    private ConfigurationRepository $configurationRepository;

    public function __construct(
        ConfigurationRepository $configurationRepository, AdapterInterface $configurationCacheAdapter,
        AdapterInterface $storageConfigurationCacheAdapter
    )
    {
        $this->configurationRepository = $configurationRepository;
        $this->configurationCacheAdapter = $configurationCacheAdapter;
        $this->storageConfigurationCacheAdapter = $storageConfigurationCacheAdapter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCache(): bool
    {
        $this->clearCacheDataForAdapterAndKeyParts(
            $this->getStorageConfigurationCacheAdapter(), [StorageConfigurationCacheDataPreLoader::class]
        );
        $this->clearCacheDataForAdapterAndKeyParts(
            $this->getConfigurationCacheAdapter(), [AggregatedCacheDataPreLoader::class]
        );

        return $this->getConfigurationRepository()->clearSettingCache();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createSetting(Setting $setting): bool
    {
        if (!$this->getConfigurationRepository()->createSetting($setting))
        {
            return false;
        }

        $this->clearCache();

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createSettingFromParameters(
        string $context, string $variable, ?string $value = null, bool $isUserSetting = false
    ): bool
    {
        $setting = new Setting();

        $setting->set_context($context);
        $setting->set_variable($variable);
        $setting->set_value($value);
        $setting->set_user_setting((int) $isUserSetting);

        return $this->createSetting($setting);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function deleteSetting(Setting $setting): bool
    {
        if (!$this->getConfigurationRepository()->deleteSetting($setting))
        {
            return false;
        }

        //TODO: Need to do something about user settings here @see Setting::delete()

        $this->clearCache();

        return true;
    }

    public function deleteSettingForContextAndVariableName(string $context, string $variableName): bool
    {
        $setting = $this->findSettingByContextAndVariableName($context, $variableName);

        if (!$setting instanceof Setting)
        {
            return false;
        }
        else
        {
            return $this->deleteSetting($setting);
        }
    }

    public function findSettingByContextAndVariableName(string $context, string $variable): ?Setting
    {
        return $this->getConfigurationRepository()->findSettingByContextAndVariableName($context, $variable);
    }

    public function getConfigurationCacheAdapter(): AdapterInterface
    {
        return $this->configurationCacheAdapter;
    }

    protected function getConfigurationRepository(): ConfigurationRepository
    {
        return $this->configurationRepository;
    }

    public function getStorageConfigurationCacheAdapter(): AdapterInterface
    {
        return $this->storageConfigurationCacheAdapter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function updateSetting(Setting $setting): bool
    {
        if (!$this->getConfigurationRepository()->updateSetting($setting))
        {
            return false;
        }

        $this->clearCache();

        return true;
    }
}