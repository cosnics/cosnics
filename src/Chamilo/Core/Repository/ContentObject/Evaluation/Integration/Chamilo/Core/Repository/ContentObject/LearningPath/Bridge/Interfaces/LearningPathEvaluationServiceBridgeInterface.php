<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\ContextIdentifier;

interface LearningPathEvaluationServiceBridgeInterface
{
    /**
     * @return int
     */
    public function getCurrentEntityType(): int;

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool;

    /**
     * @return bool
     */
    public function getReleaseScores(): bool;

    /**
     * @param TreeNode $treeNode
     * @return ContextIdentifier
     */
    public function getContextIdentifier(TreeNode $treeNode): ContextIdentifier;

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array;
}
