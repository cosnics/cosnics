<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;


/**
 * Class ApplicationFactory
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ApplicationFactory extends \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface
     */
    protected $presenceServiceBridge;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface $presenceServiceBridge
     */
    public function setPresenceServiceBridge(PresenceServiceBridgeInterface $presenceServiceBridge)
    {
        $this->presenceServiceBridge = $presenceServiceBridge;
    }

    public function getDefaultAction($context)
    {
        if ($this->presenceServiceBridge->canEditPresence()) {
            return 'Browser';
        }
        return Manager::ACTION_USER_PRESENCES;
    }

}