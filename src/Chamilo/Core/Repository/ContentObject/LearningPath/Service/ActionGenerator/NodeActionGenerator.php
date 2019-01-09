<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\ActionInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;

/**
 * Base class to generate actions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class NodeActionGenerator extends ActionGenerator
{

    /**
     * Generates the acions for a given TreeNode
     *
     * @param TreeNode $treeNode
     * @param bool $canEditTreeNode
     * @param bool $canViewReporting
     *
     * @return array|ActionInterface[]
     */
    abstract public function generateNodeActions(
        TreeNode $treeNode, bool $canEditTreeNode = false, bool $canViewReporting = false
    ): array;
}