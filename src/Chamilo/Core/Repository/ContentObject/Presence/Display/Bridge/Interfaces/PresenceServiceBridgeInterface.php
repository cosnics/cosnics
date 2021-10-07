<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces;

use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

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

    /**
     * @param FilterParameters|null $filterParameters
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array;

}