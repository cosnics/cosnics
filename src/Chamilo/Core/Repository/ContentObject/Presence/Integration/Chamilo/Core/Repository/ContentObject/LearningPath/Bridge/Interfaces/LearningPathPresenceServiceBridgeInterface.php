<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces;

use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

interface LearningPathPresenceServiceBridgeInterface
{
    /**
     * @param int $stepId
     * @return ContextIdentifier
     */
    public function getContextIdentifier(int $stepId): ContextIdentifier;

    /**
     * @return bool
     */
    public function canEditPresence(): bool;

    /**
     * @param FilterParameters|null $filterParameters
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array;
}
