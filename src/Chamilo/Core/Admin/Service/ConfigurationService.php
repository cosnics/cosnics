<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Service\DataLoader\StorageConfigurationCacheDataPreLoader;
use Chamilo\Core\Admin\Storage\DataClass\Setting;
use Chamilo\Core\Admin\Storage\Repository\ConfigurationRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\CacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationService
{
    use CacheAdapterHandlerTrait;

    protected AdapterInterface $storageConfigurationCacheAdapter;

    protected UserService $userService;

    private ConfigurationRepository $configurationRepository;

    public function __construct(
        ConfigurationRepository $configurationRepository, AdapterInterface $storageConfigurationCacheAdapter,
        UserService $userService
    )
    {
        $this->configurationRepository = $configurationRepository;
        $this->storageConfigurationCacheAdapter = $storageConfigurationCacheAdapter;
        $this->userService = $userService;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCache(): bool
    {
        if (!$this->clearCacheDataForAdapterAndKeyParts(
            $this->getStorageConfigurationCacheAdapter(), [StorageConfigurationCacheDataPreLoader::class]
        )) {
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
        if (!$this->getConfigurationRepository()->createSetting($setting)) {
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
    public function createSettingFromParameters(string $context, string $variable, ?string $value = null): bool
    {
        $setting = new Setting();

        $setting->setContext($context);
        $setting->setVariable($variable);
        $setting->setValue($value);

        return $this->createSetting($setting);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function deleteSetting(Setting $setting): bool
    {
        if (!$this->getConfigurationRepository()->deleteSetting($setting)) {
            return false;
        }

        if (!$this->clearCache()) {
            return false;
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

        if (!$setting instanceof Setting) {
            return false;
        }
        else {
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
    public function findSettingContextsForCondition(?ConditionInterface $condition = null): array
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function updateSetting(Setting $setting): bool
    {
        if (!$this->getConfigurationRepository()->updateSetting($setting)) {
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
        string $context, string $variable, ?string $value = null
    ): bool
    {
        $setting = $this->findSettingByContextAndVariableName($context, $variable);

        if (!$setting instanceof Setting) {
            return false;
        }

        if (!is_null($value)) {
            $setting->setValue($value);
        }

        return $this->updateSetting($setting);
    }
}