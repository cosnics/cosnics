<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Bridge;

use Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathPresenceServiceBridgeInterface;
use Chamilo\Core\Repository\Test\Acceptance\Behat\Context;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

class PresenceServiceBridge implements LearningPathPresenceServiceBridgeInterface
{
    /**
     * @param int $stepId
     * @return ContextIdentifier
     */
    public function getContextIdentifier(int $stepId): ContextIdentifier
    {
        return new ContextIdentifier('preview', 0);
    }

    /**
     * @return bool
     */
    public function canEditPresence(): bool
    {
        return true;
    }

    /**
     * @param FilterParameters|null $filterParameters
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array
    {
        return [];
    }

    public function getContextTitle(): string
    {
        return 'preview';
    }
}
