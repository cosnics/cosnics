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
    public function __construct(TreeNode $treeNode, int $expectedSortValue)
    {
        parent::__construct(
            sprintf(
                'The given sort for tree node %s is invalid: %s given, expected %s', $treeNode->getId(),
                $treeNode->getSort(), $expectedSortValue
            )
        );
    }
}
