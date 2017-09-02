<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * A single node for a tree. Used in an in-memory tree
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNode
{
    /**
     * The step for this node
     *
     * @var int
     */
    protected $step;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * The TreeNodeData data
     *
     * @var TreeNodeData
     */
    protected $treeNodeData;

    /**
     * @var ContentObject
     */
    protected $contentObject;

    /**
     * The direct parent of this node
     *
     * @var TreeNode
     */
    protected $parentNode;

    /**
     * Every parent of this node
     *
     * @var TreeNode[]
     */
    protected $parentNodes;

    /**
     * The direct children of this node
     *
     * @var TreeNode[]
     */
    protected $childNodes;

    /**
     * Every child of this node (even those of subnodes)
     *
     * @var TreeNode[]
     */
    protected $descendantNodes;

    /**
     * TreeNode constructor.
     *
     * @param Tree $tree
     * @param ContentObject $contentObject
     * @param TreeNodeData $treeNodeData
     */
    public function __construct(
        Tree $tree, ContentObject $contentObject = null,
            TreeNodeData $treeNodeData = null
    )
    {
        $this->tree = $tree;
        $this->contentObject = $contentObject;
        $this->treeNodeData = $treeNodeData;

        $this->parentNodes = array();
        $this->childNodes = array();
        $this->descendantNodes = array();

        $tree->addTreeNode($this);
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Returns the unique identifier for this node
     *
     * @return int
     */
    public function getId()
    {
        if ($this->treeNodeData instanceof TreeNodeData)
        {
            return $this->treeNodeData->getId();
        }

        return 0;
    }

    /**
     * @param int $step
     *
     * @return TreeNode
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
     * @return Tree
     */
    public function getTree()
    {
        return $this->tree;
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
     * @return TreeNode
     */
    public function setContentObject(ContentObject $contentObject)
    {
        $this->contentObject = $contentObject;
        
        return $this;
    }

    /**
     * @return TreeNodeData
     */
    public function getTreeNodeData()
    {
        return $this->treeNodeData;
    }

    /**
     * @param TreeNodeData $treeNodeData
     *
     * @return TreeNode
     */
    public function setTreeNodeData($treeNodeData)
    {
        $this->treeNodeData = $treeNodeData;

        return $this;
    }

    /**
     * @return TreeNode
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
        return $this->parentNode instanceof TreeNode;
    }

    /**
     * @return TreeNode[]
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
     * @return TreeNode[]
     */
    public function getDescendantNodes()
    {
        return $this->descendantNodes;
    }

    /**
     * @return TreeNode[]
     */
    public function getParentNodes()
    {
        return $this->parentNodes;
    }

    /**
     * Sets the parent node, if addSelfAsChild is true than the system will add this node as a child of the
     * given node
     *
     * @param TreeNode $treeNode
     * @param bool $addSelfAsChild
     *
     * @return TreeNode
     */
    public function setParentNode($treeNode, $addSelfAsChild = true)
    {
        if (isset($this->parentNode))
        {
            throw new \InvalidArgumentException(
                'The parent node is already set, please recalculate the entire tree using the tree builder service'
            );
        }

        $this->parentNode = $treeNode;

        $selfAndDescendantNodes = $this->getDescendantNodes();
        array_unshift($selfAndDescendantNodes, $this);

        foreach ($selfAndDescendantNodes as $descendantNode)
        {
            $parentNodes = $treeNode->getParentNodes();

            foreach ($parentNodes as $parentNode)
            {
                $descendantNode->addTreeNodeToParentNodes($parentNode);
            }

            $descendantNode->addTreeNodeToParentNodes($treeNode);
        }

        if ($addSelfAsChild)
        {
            $treeNode->addChildNode($this, false);
        }

        return $this;
    }

    /**
     * Adds a TreeNode to the list of children and descendants. The system will notify all the parents
     * of the newly added child. If setSelfAsParent is true than the system will set this node as the parent of
     * the selected node
     *
     * @param TreeNode $treeNode
     * @param bool $setSelfAsParent
     *
     * @return $this
     */
    public function addChildNode(TreeNode $treeNode, $setSelfAsParent = true)
    {
        if (array_key_exists($treeNode->getStep(), $this->childNodes))
        {
            return $this;
        }

        $this->childNodes[$treeNode->getStep()] = $treeNode;

        $selfAndParentNodes = $this->getParentNodes();
        array_unshift($selfAndParentNodes, $this);

        foreach ($selfAndParentNodes as $parentNode)
        {
            $parentNode->addDescendantNode($treeNode);

            foreach ($treeNode->getDescendantNodes() as $descendantNode)
            {
                $parentNode->addDescendantNode($descendantNode);
            }
        }

        if ($setSelfAsParent)
        {
            $treeNode->setParentNode($this, false);
        }

        return $this;
    }

    /**
     * Adds a TreeNode to the list of descendants
     *
     * @param TreeNode $treeNode
     *
     * @return $this
     */
    public function addDescendantNode(TreeNode $treeNode)
    {
        if (array_key_exists($treeNode->getStep(), $this->descendantNodes))
        {
            return $this;
        }

        $this->descendantNodes[$treeNode->getStep()] = $treeNode;

        return $this;
    }

    /**
     * Adds a TreeNode to the list of parent nodes
     *
     * @param TreeNode $treeNode
     *
     * @return $this
     */
    public function addTreeNodeToParentNodes(TreeNode $treeNode)
    {
        if (array_key_exists($treeNode->getStep(), $this->parentNodes))
        {
            return $this;
        }

        $this->parentNodes[$treeNode->getStep()] = $treeNode;

        return $this;
    }

    /**
     * Returns the next learning path tree node (if available)
     *
     * @return TreeNode
     */
    public function getNextNode()
    {
        try
        {
            return $this->getTree()->getTreeNodeByStep($this->getStep() + 1);
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }

    /**
     * Returns the previous learning path tree node (if available)
     *
     * @return TreeNode
     */
    public function getPreviousNode()
    {
        try
        {
            return $this->getTree()->getTreeNodeByStep($this->getStep() - 1);
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }

    /**
     * Returns the previous nodes for the current learning path tree node
     *
     * @return TreeNode[]
     */
    public function getPreviousNodes()
    {
        return $this->getTree()->getNodesWithStepSmallerThan($this->getStep());
    }

    /**
     * Returns whether or not the current node is a root node
     *
     * @return bool
     */
    public function isRootNode()
    {
        return $this->getTree()->getRoot() === $this;
    }

    /**
     * Returns the identifiers for the TreeNodeData objects for this object and all of it's descendants
     *
     * @return int[]
     */
    public function getTreeNodeDataIdsFromSelfAndDescendants()
    {
        $treeNodeDataIds = array();

        $treeNodeDataIds[] = $this->getId();

        foreach($this->getDescendantNodes() as $descendantNode)
        {
            $treeNodeDataIds[] = $descendantNode->getId();
        }

        return $treeNodeDataIds;
    }

    /**
     * Returns whether or not the current node (self) is a child of the given node (possibleParentNode)
     *
     * @param TreeNode $possibleParentNode
     *
     * @return bool
     */
    public function isChildOf(TreeNode $possibleParentNode)
    {
        return in_array($possibleParentNode, $this->getParentNodes());
    }

    /**
     * Returns whether or not the current node (self) is a follow-up node of the giving node.
     *
     * @param TreeNode $possiblePreviousNode
     *
     * @return bool
     */
    public function comesAfterNode(TreeNode $possiblePreviousNode)
    {
        return $this->getStep() > $possiblePreviousNode->getStep();
    }

    /**
     * Returns whether or not this node is used in a default traversing order of any of his parents
     *
     * @return bool
     */
    public function isInDefaultTraversingOrder()
    {
        return $this->getFirstParentThatEnforcesDefaultTraversingOrder() instanceof TreeNode;
    }

    /**
     * Returns the first parent that enforces the default traversing order
     *
     * @return TreeNode|null
     */
    public function getFirstParentThatEnforcesDefaultTraversingOrder()
    {
        /** @var LearningPath $learningPath */
        $learningPath = $this->getTree()->getRoot()->getContentObject();
        if ($learningPath->enforcesDefaultTraversingOrder() && !$this->isRootNode())
        {
            return $this->getTree()->getRoot();
        }

        $parents = $this->getParentNodes();
        foreach ($parents as $parentNode)
        {
            if ($parentNode->getTreeNodeData()->enforcesDefaultTraversingOrder())
            {
                return $parentNode;
            }
        }

        return null;
    }
}