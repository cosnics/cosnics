<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorRegistry;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserSettingsService
{
    protected SettingsConnectorRegistry $settingsConnectorRegistry;

    protected UserService $userService;

    protected UserSettingsParser $userSettingsParser;

    public function __construct(
        SettingsConnectorRegistry $settingsConnectorRegistry, UserService $userService,
        UserSettingsParser $userSettingsParser
    )
    {
        $this->settingsConnectorRegistry = $settingsConnectorRegistry;
        $this->userService = $userService;
        $this->userSettingsParser = $userSettingsParser;
    }

    public function getSettingsConnectorRegistry(): SettingsConnectorRegistry
    {
        return $this->settingsConnectorRegistry;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserSettingsParser(): UserSettingsParser
    {
        return $this->userSettingsParser;
    }

    public function isSettingAvailable(string $context, array $setting): bool
    {
        $settingsConnector = $this->getSettingsConnectorRegistry()->getSettingsConnectorForContext($context);
        $isHidden = $this->isSettingHidden($setting);

        $availabilitySource = $setting['availability']['source'];
        $hasAvailabilityMethod = $setting['availability'] && $availabilitySource;

        if (!$isHidden) {
            if ($hasAvailabilityMethod) {
                if (method_exists($settingsConnector, $availabilitySource)) {
                    return $settingsConnector->$availabilitySource();
                }
                else {
                    return false;
                }
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    protected function isSettingHidden($setting): bool
    {
        return isset($setting['hidden']) && ($setting['hidden'] == 1 || $setting['hidden'] == 'true');
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserSetting(User $user, string $variable, mixed $value = null): bool
    {
        $user->setSetting($variable, $value);

        return $this->getUserService()->updateUser($user);
    }

    public function findUserSetting(User $user, string $variable, mixed $defaultValue = null)
    {
        return $user->getSetting($variable, $defaultValue);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserSettingsFromParameters(User $user, string $context, array $values): bool
    {
        $problems = 0;
        $configuration = $this->getUserSettingsParser()->determineConfigurablePackageContextSettings($context);

        foreach ($configuration as $settings) {
            foreach ($settings as $name => $setting) {
                if (!$this->isSettingAvailable($context, $setting)) {
                    continue;
                }

                if ($setting['locked'] != 'true' && $setting['user_setting']) {
                    if (!$this->updateUserSetting(
                        $user, $name, $values[$name]
                    )) {
                        $problems ++;
                    }
                }
            }
        }

        if ($problems > 0) {
            return false;
        }
        else {
            return true;
        }
    }
}