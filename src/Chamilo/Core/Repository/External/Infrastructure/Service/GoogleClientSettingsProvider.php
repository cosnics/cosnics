<?php

namespace Chamilo\Core\Repository\External\Infrastructure\Service;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\GoogleClient\GoogleClientSettingsProviderInterface;

/**
 * Settings provider to support the google client service
 *
 * @package common\extensions\external_repository_manager\implementation\youtube
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GoogleClientSettingsProvider implements GoogleClientSettingsProviderInterface
{
    /**
     * The external repository instance
     *
     * @var Instance
     */
    protected $externalRepositoryInstance;

    /**
     * The user that uses the google client service
     *
     * @var User
     */
    protected $user;

    /**
     * The scopes
     *
     * @var string
     */
    protected $scopes;

    /**
     * Constructor
     *
     * @param Instance $externalRepositoryInstance
     * @param User $user
     * @param string $scopes
     */
    public function __construct(Instance $externalRepositoryInstance, User $user, $scopes)
    {
        $this->externalRepositoryInstance = $externalRepositoryInstance;
        $this->user = $user;
        $this->scopes = $scopes;
    }

    /**
     * Returns the developer key for the google client
     *
     * @return string
     */
    public function getDeveloperKey()
    {
        return Setting::get('developer_key', $this->externalRepositoryInstance->getId());
    }

    /**
     * Returns the client id for the google client
     *
     * @return string
     */
    public function getClientId()
    {
        return Setting::get('client_id', $this->externalRepositoryInstance->getId());
    }

    /**
     * Returns the client secret for the google client
     *
     * @return string
     */
    public function getClientSecret()
    {
        return Setting::get('client_secret', $this->externalRepositoryInstance->getId());
    }

    /**
     * Returns the scopes for the google client
     *
     * @return string
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Returns the security access token for the google client
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->getUserSettingValue('session_token');
    }

    /**
     * Returns the security refresh token for the google client
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->getUserSettingValue('refresh_token');
    }

    /**
     * Stores the access token from the google client into chamilo
     *
     * @param string $accessToken
     *
     * @return bool
     */
    public function saveAccessToken($accessToken)
    {
        return $this->saveUserSetting('session_token', $accessToken);
    }

    /**
     * Stores the refresh token
     *
     * @param $refreshToken
     *
     * @return bool
     */
    public function saveRefreshToken($refreshToken)
    {
        return $this->saveUserSetting('refresh_token', $refreshToken);
    }

    /**
     * Removes the access token
     *
     * @return bool
     */
    public function removeAccessToken()
    {
        return $this->removeUserSetting('session_token');
    }

    /**
     * Removes the refresh token
     *
     * @return bool
     */
    public function removeRefreshToken()
    {
        return $this->removeUserSetting('refresh_token');
    }

    /**
     * Saves a user setting for the given variable with the given value
     *
     *
     * @param string $variable
     * @param string $value
     *
     * @return bool
     */
    protected function saveUserSetting($variable, $value)
    {
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), $this->user->getId(), $variable
        );

        if (!$setting)
        {
            $setting = new Setting();
        }

        $setting->set_external_id($this->externalRepositoryInstance->getId());
        $setting->set_variable($variable);
        $setting->set_user_id($this->user->getId());
        $setting->set_value($value);

        return $setting->save();
    }

    /**
     * Returns the value for the user setting for the given variable
     * 
     * @param string $variable
     *
     * @return string
     */
    protected function getUserSettingValue($variable)
    {
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), $this->user->getId(), $variable
        );

        if ($setting)
        {
            return $setting->get_value();
        }

        return null;
    }

    /**
     * Removes a user setting
     *
     * @param string $variable
     *
     * @return bool
     */
    protected function removeUserSetting($variable)
    {
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), $this->user->getId(), $variable
        );

        if ($setting)
        {
            return $setting->delete();
        }

        return true;
    }
}