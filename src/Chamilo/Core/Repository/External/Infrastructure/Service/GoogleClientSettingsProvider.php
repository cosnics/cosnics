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
        return $this->externalRepositoryInstance->get_setting('developer_key');
    }

    /**
     * Returns the client id for the google client
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->externalRepositoryInstance->get_setting('client_id');
    }

    /**
     * Returns the client secret for the google client
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->externalRepositoryInstance->get_setting('client_secret');
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
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), $this->user->getId(), 'session_token'
        );

        if($setting)
        {
            return $setting->get_value();
        }

        return null;
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
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), $this->user->getId(), 'session_token'
        );

        if(!$setting)
        {
            $setting = new Setting();
        }

        $setting->set_external_id($this->externalRepositoryInstance->getId());
        $setting->set_variable('session_token');
        $setting->set_user_id($this->user->getId());
        $setting->set_value($accessToken);

        return $setting->save();
    }

    /**
     * Removes the access token
     *
     * @return bool
     */
    public function removeAccessToken()
    {
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), $this->user->getId(), 'session_token'
        );

        if($setting)
        {
            return $setting->delete();
        }

        return true;
    }
}