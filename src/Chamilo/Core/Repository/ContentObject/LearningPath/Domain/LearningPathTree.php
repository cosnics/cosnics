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

        $nextStep = $this->getNextStep();

        $this->learningPathTreeNodes[$nextStep] = $learningPathTreeNode;
        $learningPathTreeNode->setStep($nextStep);
    }

    /**
     * @param int $step
     *
     * @return LearningPathTreeNode
     */
    public function getLearningPathTreeNodeByStep($step)
    {
        if (!is_integer($step))
        {
            throw new \InvalidArgumentException('The given step should be valid integer');
        }

        if ($step < 1 || $step > $this->getLastStepNumber())
        {
            throw new \RangeException(
                'The given step should be between 1 and ' . $this->getLastStepNumber()
            );
        }

        return $this->learningPathTreeNodes[$step];
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

        return $this->learningPathTreeNodes[1];
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