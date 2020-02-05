<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * In the domain model it is possible to change the sorts of a single treenode and possibly invalidate the model.
 * This class will validate the model and check if their are no disruptancies in the sort values of the children.
 */
class RubricValidator
{
    /**
     * @param RubricData $rubricData
     *
     * @throws InvalidTreeStructureException
     */
    public function validateRubric(RubricData $rubricData)
    {
        $this->validateTreeNode($rubricData->getRootNode());
    }

    /**
     * @param TreeNode $treeNode
     * @param int $expectedDepth
     * @param int $expectedSort
     *
     * @throws InvalidTreeStructureException
     */
    protected function validateTreeNode(TreeNode $treeNode, int $expectedDepth = 0, int $expectedSort = 1)
    {
        if($treeNode->getDepth() != $expectedDepth || $treeNode->getSort() != $expectedSort)
        {
            throw new InvalidTreeStructureException($treeNode, $expectedSort, $expectedDepth);
        }

        $children = $treeNode->getChildren()->getIterator();

        $children->uasort(
            function (TreeNode $treeNodeA, TreeNode $treeNodeB) {
                return ($treeNodeA->getSort() > $treeNodeB->getSort()) ? 1 : -1;
            }
        );

        $expectedSort = 1;
        foreach ($children as $child)
        {
            $this->validateTreeNode($child, $expectedDepth + 1, $expectedSort);

        }
    }
}
