<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Service\DataLoader\StorageConfigurationCacheDataPreLoader;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\Repository\ConfigurationRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationService
{
    use CacheAdapterHandlerTrait;

    protected AdapterInterface $storageConfigurationCacheAdapter;

    protected UserService $userService;

    protected FilesystemAdapter $userSettingsCacheAdapter;

    private ConfigurationRepository $configurationRepository;

    public function __construct(
        ConfigurationRepository $configurationRepository, AdapterInterface $storageConfigurationCacheAdapter,
        FilesystemAdapter $userSettingsCacheAdapter, UserService $userService
    )
    {
        $this->configurationRepository = $configurationRepository;
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

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createSettingFromParameters(
        string $context, string $variable, ?string $value = null, bool $isUserSetting = false
    ): bool
    {
        $setting = new Setting();

        $setting->setContext($context);
        $setting->setVariable($variable);
        $setting->setValue($value);
        $setting->setUserSetting((int) $isUserSetting);

        return $this->createSetting($setting);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

        if ($setting->getUserSetting())
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findSettingByContextAndVariableName(string $context, string $variable): ?Setting
    {
        return $this->getConfigurationRepository()->findSettingByContextAndVariableName($context, $variable);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findSettingContextsForCondition(?Condition $condition = null): array
    {
        return $this->getConfigurationRepository()->findSettingContextsForCondition($condition);
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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
            $setting->setValue($value);
        }

        if (!is_null($isUserSetting))
        {
            $setting->setUserSetting((int) $isUserSetting);
        }

        return $this->updateSetting($setting);
    }
}