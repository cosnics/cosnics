<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorRegistry;
use Chamilo\Core\User\Storage\DataClass\User;
use Throwable;

/**
 * @package Chamilo\Core\User\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class UserSettingsService
{
    public function __construct(
        protected SettingsConnectorRegistry $settingsConnectorRegistry, protected UserService $userService,
        protected UserSettingsParser $userSettingsParser
    )
    {
    }

    public function findUserSetting(User $user, string $variable, mixed $defaultValue = null)
    {
        return $user->getSetting($variable, $defaultValue);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function isSettingAvailable(string $context, array $setting): bool
    {
        $settingsConnector = $this->settingsConnectorRegistry->getSettingsConnectorForContext($context);
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
    public function updateUserSetting(User $user, string $variable, mixed $value = null, ?User $executingUser = null
    ): void
    {
        $user->setSetting($variable, $value);
        $this->userService->updateUser($user, $executingUser);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function updateUserSettingsFromParameters(
        User $user, string $context, array $values, ?User $executingUser = null
    ): bool
    {
        $problems = 0;
        $configuration = $this->userSettingsParser->determineConfigurablePackageContextSettings($context);

        foreach ($configuration as $category) {
            foreach ($category as $name => $setting) {
                if (!$this->isSettingAvailable($context, $setting)) {
                    continue;
                }

                try {
                    $this->updateUserSetting($user, $name, $values[$name], $executingUser);
                }
                catch (Throwable) {
                    $problems ++;
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