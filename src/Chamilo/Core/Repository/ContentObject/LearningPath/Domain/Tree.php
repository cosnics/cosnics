<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

/**
 * Full in memory tree of a learning path. This class can be used to traverse through the nested tree in memory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Tree
{
    /**
     * @var TreeNode[]
     */
    protected $treeNodes;

    /**
     * @var TreeNode[]
     */
    protected $treeNodesByStep;

    /**
     * @return TreeNode[]
     */
    public function getTreeNodes()
    {
        return $this->treeNodes;
    }

    /**
     * @param TreeNode[] $treeNodes
     */
    public function setTreeNodes($treeNodes)
    {
        $this->treeNodes = $treeNodes;
    }

    /**
     * Adds a TreeNode to the tree. This function is automatically called from the constructor of
     * TreeNode and should not be called by others. Adding the same node twice throws an exception
     *
     * @param TreeNode $treeNode
     */
    public function addTreeNode(TreeNode $treeNode)
    {
        if (in_array($treeNode, $this->treeNodes))
        {
            throw new \InvalidArgumentException(
                'The given learning path three node is already added to the list of nodes'
            );
        }

        $treeNode->setStep($this->getNextStep());
        $this->treeNodes[$treeNode->getId()] = $treeNode;
        $this->treeNodesByStep[$treeNode->getStep()] = $treeNode;
    }

    /**
     * Returns a TreeNode by a given identifier
     *
     * @param int $id
     *
     * @return TreeNode
     */
    public function getTreeNodeById($id)
    {
        if (!is_integer($id))
        {
            throw new \InvalidArgumentException('The given id should be valid integer');
        }

        if (!array_key_exists($id, $this->treeNodes))
        {
            throw new \InvalidArgumentException(
                sprintf('The learning path three node with id %s could not be found', $id)
            );
        }

        return $this->treeNodes[$id];
    }

    /**
     * Returns a TreeNode by a given step number
     * (only for internal use between Tree and TreeNode)
     *
     * @param int $step
     *
     * @return TreeNode
     */
    public function getTreeNodeByStep($step)
    {
        if (!is_integer($step))
        {
            throw new \InvalidArgumentException('The given step should be valid integer');
        }

        if (!array_key_exists($step, $this->treeNodesByStep))
        {
            throw new \InvalidArgumentException(
                sprintf('The learning path three node with step %s could not be found', $step)
            );
        }

        return $this->treeNodesByStep[$step];
    }

    /**
     * Returns the last step number
     *
     * @return int
     */
    public function getLastStepNumber()
    {
        return count($this->treeNodes);
    }

    /**
     * Returns the root node for the learning path
     *
     * @return TreeNode
     */
    public function getRoot()
    {
        if (empty($this->treeNodes))
        {
            throw new \RuntimeException(
                'The learning path tree does not have a root node yet. Please use the ' .
                'TreeBuilder service to populate the learning path tree'
            );
        }

        return $this->treeNodes[0];
    }

    /**
     * Returns the nodes that have a step number smaller than the given step number
     *
     * @return TreeNode[]
     */
    public function getNodesWithStepSmallerThan($stepNumber)
    {
        return array_slice($this->treeNodesByStep, 0, $stepNumber - 1);
    }

    /**
     * Returns the next step number for a TreeNode
     *
     * @return int
     */
    protected function getNextStep()
    {
        return $this->getLastStepNumber() + 1;
    }
}