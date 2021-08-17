<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces;

use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Interfaces
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
interface PresenceServiceBridgeInterface
{
    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier;

    /**
     * @return boolean
     */
    public function canEditPresence(): bool;
}