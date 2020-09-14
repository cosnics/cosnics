<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidRootNodeException extends RubricStructureException
{
    /**
     * InvalidChildTypeException constructor.
     *
     * @param TreeNode $treeNode
     */
    public function __construct(TreeNode $treeNode)
    {
        parent::__construct(
            sprintf(
                'The tree node %s does not have a parent node and is not registered as root node',
                $treeNode->getId()
            )
        );
    }
}
