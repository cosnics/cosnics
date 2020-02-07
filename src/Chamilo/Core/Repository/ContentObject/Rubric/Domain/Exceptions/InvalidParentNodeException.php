<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidParentNodeException extends RubricStructureException
{
    /**
     * InvalidParentNodeException constructor.
     *
     * @param TreeNode $treeNode
     * @param TreeNode|null $expectedParentNode
     */
    public function __construct(TreeNode $treeNode, Treenode $expectedParentNode = null)
    {
        parent::__construct(
            sprintf(
                'The parent node of treenode %s is incorrect. [Expected] %s. [Given] %s.',
                $treeNode->getId(),
                is_null($expectedParentNode) ? 'no parent' : $expectedParentNode->getId(),
                is_null($treeNode->getParentNode()) ? 'no parent' : $treeNode->getParentNode()->getId()
            )
        );
    }
}
