<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the TreeNode class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeTest extends Test
{
    public function testStepAutomaticallySetByTree()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree, new LearningPath());

        $this->assertEquals(1, $treeNode->getStep());
    }

    public function testConstructorCallsTree()
    {
        /** @var Tree | \PHPUnit_Framework_MockObject_MockObject $treeMock */
        $treeMock = $this->getMock(Tree::class, array(), array(), '', false);

        $treeMock->expects($this->once())
            ->method('addTreeNode');

        new TreeNode($treeMock, new LearningPath());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetStepCalledTwiceThrowsException()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree, new LearningPath());

        $treeNode->setStep(5);
    }

    public function testGetTree()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree, new LearningPath());

        $this->assertEquals($tree, $treeNode->getTree());
    }

    public function testGetContentObject()
    {
        $contentObject = new LearningPath();

        $tree = new Tree();
        $treeNode = new TreeNode($tree, $contentObject);

        $this->assertEquals($contentObject, $treeNode->getContentObject());
    }

    public function testSetGetTreeNodeData()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree, new LearningPath());

        $treeNodeData = new TreeNodeData();
        $treeNode->setTreeNodeData($treeNodeData);

        $this->assertEquals($treeNodeData, $treeNode->getTreeNodeData());
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
        $treeNodeMock =
            $this->getMock(TreeNode::class, array(), array(), '', false);

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
        $treeNodeMock =
            $this->getMock(TreeNode::class, array(), array(), '', false);

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

    /**
     * Tests a complex learning path structure and checks
     * if all the contents are correctly mapped
     *
     * - Learning Path A
     *      - Learning Path B
     *          - Page 1
     *      - Learning Path C
     *      - Learning Path D
     *          - Learning Path B
     *              - Page 1
     */
    public function testComplexLearningPathStructure()
    {
        $learningPathA = new LearningPath();
        $learningPathB = new LearningPath();
        $learningPathC = new LearningPath();
        $learningPathD = new LearningPath();

        $page1 = new Page();

        $tree = new Tree();

        $learningPathNodeA = new TreeNode($tree, $learningPathA);
        $learningPathNodeB1 = new TreeNode($tree, $learningPathB);

        $learningPathNodePage1A = new TreeNode($tree, $page1);

        $learningPathNodeC = new TreeNode($tree, $learningPathC);
        $learningPathNodeD = new TreeNode($tree, $learningPathD);
        $learningPathNodeB2 = new TreeNode($tree, $learningPathB);

        $learningPathNodePage1B = new TreeNode($tree, $page1);

        $learningPathNodeA->addChildNode($learningPathNodeB1);
        $learningPathNodeB1->addChildNode($learningPathNodePage1A);

        $learningPathNodeA->addChildNode($learningPathNodeC);
        $learningPathNodeA->addChildNode($learningPathNodeD);

        $learningPathNodeD->addChildNode($learningPathNodeB2);
        $learningPathNodeB2->addChildNode($learningPathNodePage1B);

        $this->assertEquals(
            array(1 => $learningPathNodeA, 5 => $learningPathNodeD, 6 => $learningPathNodeB2),
            $learningPathNodePage1B->getParentNodes()
        );

        $this->assertEquals(
            array(6 => $learningPathNodeB2, 7 => $learningPathNodePage1B),
            $learningPathNodeD->getDescendantNodes()
        );
    }
}