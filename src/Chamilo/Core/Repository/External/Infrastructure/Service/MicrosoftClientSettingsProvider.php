<?php
namespace Chamilo\Core\Repository\External\Infrastructure\Service;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\MicrosoftClient\MicrosoftClientSettingsProviderInterface;

/**
 * Settings provider for external repositories to support the microsoft client service
 *
 * @author Andras Zolnay - edufiles
 */
abstract class MicrosoftClientSettingsProvider extends ExternalRepositorySettingsProvider implements 
    MicrosoftClientSettingsProviderInterface
{

    /**
     * Constructor
     *
     * @param Instance $externalRepositoryInstance
     * @param User $user
     */
    public function __construct(Instance $externalRepositoryInstance, User $user)
    {
        parent :: __construct($externalRepositoryInstance, $user);
    }

    /**
     * Return the tenant need to constuct Microsoft service URL's.
     * Allowed values: common, organizations, consumers, and tenant identifiers.  
     * If no tenant given by user, 'common' is returned.
     *
     * @return string
     *
     * @see MicrosoftClientSettingsProviderInterface::getTenant()
     */
    public function getTenant()
    {
        $tenant = Setting::get('tenant', $this->externalRepositoryInstance->getId());

        if (empty($tenant))
        {
            return 'common';
        }

        return $tenant;
    }
    
    /**
     * Returns the security access token for the microsoft client
     *
     * @return \stdClass
     */
    public function getAccessToken()
    {
        $accessTokenString = $this->getUserSettingValue('session_token');
        if (! is_null($accessTokenString))
        {
            return json_decode($accessTokenString); 
        }
        
        return null;
    }

    /**
     * Stores the access token from the microsoft client
     *
     * @param \stdClass $accessToken
     *
     * @return bool
     */
    public function saveAccessToken($accessToken)
    {
        return $this->saveUserSetting('session_token', json_encode($accessToken));
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
}