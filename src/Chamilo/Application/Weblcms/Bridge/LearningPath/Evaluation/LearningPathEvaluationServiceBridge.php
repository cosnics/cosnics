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
     * @var bool
     */
    protected $canEditEvaluation;

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
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::getPublicationTargetUserIds($this->publicationId, null);
    }
}