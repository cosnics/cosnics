<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the Tree class
 */
class TreeTest extends ChamiloTestCase
{
    public function testSetGetTreeNodes()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree, new LearningPath());

        $tree->setTreeNodes(array($treeNode));
        $this->assertEquals(array($treeNode), $tree->getTreeNodes());
    }

    public function testAddTreeNodeSetsStepNumber()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree, new LearningPath());

        $this->assertEquals(1, $treeNode->getStep());
    }

    public function testAddTreeNodeCallsSetStep()
    {
        $tree = new Tree();

        /** @var TreeNode | \PHPUnit_Framework_MockObject_MockObject $treeNodeMock */
        $treeNodeMock = $this->getMockBuilder(TreeNode::class)->disableOriginalConstructor()->getMock();

        $treeNodeMock->expects($this->once())
            ->method('setStep')
            ->with(1);

        $tree->addTreeNode($treeNodeMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddTreeNodeThrowsExceptionWhenAddedTwice()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree, new LearningPath());

        $tree->addTreeNode($treeNode);
    }

    public function testAddTreeNodeIncrementsStepNumber()
    {
        $tree = new Tree();

        new TreeNode($tree, new LearningPath());
        $treeNode2 = new TreeNode($tree, new LearningPath());

        $this->assertEquals(2, $treeNode2->getStep());
    }

    public function testGetTreeNodeByStep()
    {
        $tree = new Tree();

        $treeNode = new TreeNode($tree, new LearningPath());

        $this->assertEquals($treeNode, $tree->getTreeNodeByStep(1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTreeNodeByStepWithInvalidStep()
    {
        $tree = new Tree();
        $tree->getTreeNodeByStep('test');
    }

    /**
     * @expectedException \RangeException
     */
    public function testGetTreeNodeByStepWithStepBelowOne()
    {
        $tree = new Tree();
        $tree->getTreeNodeByStep(0);
    }

    /**
     * @expectedException \RangeException
     */
    public function testGetTreeNodeByStepWithStepAboveMax()
    {
        $tree = new Tree();
        new TreeNode($tree, new LearningPath());

        $tree->getTreeNodeByStep(2);
    }

    public function testGetRoot()
    {
        $tree = new Tree();
        $node = new TreeNode($tree, new LearningPath());

        $this->assertEquals($node, $tree->getRoot());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetRootThrowsExceptionWithoutNodes()
    {
        $tree = new Tree();
        $tree->getRoot();
    }

    public function testGetTreeNodeById()
    {
        $tree = new Tree();
        $rootNodeData = new TreeNodeData();
        $rootNodeData->setId(1);

        $rootNode = new TreeNode($tree, new LearningPath(), $rootNodeData);

        $this->assertEquals($rootNode, $tree->getTreeNodeById(1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTreeNodeByIdWithInvalidId()
    {
        $tree = new Tree();
        $tree->getTreeNodeById('test');
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    public function testGetTreeNodeByIdWithInexistingId()
    {
        $tree = new Tree();
        $tree->getTreeNodeById(5);
    }

    /**
     * Tests that the root node is returned when you retrieve the node with id 0
     */
    public function testGetTreeNodeByIdWithId0()
    {
        $tree = new Tree();
        $rootNodeData = new TreeNodeData();
        $rootNodeData->setId(1);

        $rootNode = new TreeNode($tree, new LearningPath(), $rootNodeData);

        $this->assertEquals($rootNode, $tree->getTreeNodeById(0));
    }

    public function testGetNodesWithStepSmallerThan()
    {
        $tree = new Tree();
        $rootNodeData = new TreeNodeData();
        $rootNodeData->setId(1);

        $page1TreeNodeData = new TreeNodeData();
        $page1TreeNodeData->setId(2);

        $page2TreeNodeData = new TreeNodeData();
        $page2TreeNodeData->setId(3);

        $page3TreeNodeData = new TreeNodeData();
        $page3TreeNodeData->setId(4);

        $rootNode = new TreeNode($tree, new LearningPath(), $rootNodeData);
        $page1Node = new TreeNode($tree, new Page(), $page1TreeNodeData);
        $page2Node = new TreeNode($tree, new Page(), $page2TreeNodeData);
        $page3Node = new TreeNode($tree, new Page(), $page3TreeNodeData);

        $this->assertEquals(
            array($rootNode, $page1Node, $page2Node), $tree->getNodesWithStepSmallerThan($page3Node->getStep())
        );
    }
}