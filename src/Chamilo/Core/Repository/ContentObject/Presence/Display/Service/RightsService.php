<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class RightsService
{
    /**
     * @var PresenceServiceBridgeInterface
     */
    protected $presenceServiceBridge;

    /**
     * @param PresenceServiceBridgeInterface $presenceServiceBridge
     */
    public function setPresenceServiceBridge(PresenceServiceBridgeInterface $presenceServiceBridge)
    {
        $this->presenceServiceBridge = $presenceServiceBridge;
    }

    /**
     * @return bool
     */
    public function canUserEditPresence(): bool
    {
        return $this->presenceServiceBridge->canEditPresence();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canUserViewPresence(User $user): bool
    {
        if ($this->canUserEditPresence())
        {
            return true;
        }

        return in_array($user->getId(), $this->presenceServiceBridge->getTargetUserIds());
    }
}