<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the LearningPathTreeNode class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTreeNodeTest extends Test
{
    public function testStepAutomaticallySetByLearningPathTree()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $this->assertEquals(1, $learningPathTreeNode->getStep());
    }

    public function testConstructorCallsLearningPathTree()
    {
        /** @var LearningPathTree | \PHPUnit_Framework_MockObject_MockObject $learningPathTreeMock */
        $learningPathTreeMock = $this->getMock(LearningPathTree::class, array(), array(), '', false);

        $learningPathTreeMock->expects($this->once())
            ->method('addLearningPathTreeNode');

        new LearningPathTreeNode($learningPathTreeMock, new LearningPath());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetStepCalledTwiceThrowsException()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNode->setStep(5);
    }

    public function testGetLearningPathTree()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $this->assertEquals($learningPathTree, $learningPathTreeNode->getLearningPathTree());
    }

    public function testGetContentObject()
    {
        $contentObject = new LearningPath();

        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, $contentObject);

        $this->assertEquals($contentObject, $learningPathTreeNode->getContentObject());
    }

    public function testSetGetLearningPathChild()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathChild = new LearningPathChild();
        $learningPathTreeNode->setLearningPathChild($learningPathChild);

        $this->assertEquals($learningPathChild, $learningPathTreeNode->getLearningPathChild());
    }

    public function testSetParentNode()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeChild->setParentNode($learningPathTreeNodeParent);

        $this->assertEquals($learningPathTreeNodeParent, $learningPathTreeNodeChild->getParentNode());
    }

    public function testSetParentNodeAddsParentToAllParents()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeChild->setParentNode($learningPathTreeNodeParent);

        $this->assertEquals(array(1 => $learningPathTreeNodeParent), $learningPathTreeNodeChild->getParentNodes());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParentNodeThrowsExceptionWhenCalledTwice()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeChild->setParentNode($learningPathTreeNodeParent);
        $learningPathTreeNodeChild->setParentNode($learningPathTreeNodeParent);
    }

    public function testSetParentNodeCallsAddChildNode()
    {
        $learningPathTree = new LearningPathTree();

        /** @var LearningPathTreeNode | \PHPUnit_Framework_MockObject_MockObject $learningPathTreeNodeMock */
        $learningPathTreeNodeMock =
            $this->getMock(LearningPathTreeNode::class, array(), array(), '', false);

        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeMock->expects($this->once())
            ->method('addChildNode')
            ->with($learningPathTreeNodeChild, false);

        $learningPathTreeNodeChild->setParentNode($learningPathTreeNodeMock);
    }

    public function testSetParentNodeAddsChildToParent()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeChild->setParentNode($learningPathTreeNodeParent);

        $this->assertEquals(array(2 => $learningPathTreeNodeChild), $learningPathTreeNodeParent->getChildNodes());
    }

    public function testSetParentNodeAddsDescendantToParent()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeChild->setParentNode($learningPathTreeNodeParent);

        $this->assertEquals(array(2 => $learningPathTreeNodeChild), $learningPathTreeNodeParent->getDescendantNodes());
    }

    public function testAddChildNode()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeParent->addChildNode($learningPathTreeNodeChild);

        $this->assertEquals(array(2 => $learningPathTreeNodeChild), $learningPathTreeNodeParent->getChildNodes());
    }

    public function testAddChildNodeAddsChildToDescendants()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeParent->addChildNode($learningPathTreeNodeChild);

        $this->assertEquals(array(2 => $learningPathTreeNodeChild), $learningPathTreeNodeParent->getDescendantNodes());
    }

    public function testAddChildTwiceShouldNotBeAddedTwice()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeParent->addChildNode($learningPathTreeNodeChild);
        $learningPathTreeNodeParent->addChildNode($learningPathTreeNodeChild);

        $this->assertEquals(array(2 => $learningPathTreeNodeChild), $learningPathTreeNodeParent->getChildNodes());
    }

    public function testSetParentNodeCallsSetParentNode()
    {
        $learningPathTree = new LearningPathTree();

        /** @var LearningPathTreeNode | \PHPUnit_Framework_MockObject_MockObject $learningPathTreeNodeMock */
        $learningPathTreeNodeMock =
            $this->getMock(LearningPathTreeNode::class, array(), array(), '', false);

        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeMock->expects($this->once())
            ->method('setParentNode')
            ->with($learningPathTreeNodeParent, false);

        $learningPathTreeNodeParent->addChildNode($learningPathTreeNodeMock);
    }

    public function testAddChildNodeSetsParentNode()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeParent->addChildNode($learningPathTreeNodeChild);

        $this->assertEquals($learningPathTreeNodeParent, $learningPathTreeNodeChild->getParentNode());
    }

    public function testAddChildNodeAddsParentNodeToParentNodes()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeParent->addChildNode($learningPathTreeNodeChild);

        $this->assertEquals(array(1 => $learningPathTreeNodeParent), $learningPathTreeNodeChild->getParentNodes());
    }

    public function testAddDescendantNodeTwice()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeParent->addDescendantNode($learningPathTreeNodeChild);
        $learningPathTreeNodeParent->addDescendantNode($learningPathTreeNodeChild);

        $this->assertEquals(array(2 => $learningPathTreeNodeChild), $learningPathTreeNodeParent->getDescendantNodes());
    }

    public function testAddLearningPathTreeNodeToParentNodesTwice()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNodeParent = new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNodeChild = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTreeNodeChild->addLearningPathTreeNodeToParentNodes($learningPathTreeNodeParent);
        $learningPathTreeNodeChild->addLearningPathTreeNodeToParentNodes($learningPathTreeNodeParent);

        $this->assertEquals(array(1 => $learningPathTreeNodeParent), $learningPathTreeNodeChild->getParentNodes());
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

        $learningPathTree = new LearningPathTree();

        $learningPathNodeA = new LearningPathTreeNode($learningPathTree, $learningPathA);
        $learningPathNodeB1 = new LearningPathTreeNode($learningPathTree, $learningPathB);

        $learningPathNodePage1A = new LearningPathTreeNode($learningPathTree, $page1);

        $learningPathNodeC = new LearningPathTreeNode($learningPathTree, $learningPathC);
        $learningPathNodeD = new LearningPathTreeNode($learningPathTree, $learningPathD);
        $learningPathNodeB2 = new LearningPathTreeNode($learningPathTree, $learningPathB);

        $learningPathNodePage1B = new LearningPathTreeNode($learningPathTree, $page1);

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