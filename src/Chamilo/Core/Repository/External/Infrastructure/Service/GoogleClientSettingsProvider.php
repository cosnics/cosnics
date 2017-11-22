<?php
namespace Chamilo\Core\Repository\External\Infrastructure\Service;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\GoogleClient\GoogleClientSettingsProviderInterface;

/**
 * Settings provider to support the google client service
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GoogleClientSettingsProvider extends ExternalRepositorySettingsProvider implements 
    GoogleClientSettingsProviderInterface
{

    /**
     * Scopes that enable access to particular resources
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
        parent::__construct($externalRepositoryInstance, $user);
        
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
     * Scopes enable access to particular resources
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
}