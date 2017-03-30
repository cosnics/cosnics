<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

/**
 * Full in memory tree of a learning path. This class can be used to traverse through the nested tree in memory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTree
{
    /**
     * @var LearningPathTreeNode[]
     */
    protected $learningPathTreeNodes;

    /**
     * @var LearningPathTreeNode[]
     */
    protected $learningPathTreeNodesByStep;

    /**
     * @return LearningPathTreeNode[]
     */
    public function getLearningPathTreeNodes()
    {
        return $this->learningPathTreeNodes;
    }

    /**
     * @param LearningPathTreeNode[] $learningPathTreeNodes
     */
    public function setLearningPathTreeNodes($learningPathTreeNodes)
    {
        $this->learningPathTreeNodes = $learningPathTreeNodes;
    }

    /**
     * Adds a LearningPathTreeNode to the tree. This function is automatically called from the constructor of
     * LearningPathTreeNode and should not be called by others. Adding the same node twice throws an exception
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    public function addLearningPathTreeNode(LearningPathTreeNode $learningPathTreeNode)
    {
        if (in_array($learningPathTreeNode, $this->learningPathTreeNodes))
        {
            throw new \InvalidArgumentException(
                'The given learning path three node is already added to the list of nodes'
            );
        }

        $learningPathTreeNode->setStep($this->getNextStep());
        $this->learningPathTreeNodes[$learningPathTreeNode->getId()] = $learningPathTreeNode;
        $this->learningPathTreeNodesByStep[$learningPathTreeNode->getStep()] = $learningPathTreeNode;
    }

    /**
     * @param int $id
     *
     * @return LearningPathTreeNode
     */
    public function getLearningPathTreeNodeById($id)
    {
        if (!is_integer($id))
        {
            throw new \InvalidArgumentException('The given id should be valid integer');
        }

        if (!array_key_exists($id, $this->learningPathTreeNodes))
        {
            throw new \InvalidArgumentException(
                sprintf('The learning path three node with id %s could not be found', $id)
            );
        }

        return $this->learningPathTreeNodes[$id];
    }

    /**
     * Returns the last step number
     *
     * @return int
     */
    public function getLastStepNumber()
    {
        return count($this->learningPathTreeNodes);
    }

    /**
     * Returns the root node for the learning path
     *
     * @return LearningPathTreeNode
     */
    public function getRoot()
    {
        if (empty($this->learningPathTreeNodes))
        {
            throw new \RuntimeException(
                'The learning path tree does not have a root node yet. Please use the ' .
                'LearningPathTreeBuilder service to populate the learning path tree'
            );
        }

        return $this->learningPathTreeNodes[0];
    }

    /**
     * Returns the nodes that have a step number smaller than the given step number
     *
     * @return LearningPathTreeNode[]
     */
    public function getNodesWithStepSmallerThan($stepNumber)
    {
        return array_slice($this->learningPathTreeNodesByStep, 0, $stepNumber - 1);
    }

    /**
     * Returns the next step number for a LearningPathTreeNode
     *
     * @return int
     */
    protected function getNextStep()
    {
        return $this->getLastStepNumber() + 1;
    }
}