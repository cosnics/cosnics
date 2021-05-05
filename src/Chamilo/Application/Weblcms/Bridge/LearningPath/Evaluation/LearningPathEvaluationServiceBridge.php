<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Evaluation;

use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Evaluation
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathEvaluationServiceBridge implements LearningPathEvaluationServiceBridgeInterface
{
    /**
     * @var integer
     */
    protected $publicationId;

    /**
     * @var bool
     */
    protected $canEditEvaluation;

    /**
     * @return int
     */
    public function getPublicationId(): int
    {
        return $this->publicationId;
    }

    /**
     * @param int $publicationId
     */
    public function setPublicationId(int $publicationId)
    {
        $this->publicationId = $publicationId;
    }

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool
    {
        return $this->canEditEvaluation;
    }

    /**
     * @param bool $canEditEvaluation
     */
    public function setCanEditEvaluation($canEditEvaluation = true)
    {
        $this->canEditEvaluation = $canEditEvaluation;
    }
}