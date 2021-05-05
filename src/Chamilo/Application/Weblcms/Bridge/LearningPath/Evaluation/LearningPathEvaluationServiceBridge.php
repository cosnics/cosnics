<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Evaluation;

use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\ContextIdentifier;

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
     * @var integer
     */
    protected $currentEntityType;

    /**
     * @var bool
     */
    protected $canEditEvaluation;

    /**
     * @var bool
     */
    protected $releaseScores;

    /**
     * @param TreeNode $treeNode
     * @return ContextIdentifier
     */
    public function getContextIdentifier(TreeNode $treeNode): ContextIdentifier
    {
        // todo
        return new ContextIdentifier('', 0);
    }

    /**
     * @param int $publicationId
     */
    public function setPublicationId(int $publicationId)
    {
        $this->publicationId = $publicationId;
    }

    /**
     * @return int
     */
    public function getCurrentEntityType(): int
    {
        return $this->currentEntityType;
    }

    /**
     * @param int $currentEntityType
     */
    public function setCurrentEntityType(int $currentEntityType)
    {
        $this->currentEntityType = $currentEntityType;
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

    /**
     * @return bool
     */
    public function getReleaseScores(): bool
    {
        return $this->releaseScores;
    }

    /**
     * @param bool $releaseScores
     */
    public function setReleaseScores($releaseScores)
    {
        $this->releaseScores = $releaseScores;
    }

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::getPublicationTargetUserIds($this->publicationId, null);
    }
}