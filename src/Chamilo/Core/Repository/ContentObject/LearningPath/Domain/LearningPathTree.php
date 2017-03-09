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
     * LearningPathTree constructor.
     *
     * @param LearningPathTreeNode[] $learningPathTreeNodes
     */
    public function __construct(array $learningPathTreeNodes)
    {
        $this->setLearningPathTreeNodes($learningPathTreeNodes);
    }

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
     * Adds a LearningPathTreeNode to the tree
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    public function addLearningPathTreeNode(LearningPathTreeNode $learningPathTreeNode)
    {
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

        if (!array_key_exists($step, $this->learningPathTreeNodes))
        {
            throw new \InvalidArgumentException(
                'The system could not find a valid LearningPathThreeNode with the given step ' . $step
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
     * Returns the next step number for a LearningPathTreeNode
     *
     * @return int
     */
    protected function getNextStep()
    {
        return count($this->learningPathTreeNodes) + 1;
    }



}