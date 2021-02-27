<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidCriteriumException;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidParentNodeException;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRootNodeException;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidRubricDataException;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidTreeStructureException;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
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
     * @throws RubricStructureException
     */
    public function validateRubric(RubricData $rubricData)
    {
        $this->validateTreeNode($rubricData->getRootNode(), $rubricData);
        $this->validateRubricDataCollections($rubricData);
    }

    /**
     * @param TreeNode $treeNode
     * @param RubricData $rubricData
     * @param int $expectedDepth
     * @param int $expectedSort
     * @param TreeNode|null $expectedParentNode
     *
     * @throws RubricStructureException
     * @throws \Exception
     */
    protected function validateTreeNode(
        TreeNode $treeNode, RubricData $rubricData, int $expectedDepth = 0, int $expectedSort = 1,
        TreeNode $expectedParentNode = null
    )
    {
        if ($treeNode->getDepth() != $expectedDepth || $treeNode->getSort() != $expectedSort)
        {
            throw new InvalidTreeStructureException($treeNode, $expectedSort, $expectedDepth);
        }

        if ($treeNode->getRubricData() !== $rubricData)
        {
            throw new InvalidRubricDataException(
                'tree node', $treeNode->getId(), $rubricData, $treeNode->getRubricData()
            );
        }

        if ($treeNode->getParentNode() !== $expectedParentNode)
        {
            throw new InvalidParentNodeException($treeNode, $expectedParentNode);
        }

        if ($treeNode instanceof CriteriumNode)
        {
            $this->validateChoices($treeNode, $rubricData);
        }

        $children = $treeNode->getChildren()->getIterator();

        $children->uasort(
            function (TreeNode $treeNodeA, TreeNode $treeNodeB) {
                return ($treeNodeA->getSort() > $treeNodeB->getSort()) ? 1 : - 1;
            }
        );

        $expectedSort = 1;
        foreach ($children as $child)
        {
            $this->validateTreeNode($child, $rubricData, $expectedDepth + 1, $expectedSort, $treeNode);
            $expectedSort ++;
        }
    }

    /**
     * @param CriteriumNode $criteriumNode
     * @param RubricData $rubricData
     *
     * @throws RubricStructureException
     */
    protected function validateChoices(CriteriumNode $criteriumNode, RubricData $rubricData)
    {
        foreach ($criteriumNode->getChoices() as $choice)
        {
            if ($choice->getRubricData() !== $rubricData)
            {
                throw new InvalidRubricDataException(
                    'choice', $choice->getId(), $rubricData, $choice->getRubricData()
                );
            }

            if($choice->getCriterium() !== $criteriumNode)
            {
                throw new InvalidCriteriumException($choice, $criteriumNode);
            }
        }
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws RubricStructureException
     */
    protected function validateRubricDataCollections(RubricData $rubricData)
    {
        foreach ($rubricData->getTreeNodes() as $treeNode)
        {
            if ($treeNode->getRubricData() !== $rubricData)
            {
                throw new InvalidRubricDataException(
                    'tree node', $treeNode->getId(), $rubricData, $treeNode->getRubricData()
                );
            }

            if(empty($treeNode->getParentNode()) && $treeNode !== $rubricData->getRootNode())
            {
                throw new InvalidRootNodeException($treeNode);
            }
        }

        foreach ($rubricData->getLevels() as $level)
        {
            if ($level->getRubricData() !== $rubricData)
            {
                throw new InvalidRubricDataException(
                    'level', $level->getId(), $rubricData, $level->getRubricData()
                );
            }
        }

        foreach ($rubricData->getChoices() as $choice)
        {
            if ($choice->getRubricData() !== $rubricData)
            {
                throw new InvalidRubricDataException(
                    'level', $choice->getId(), $rubricData, $choice->getRubricData()
                );
            }
        }
    }
}
