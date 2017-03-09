<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathContentObjectRelation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * A single node for a learning path tree. Used in an in-memory learning path tree
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTreeNode
{
    /**
     * The identifier of this node by a step
     *
     * @var int
     */
    protected $step;

    /**
     * @var LearningPathTree
     */
    protected $learningPathTree;

    /**
     * @var LearningPathContentObjectRelation
     */
    protected $learningPathContentObjectRelation;

    /**
     * @var ContentObject
     */
    protected $contentObject;

    /**
     * The direct parent of this node
     *
     * @var LearningPathTreeNode
     */
    protected $parentNode;

    /**
     * Every parent of this node
     *
     * @var LearningPathTreeNode[]
     */
    protected $parentNodes;

    /**
     * The direct children of this node
     *
     * @var LearningPathTreeNode[]
     */
    protected $childNodes;

    /**
     * Every child of this node (even those of subnodes)
     *
     * @var LearningPathTreeNode[]
     */
    protected $descendantNodes;

    /**
     * LearningPathTreeNode constructor.
     *
     * @param LearningPathTree $learningPathTree
     * @param ContentObject $contentObject
     */
    public function __construct(LearningPathTree $learningPathTree, ContentObject $contentObject)
    {
        $this->setLearningPathTree($learningPathTree);
        $this->setContentObject($contentObject);
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param int $step
     *
     * @return LearningPathTreeNode
     */
    public function setStep($step)
    {
        if (!is_integer($step))
        {
            throw new \InvalidArgumentException('The given step should be valid integer');
        }

        if ($step < 1 || $step > $this->learningPathTree->getLastStepNumber())
        {
            throw new \RangeException(
                'The given step should be between 1 and ' . $this->learningPathTree->getLastStepNumber()
            );
        }

        $this->step = $step;

        return $this;
    }

    /**
     * @return LearningPathTree
     */
    public function getLearningPathTree()
    {
        return $this->learningPathTree;
    }

    /**
     * @param LearningPathTree $learningPathTree
     *
     * @return LearningPathTreeNode
     */
    public function setLearningPathTree(LearningPathTree $learningPathTree)
    {
        $this->learningPathTree = $learningPathTree;

        return $this;
    }

    /**
     * @return LearningPathContentObjectRelation
     */
    public function getLearningPathContentObjectRelation()
    {
        return $this->learningPathContentObjectRelation;
    }

    /**
     * @param LearningPathContentObjectRelation $learningPathContentObjectRelation
     *
     * @return LearningPathTreeNode
     */
    public function setLearningPathContentObjectRelation($learningPathContentObjectRelation)
    {
        $this->learningPathContentObjectRelation = $learningPathContentObjectRelation;

        return $this;
    }

    /**
     * @return ContentObject
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     * @param ContentObject $contentObject
     *
     * @return LearningPathTreeNode
     */
    public function setContentObject($contentObject)
    {
        $this->contentObject = $contentObject;

        return $this;
    }

    /**
     * @return LearningPathTreeNode
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }

    /**
     * @param LearningPathTreeNode[] $parentNodes
     *
     * @return LearningPathTreeNode
     */
    public function setParentNodes($parentNodes)
    {
        $this->parentNodes = $parentNodes;

        return $this;
    }

    /**
     * @return LearningPathTreeNode[]
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * @param LearningPathTreeNode[] $childNodes
     *
     * @return LearningPathTreeNode
     */
    public function setChildNodes($childNodes)
    {
        $this->childNodes = $childNodes;

        return $this;
    }

    /**
     * @return LearningPathTreeNode[]
     */
    public function getDescendantNodes()
    {
        return $this->descendantNodes;
    }

    /**
     * @param LearningPathTreeNode[] $descendantNodes
     *
     * @return LearningPathTreeNode
     */
    public function setDescendantNodes($descendantNodes)
    {
        $this->descendantNodes = $descendantNodes;

        return $this;
    }

    /**
     * @return LearningPathTreeNode[]
     */
    public function getParentNodes()
    {
        return $this->parentNodes;
    }

    /**
     * Sets the parent node, if addSelfAsChild is true than the system will add this node as a child of the
     * given node
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param bool $addSelfAsChild
     *
     * @return LearningPathTreeNode
     */
    public function setParentNode($learningPathTreeNode, $addSelfAsChild = true)
    {
        if (isset($this->parentNode))
        {
            throw new \InvalidArgumentException(
                'The parent node is already set, please recalculate the entire tree using the tree builder service'
            );
        }

        $this->parentNode = $learningPathTreeNode;

        $selfAndDescendantNodes = $this->getDescendantNodes();
        array_unshift($selfAndDescendantNodes, $this);

        foreach ($selfAndDescendantNodes as $descendantNode)
        {
            $descendantNode->addLearningPathTreeNodeToParents($learningPathTreeNode);

            foreach($learningPathTreeNode->getParentNodes() as $parentNode)
            {
                $descendantNode->addLearningPathTreeNodeToParents($parentNode);
            }
        }

        if($addSelfAsChild)
        {
            $learningPathTreeNode->addChild($this, false);
        }

        return $this;
    }

    /**
     * Adds a LearningPathTreeNode to the list of children and descendants. The system will notify all the parents
     * of the newly added child. If setSelfAsParent is true than the system will set this node as the parent of
     * the selected node
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param bool $setSelfAsParent
     *
     * @return $this
     */
    public function addChild(LearningPathTreeNode $learningPathTreeNode, $setSelfAsParent = true)
    {
        $this->childNodes[] = $learningPathTreeNode;

        $selfAndParentNodes = $this->getParentNodes();
        array_unshift($selfAndParentNodes, $this);

        foreach ($selfAndParentNodes as $parentNode)
        {
            $parentNode->addDescendant($learningPathTreeNode);

            foreach($learningPathTreeNode->getDescendantNodes() as $descendantNode)
            {
                $parentNode->addDescendant($descendantNode);
            }
        }

        if($setSelfAsParent)
        {
            $learningPathTreeNode->setParentNode($this, false);
        }

        return $this;
    }

    /**
     * Adds a LearningPathTreeNode to the list of descendants
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return $this
     */
    public function addDescendant(LearningPathTreeNode $learningPathTreeNode)
    {
        if(in_array($learningPathTreeNode, $this->descendantNodes))
        {
            return $this;
        }

        $this->descendantNodes[] = $learningPathTreeNode;

        return $this;
    }

    /**
     * Adds a
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return $this
     */
    public function addLearningPathTreeNodeToParents(LearningPathTreeNode $learningPathTreeNode)
    {
        if(in_array($learningPathTreeNode, $this->parentNodes))
        {
            return $this;
        }

        $this->parentNodes[] = $learningPathTreeNode;

        return $this;
    }

}