<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
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
     * The LearningPathChild data
     *
     * @var LearningPathChild
     */
    protected $learningPathChild;

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
        $this->learningPathTree = $learningPathTree;
        $this->contentObject = $contentObject;

        $this->parentNodes = array();
        $this->childNodes = array();
        $this->descendantNodes = array();

        $learningPathTree->addLearningPathTreeNode($this);
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
        if (isset($this->step))
        {
            throw new \RuntimeException(
                'The given step is already set and synced with the learning path tree and should not be changed'
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
     * @return ContentObject
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     * @return LearningPathChild
     */
    public function getLearningPathChild()
    {
        return $this->learningPathChild;
    }

    /**
     * @param LearningPathChild $learningPathChild
     *
     * @return LearningPathTreeNode
     */
    public function setLearningPathChild($learningPathChild)
    {
        $this->learningPathChild = $learningPathChild;

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
     * @return bool
     */
    public function hasParentNode()
    {
        return $this->parentNode instanceof LearningPathTreeNode;
    }

    /**
     * @return LearningPathTreeNode[]
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * @return bool
     */
    public function hasChildNodes()
    {
        return count($this->childNodes) > 0;
    }

    /**
     * @return LearningPathTreeNode[]
     */
    public function getDescendantNodes()
    {
        return $this->descendantNodes;
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
            $descendantNode->addLearningPathTreeNodeToParentNodes($learningPathTreeNode);

            foreach ($learningPathTreeNode->getParentNodes() as $parentNode)
            {
                $descendantNode->addLearningPathTreeNodeToParentNodes($parentNode);
            }
        }

        if ($addSelfAsChild)
        {
            $learningPathTreeNode->addChildNode($this, false);
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
    public function addChildNode(LearningPathTreeNode $learningPathTreeNode, $setSelfAsParent = true)
    {
        if (array_key_exists($learningPathTreeNode->getStep(), $this->childNodes))
        {
            return $this;
        }

        $this->childNodes[$learningPathTreeNode->getStep()] = $learningPathTreeNode;

        $selfAndParentNodes = $this->getParentNodes();
        array_unshift($selfAndParentNodes, $this);

        foreach ($selfAndParentNodes as $parentNode)
        {
            $parentNode->addDescendantNode($learningPathTreeNode);

            foreach ($learningPathTreeNode->getDescendantNodes() as $descendantNode)
            {
                $parentNode->addDescendantNode($descendantNode);
            }
        }

        if ($setSelfAsParent)
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
    public function addDescendantNode(LearningPathTreeNode $learningPathTreeNode)
    {
        if (array_key_exists($learningPathTreeNode->getStep(), $this->descendantNodes))
        {
            return $this;
        }

        $this->descendantNodes[$learningPathTreeNode->getStep()] = $learningPathTreeNode;

        return $this;
    }

    /**
     * Adds a LearningPathTreeNode to the list of parent nodes
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return $this
     */
    public function addLearningPathTreeNodeToParentNodes(LearningPathTreeNode $learningPathTreeNode)
    {
        if (array_key_exists($learningPathTreeNode->getStep(), $this->parentNodes))
        {
            return $this;
        }

        $this->parentNodes[$learningPathTreeNode->getStep()] = $learningPathTreeNode;

        return $this;
    }

    /**
     * Returns the next learning path tree node (if available)
     *
     * @return LearningPathTreeNode
     */
    public function getNextNode()
    {
        try
        {
            return $this->getLearningPathTree()->getLearningPathTreeNodeByStep($this->getStep() + 1);
        }
        catch(\Exception $ex)
        {
            return null;
        }
    }

    /**
     * Returns the previous learning path tree node (if available)
     *
     * @return LearningPathTreeNode
     */
    public function getPreviousNode()
    {
        try
        {
            return $this->getLearningPathTree()->getLearningPathTreeNodeByStep($this->getStep() - 1);
        }
        catch(\Exception $ex)
        {
            return null;
        }
    }

    /**
     * Returns whether or not the current node is a root node
     *
     * @return bool
     */
    public function isRootNode()
    {
        return $this->getLearningPathTree()->getRoot() == $this;
    }

    /**
     * Returns the path to this node by a set of content object ids for the current node and his parents
     *
     * @return int[]
     */
    public function getPathAsContentObjectIds()
    {
        $contentObjectIds = array();

        $currentNode = $this;

        while($currentNode->hasParentNode())
        {
            array_unshift($contentObjectIds, $currentNode->getContentObject()->getId());
            $currentNode = $currentNode->getParentNode();
        }

        array_unshift($contentObjectIds, $currentNode->getContentObject()->getId());

        return $contentObjectIds;
    }

}