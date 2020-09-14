<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidChildTypeException extends RubricStructureException
{
    /**
     * InvalidChildTypeException constructor.
     *
     * @param TreeNode $treeNode
     * @param TreeNode $childNode
     */
    public function __construct(TreeNode $treeNode, TreeNode $childNode)
    {
        parent::__construct(
            sprintf(
                'The tree node %s does not allow children of type %s to be added. Allowed types are %s',
                $treeNode->getId(), get_class($childNode), implode(', ', $treeNode->getAllowedChildTypes())
            )
        );
    }
}
