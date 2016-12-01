<?php
namespace Chamilo\Core\Repository\External\Infrastructure\Service;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Base class for external repository settings providers
 * 
 * @author Andras Zolnay - edufiles
 */
class ExternalRepositorySettingsProvider
{

    /**
     * The external repository instance
     * 
     * @var Instance
     */
    protected $externalRepositoryInstance;

    /**
     * The user that accesses the external repository service
     * 
     * @var User
     */
    protected $user;

    /**
     * Constructor
     * 
     * @param Instance $externalRepositoryInstance
     * @param User $user
     */
    public function __construct(Instance $externalRepositoryInstance, User $user)
    {
        $this->externalRepositoryInstance = $externalRepositoryInstance;
        $this->user = $user;
    }

    /**
     * Returns the client id
     * 
     * @return string
     */
    public function getClientId()
    {
        return Setting::get('client_id', $this->externalRepositoryInstance->getId());
    }

    /**
     * Returns the client secret
     * 
     * @return string
     */
    public function getClientSecret()
    {
        return Setting::get('client_secret', $this->externalRepositoryInstance->getId());
    }

    /**
     * Saves a user setting for the given variable with the given value
     * 
     * @param string $variable
     * @param string $value
     *
     * @return bool
     */
    public function saveUserSetting($variable, $value)
    {
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), 
            $this->user->getId(), 
            $variable);
        
        if (! $setting)
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
    public function getUserSettingValue($variable)
    {
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), 
            $this->user->getId(), 
            $variable);
        
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
    public function removeUserSetting($variable)
    {
        $setting = DataManager::retrieveUserSetting(
            $this->externalRepositoryInstance->getId(), 
            $this->user->getId(), 
            $variable);
        
        if ($setting)
        {
            return $setting->delete();
        }
        
        return true;
    }
}