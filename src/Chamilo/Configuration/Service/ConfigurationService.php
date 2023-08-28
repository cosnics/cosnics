<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Service\DataLoader\AggregatedCacheDataPreLoader;
use Chamilo\Configuration\Service\DataLoader\StorageConfigurationCacheDataPreLoader;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\Repository\ConfigurationRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationService
{
    use CacheAdapterHandlerTrait;

    protected AdapterInterface $configurationCacheAdapter;

    protected AdapterInterface $storageConfigurationCacheAdapter;

    protected UserService $userService;

    protected FilesystemAdapter $userSettingsCacheAdapter;

    private ConfigurationRepository $configurationRepository;

    public function __construct(
        ConfigurationRepository $configurationRepository, AdapterInterface $configurationCacheAdapter,
        AdapterInterface $storageConfigurationCacheAdapter, FilesystemAdapter $userSettingsCacheAdapter,
        UserService $userService
    )
    {
        $this->configurationRepository = $configurationRepository;
        $this->configurationCacheAdapter = $configurationCacheAdapter;
        $this->storageConfigurationCacheAdapter = $storageConfigurationCacheAdapter;
        $this->userSettingsCacheAdapter = $userSettingsCacheAdapter;
        $this->userService = $userService;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCache(): bool
    {
        if (!$this->clearCacheDataForAdapterAndKeyParts(
            $this->getStorageConfigurationCacheAdapter(), [StorageConfigurationCacheDataPreLoader::class]
        ))
        {
            return false;
        }

        if (!$this->clearCacheDataForAdapterAndKeyParts(
            $this->getConfigurationCacheAdapter(), [AggregatedCacheDataPreLoader::class]
        ))
        {
            return false;
        }

        return true;
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

        if (!$this->clearCache())
        {
            return false;
        }

        if ($setting->get_user_setting())
        {
            if (!$this->getUserService()->deleteUserSettingsForSettingIdentifier($setting->getId()))
            {
                return false;
            }
            else
            {
                return $this->clearAllCacheDataForAdapter($this->getUserSettingsCacheAdapter());
            }
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
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

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserSettingsCacheAdapter(): FilesystemAdapter
    {
        return $this->userSettingsCacheAdapter;
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

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function updateSettingFromParameters(
        string $context, string $variable, ?string $value = null, ?bool $isUserSetting = null
    ): bool
    {
        $setting = $this->findSettingByContextAndVariableName($context, $variable);

        if (!$setting instanceof Setting)
        {
            return false;
        }

        if (!is_null($value))
        {
            $setting->set_value($value);
        }

        if (!is_null($isUserSetting))
        {
            $setting->set_user_setting((int) $isUserSetting);
        }

        return $this->updateSetting($setting);
    }
}