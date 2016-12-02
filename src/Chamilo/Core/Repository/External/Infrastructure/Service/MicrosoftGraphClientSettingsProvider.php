<?php
namespace Chamilo\Core\Repository\External\Infrastructure\Service;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Settings provider for external repositories to support the Microsoft Graph services
 * 
 * @author Andras Zolnay - edufiles
 */
class MicrosoftGraphClientSettingsProvider extends MicrosoftClientSettingsProvider
{

    /**
     * Constructor
     * 
     * @param Instance $externalRepositoryInstance
     * @param User $user
     */
    public function __construct(Instance $externalRepositoryInstance, User $user)
    {
        parent::__construct($externalRepositoryInstance, $user);
    }

    /**
     *
     * @see MicrosoftClientSettingsProviderInterface::getServiceBaseUrl()
     */
    public function getServiceBaseUrl()
    {
        return 'https://graph.microsoft.com/v1.0/me/';
    }

    /**
     *
     * @see MicrosoftClientSettingsProviderInterface::getOauth2Version()
     */
    public function getOauth2Version()
    {
        return 'v2.0';
    }

    /**
     *
     * @see MicrosoftClientSettingsProviderInterface::getScopeOrResource()
     */
    public function getScopeOrResource()
    {
        return array('https://graph.microsoft.com/Files.Read');
    }
}