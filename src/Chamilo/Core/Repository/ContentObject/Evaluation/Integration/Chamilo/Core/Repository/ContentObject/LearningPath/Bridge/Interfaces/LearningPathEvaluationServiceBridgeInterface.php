<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces;

interface LearningPathEvaluationServiceBridgeInterface
{
    /**
     * @return int
     */
    public function getPublicationId(): int;

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool;
}
