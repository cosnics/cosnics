<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Exception;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class UserSettingService
{
    use CacheAdapterHandlerTrait;

    protected ConfigurationService $configurationService;

    protected DatetimeUtilities $datetimeUtilities;

    protected UserService $userService;

    protected FilesystemAdapter $userSettingsCacheAdapter;

    public function __construct(
        UserService $userService, FilesystemAdapter $userSettingsCache, DatetimeUtilities $datetimeUtilities,
        ConfigurationService $configurationService
    )
    {
        $this->userService = $userService;
        $this->userSettingsCacheAdapter = $userSettingsCache;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->configurationService = $configurationService;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearSettingsCacheforUser(User $user): bool
    {
        return $this->clearCacheDataForAdapterAndKeyParts(
            $this->getUserSettingsCacheAdapter(), [User::class, $user->getId()]
        );
    }

    /**
     * @throws \Exception
     */
    public function convertDateToUserTimezone(User $user, string $date, ?string $format = null): string
    {
        $userTimezone = $this->getSettingForUser($user, 'Chamilo\Core\Admin', 'platform_timezone');

        return $this->getDatetimeUtilities()->convertDateToTimezone($date, $format, $userTimezone);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createUserSettingForSettingContextVariableAndUser(
        string $context, string $variable, User $user, ?string $value = null
    ): bool
    {
        $setting = $this->getConfigurationService()->findSettingByContextAndVariableName($context, $variable);

        if (!$this->getUserService()->createUserSettingFromParameters($setting->getId(), $user->getId(), $value))
        {
            return false;
        }

        return $this->clearSettingsCacheforUser($user);
    }

    public function getConfigurationService(): ConfigurationService
    {
        return $this->configurationService;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getSettingForUser(User $user, string $context, string $variable, bool $useCache = true): ?string
    {
        $settings = $this->getSettingsForUser($user, $useCache);

        return $settings[$context][$variable];
    }

    public function getSettingsForUser(User $user, bool $useCache = true): array
    {
        try
        {
            $userSettingsCacheAdapter = $this->getUserSettingsCacheAdapter();
            $userService = $this->getUserService();

            if ($useCache)
            {
                $cacheKeyParts = [User::class, $user->getId()];

                if (!$this->hasCacheDataForAdapterAndKeyParts($userSettingsCacheAdapter, $cacheKeyParts))
                {
                    $this->saveCacheDataForAdapterAndKeyParts(
                        $userSettingsCacheAdapter, $cacheKeyParts, $userService->findSettingsForUser($user)
                    );
                }

                return $this->readCacheDataForAdapterAndKeyParts($userSettingsCacheAdapter, $cacheKeyParts);
            }
            else
            {
                return $userService->findSettingsForUser($user);
            }
        }
        catch (Exception)
        {
            return [];
        }
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserSettingForSettingContextVariableAndUser(string $context, string $variable, User $user
    ): ?UserSetting
    {
        return $this->getUserService()->findUserSettingForSettingAndUser(
            $this->getConfigurationService()->findSettingByContextAndVariableName($context, $variable), $user
        );
    }

    public function getUserSettingsCacheAdapter(): FilesystemAdapter
    {
        return $this->userSettingsCacheAdapter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveUserSettingForSettingContextVariableAndUser(
        string $context, string $variable, User $user, ?string $value = null
    ): bool
    {
        $userSetting = $this->getUserSettingForSettingContextVariableAndUser($context, $variable, $user);

        if (!$userSetting instanceof UserSetting)
        {
            $setting = $this->getConfigurationService()->findSettingByContextAndVariableName($context, $variable);

            if (!$this->getUserService()->createUserSettingFromParameters($setting->getId(), $user->getId(), $value))
            {
                return false;
            }
        }
        elseif (!$this->getUserService()->updateUserSettingValue($userSetting, $value))
        {
            return false;
        }

        return $this->clearSettingsCacheforUser($user);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function updateUserSettingForSettingContextVariableAndUser(
        string $context, string $variable, User $user, ?string $value = null
    ): bool
    {
        $userSetting = $this->getUserSettingForSettingContextVariableAndUser($context, $variable, $user);

        if (!$userSetting instanceof UserSetting)
        {
            return false;
        }

        if (!$this->getUserService()->updateUserSettingValue($userSetting, $value))
        {
            return false;
        }

        return $this->clearSettingsCacheforUser($user);
    }
}

