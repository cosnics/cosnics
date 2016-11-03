<?php

namespace Chamilo\Core\Repository\External\Infrastructure\Service;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Settings provider for external repositories to support the Microsoft SharePoint service
 *
 * @author Andras Zolnay - edufiles
 */
class MicrosoftSharePointClientSettingsProvider extends MicrosoftClientSettingsProvider
{
    /**
     * Constructor
     *
     * @param Instance $externalRepositoryInstance
     * @param User $user
     * @param string or array @see MicrosoftClientSettingsProviderInterface::getScopeOrResource()
     */
    public function __construct(Instance $externalRepositoryInstance, User $user)
    {
        parent :: __construct($externalRepositoryInstance, $user);
    }

    /**
     *  @see MicrosoftClientSettingsProviderInterface::getServiceBaseUrl()
     */
    public function getServiceBaseUrl()
    {
        return $this->getScopeOrResource() . "portals/hub/_api/";
    }

    /**
     *  @see MicrosoftClientSettingsProviderInterface::getOauth2Version()
     */
    public function getOauth2Version()
    {
        return '';
    }

    /**
     *  @see MicrosoftClientSettingsProviderInterface::getScopeOrResource()
     */
    public function getScopeOrResource()
    {
        $resource = Setting::get('root_site', $this->externalRepositoryInstance->getId());
        
        if ($resource[strlen($resource)] != '/')
        {
            return $resource . '/';
        }
        else
        {
            return $resource;
        }
    }
}