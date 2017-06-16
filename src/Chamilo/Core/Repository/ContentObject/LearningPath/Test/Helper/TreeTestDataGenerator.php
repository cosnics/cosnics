<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Generates a tree with test data
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeTestDataGenerator
{
    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var LearningPath[] | Section[] | Page[] | ContentObject[]
     */
    protected $contentObjects;

    /**
     * @var TreeNodeData[]
     */
    protected $treeNodesData;

    /**
     * @var TreeNode[]
     */
    protected $treeNodes;

    /**
     * Builds a complex learning path for testing purposes
     *
     * - Learning Path A - ID: 1
     *    - Section A - ID: 2
     *        - Page 1 - ID: 6
     *    - Section B - ID: 3
     *    - Section C - ID: 4
     *        - Section D - ID: 5
     *            - Page 2 - ID: 7
     */
    public function __construct()
    {
        $this->tree = new Tree();

        $this->contentObjects[1] = new LearningPath();
        $this->contentObjects[1]->setId(1);

        for ($i = 2; $i < 6; $i ++)
        {
            $this->contentObjects[$i] = new Section();
            $this->contentObjects[$i]->setId($i);
        }

        for ($i = 6; $i < 8; $i ++)
        {
            $this->contentObjects[$i] = new Page();
            $this->contentObjects[$i]->setId($i);
        }

        foreach ($this->contentObjects as $index => $contentObject)
        {
            $this->treeNodesData[$index] = new TreeNodeData();
            $this->treeNodesData[$index]->setId($index);
        }

        $this->treeNodes[1] = new TreeNode($this->tree, $this->contentObjects[1], $this->treeNodesData[1]);
        $this->treeNodes[2] = new TreeNode($this->tree, $this->contentObjects[2], $this->treeNodesData[2]);
        $this->treeNodes[6] = new TreeNode($this->tree, $this->contentObjects[6], $this->treeNodesData[6]);
        $this->treeNodes[3] = new TreeNode($this->tree, $this->contentObjects[3], $this->treeNodesData[3]);
        $this->treeNodes[4] = new TreeNode($this->tree, $this->contentObjects[4], $this->treeNodesData[4]);
        $this->treeNodes[5] = new TreeNode($this->tree, $this->contentObjects[5], $this->treeNodesData[5]);
        $this->treeNodes[7] = new TreeNode($this->tree, $this->contentObjects[7], $this->treeNodesData[7]);

        $this->treeNodes[1]->addChildNode($this->treeNodes[2]);
        $this->treeNodes[2]->addChildNode($this->treeNodes[6]);
        $this->treeNodes[1]->addChildNode($this->treeNodes[3]);
        $this->treeNodes[1]->addChildNode($this->treeNodes[4]);
        $this->treeNodes[4]->addChildNode($this->treeNodes[5]);
        $this->treeNodes[5]->addChildNode($this->treeNodes[7]);
    }

    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @return LearningPath[] | Section[] | Page[] | ContentObject[]
     */
    public function getContentObjects()
    {
        return $this->contentObjects;
    }

    /**
     * @return TreeNodeData[]
     */
    public function getTreeNodesData(): array
    {
        return $this->treeNodesData;
    }

    /**
     * @return TreeNode[]
     */
    public function getTreeNodes(): array
    {
        return $this->treeNodes;
    }
}