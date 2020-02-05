<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidTreeStructureException extends \Exception
{
    /**
     * InvalidTreeStructureException constructor.
     *
     * @param TreeNode $treeNode
     * @param int $expectedSortValue
     * @param int $expectedDepthValue
     */
    public function __construct(TreeNode $treeNode, int $expectedSortValue, int $expectedDepthValue)
    {
        parent::__construct(
            sprintf(
                'The given sort or depth for tree node %s is invalid. ' .
                '[Expected] Sort: %d, Depth: %s. [Given] Sort: %s, Depth: %s.',
                $treeNode->getId(), $expectedSortValue, $expectedDepthValue,
                $treeNode->getSort(), $treeNode->getDepth()
            )
        );
    }
}
