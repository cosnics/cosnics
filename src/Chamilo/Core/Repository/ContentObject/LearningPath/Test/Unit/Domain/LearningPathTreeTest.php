<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the Tree class
 */
class TreeTest extends Test
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
        $treeNodeMock =
            $this->getMock(TreeNode::class, array(), array(), '', false);

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

        $this->assertEquals($treeNode, $tree->getTreeNodeById(1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTreeNodeByStepWithInvalidStep()
    {
        $tree = new Tree();
        $tree->getTreeNodeById('test');
    }

    /**
     * @expectedException \RangeException
     */
    public function testGetTreeNodeByStepWithStepBelowOne()
    {
        $tree = new Tree();
        $tree->getTreeNodeById(0);
    }

    /**
     * @expectedException \RangeException
     */
    public function testGetTreeNodeByStepWithStepAboveMax()
    {
        $tree = new Tree();
        new TreeNode($tree, new LearningPath());

        $tree->getTreeNodeById(2);
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
}