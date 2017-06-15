<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\Platform\Security;

/**
 * Tests the TreeNode class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeTest extends Test
{
    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var LearningPath | Section | Page | ContentObject
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
     * Setup before each test
     *
     * Builds a complex learning path for each test
     *
     * - Learning Path A - ID: 1
     *    - Section A - ID: 2
     *        - Page 1 - ID: 6
     *    - Section B - ID: 3
     *    - Section C - ID: 4
     *        - Section D - ID: 5
     *            - Page 2 - ID: 7
     */
    public function setUp()
    {
        $this->tree = new Tree();

        $this->contentObjects[1] = new LearningPath();

        for ($i = 2; $i < 6; $i ++)
        {
            $this->contentObjects[$i] = new Section();
        }

        for ($i = 6; $i < 8; $i ++)
        {
            $this->contentObjects[$i] = new Page();
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

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->treeNodes);
        unset($this->tree);
        unset($this->treeNodesData);
        unset($this->contentObjects);
    }

    public function testStepAutomaticallySetByTree()
    {
        $this->assertEquals(5, $this->treeNodes[4]->getStep());
    }

    public function testConstructorCallsTree()
    {
        /** @var Tree | \PHPUnit_Framework_MockObject_MockObject $treeMock */
        $treeMock = $this->getMockBuilder(Tree::class)->disableOriginalConstructor()->getMock();

        $treeMock->expects($this->once())
            ->method('addTreeNode');

        new TreeNode($treeMock, new LearningPath());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetStepCalledTwiceThrowsException()
    {
        $this->treeNodes[1]->setStep(2);
    }

    public function testGetTree()
    {
        $this->assertEquals($this->tree, $this->treeNodes[1]->getTree());
    }

    public function testGetContentObject()
    {
        $this->assertEquals($this->contentObjects[1], $this->treeNodes[1]->getContentObject());
    }

    public function testSetContentObject()
    {
        $contentObject = new LearningPath();
        $this->treeNodes[1]->setContentObject($contentObject);

        $this->assertEquals($contentObject, $this->treeNodes[1]->getContentObject());
    }

    public function testSetGetTreeNodeData()
    {
        $treeNodeData = new TreeNodeData();
        $this->treeNodes[1]->setTreeNodeData($treeNodeData);

        $this->assertEquals($treeNodeData, $this->treeNodes[1]->getTreeNodeData());
    }

    public function testSetParentNode()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeChild->setParentNode($treeNodeParent);

        $this->assertEquals($treeNodeParent, $treeNodeChild->getParentNode());
    }

    public function testSetParentNodeAddsParentToAllParents()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeChild->setParentNode($treeNodeParent);

        $this->assertEquals(array(1 => $treeNodeParent), $treeNodeChild->getParentNodes());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParentNodeThrowsExceptionWhenCalledTwice()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeChild->setParentNode($treeNodeParent);
        $treeNodeChild->setParentNode($treeNodeParent);
    }

    public function testSetParentNodeCallsAddChildNode()
    {
        $tree = new Tree();

        /** @var TreeNode | \PHPUnit_Framework_MockObject_MockObject $treeNodeMock */
        $treeNodeMock = $this->getMockBuilder(TreeNode::class)->disableOriginalConstructor()->getMock();

        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeMock->expects($this->once())
            ->method('addChildNode')
            ->with($treeNodeChild, false);

        $treeNodeChild->setParentNode($treeNodeMock);
    }

    public function testSetParentNodeAddsChildToParent()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeChild->setParentNode($treeNodeParent);

        $this->assertEquals(array(2 => $treeNodeChild), $treeNodeParent->getChildNodes());
    }

    public function testSetParentNodeAddsDescendantToParent()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeChild->setParentNode($treeNodeParent);

        $this->assertEquals(array(2 => $treeNodeChild), $treeNodeParent->getDescendantNodes());
    }

    public function testAddChildNode()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeParent->addChildNode($treeNodeChild);

        $this->assertEquals(array(2 => $treeNodeChild), $treeNodeParent->getChildNodes());
    }

    public function testAddChildNodeAddsChildToDescendants()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeParent->addChildNode($treeNodeChild);

        $this->assertEquals(array(2 => $treeNodeChild), $treeNodeParent->getDescendantNodes());
    }

    public function testAddChildTwiceShouldNotBeAddedTwice()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeParent->addChildNode($treeNodeChild);
        $treeNodeParent->addChildNode($treeNodeChild);

        $this->assertEquals(array(2 => $treeNodeChild), $treeNodeParent->getChildNodes());
    }

    public function testSetParentNodeCallsSetParentNode()
    {
        $tree = new Tree();

        /** @var TreeNode | \PHPUnit_Framework_MockObject_MockObject $treeNodeMock */
        $treeNodeMock = $this->getMockBuilder(TreeNode::class)->disableOriginalConstructor()->getMock();
        $treeNodeParent = new TreeNode($tree, new LearningPath());

        $treeNodeMock->expects($this->once())
            ->method('setParentNode')
            ->with($treeNodeParent, false);

        $treeNodeParent->addChildNode($treeNodeMock);
    }

    public function testAddChildNodeSetsParentNode()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeParent->addChildNode($treeNodeChild);

        $this->assertEquals($treeNodeParent, $treeNodeChild->getParentNode());
    }

    public function testAddChildNodeAddsParentNodeToParentNodes()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeParent->addChildNode($treeNodeChild);

        $this->assertEquals(array(1 => $treeNodeParent), $treeNodeChild->getParentNodes());
    }

    public function testAddDescendantNodeTwice()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeParent->addDescendantNode($treeNodeChild);
        $treeNodeParent->addDescendantNode($treeNodeChild);

        $this->assertEquals(array(2 => $treeNodeChild), $treeNodeParent->getDescendantNodes());
    }

    public function testAddTreeNodeToParentNodesTwice()
    {
        $tree = new Tree();
        $treeNodeParent = new TreeNode($tree, new LearningPath());
        $treeNodeChild = new TreeNode($tree, new LearningPath());

        $treeNodeChild->addTreeNodeToParentNodes($treeNodeParent);
        $treeNodeChild->addTreeNodeToParentNodes($treeNodeParent);

        $this->assertEquals(array(1 => $treeNodeParent), $treeNodeChild->getParentNodes());
    }

    public function testHasParentNode()
    {
        $this->assertTrue($this->treeNodes[6]->hasParentNode());
    }

    public function testGetParentNodes()
    {
        $this->assertEquals(
            array(1 => $this->treeNodes[1], 5 => $this->treeNodes[4], 6 => $this->treeNodes[5]),
            $this->treeNodes[7]->getParentNodes()
        );
    }

    public function testGetDescendantNodes()
    {
        $this->assertEquals(
            array(6 => $this->treeNodes[5], 7 => $this->treeNodes[7]),
            $this->treeNodes[4]->getDescendantNodes()
        );
    }

    public function testHasChildNodes()
    {
        $this->assertTrue($this->treeNodes[4]->hasChildNodes());
    }

    public function testGetNextNode()
    {
        $this->assertEquals($this->treeNodes[6], $this->treeNodes[2]->getNextNode());
    }

    public function testGetNextNodeOnLastNode()
    {
        $this->assertNull($this->treeNodes[7]->getNextNode());
    }

    public function testGetPreviousNode()
    {
        $this->assertEquals($this->treeNodes[4], $this->treeNodes[5]->getPreviousNode());
    }

    public function testGetPreviousNodeOnFirstNode()
    {
        $this->assertNull($this->treeNodes[1]->getPreviousNode());
    }

    public function testGetPreviousNodes()
    {
        $this->assertEquals(
            array($this->treeNodes[1], $this->treeNodes[2], $this->treeNodes[6]),
            $this->treeNodes[3]->getPreviousNodes()
        );
    }

    public function testisRootNode()
    {
        $this->assertTrue($this->treeNodes[1]->isRootNode());
    }

    public function testisRootNodeForNoRootNode()
    {
        $this->assertFalse($this->treeNodes[6]->isRootNode());
    }

    public function testIsChildOf()
    {
        $this->assertTrue($this->treeNodes[7]->isChildOf($this->treeNodes[5]));
    }

    public function testIsChildOfReturnsFalse()
    {
        $this->assertFalse($this->treeNodes[6]->isChildOf($this->treeNodes[5]));
    }

    public function testGetTreeNodeDataIdsFromSelfAndDescendants()
    {
        $this->assertEquals(array(4, 5, 7), $this->treeNodes[4]->getTreeNodeDataIdsFromSelfAndDescendants());
    }

}