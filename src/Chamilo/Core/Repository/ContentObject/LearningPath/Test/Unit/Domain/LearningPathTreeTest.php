<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the LearningPathTree class
 */
class LearningPathTreeTest extends Test
{
    public function testSetGetLearningPathTreeNodes()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTree->setLearningPathTreeNodes(array($learningPathTreeNode));
        $this->assertEquals(array($learningPathTreeNode), $learningPathTree->getLearningPathTreeNodes());
    }

    public function testAddLearningPathTreeNodeSetsStepNumber()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $this->assertEquals(1, $learningPathTreeNode->getStep());
    }

    public function testAddLearningPathTreeNodeCallsSetStep()
    {
        $learningPathTree = new LearningPathTree();

        /** @var LearningPathTreeNode | \PHPUnit_Framework_MockObject_MockObject $learningPathTreeNodeMock */
        $learningPathTreeNodeMock =
            $this->getMock(LearningPathTreeNode::class, array(), array(), '', false);

        $learningPathTreeNodeMock->expects($this->once())
            ->method('setStep')
            ->with(1);

        $learningPathTree->addLearningPathTreeNode($learningPathTreeNodeMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddLearningPathTreeNodeThrowsExceptionWhenAddedTwice()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTree->addLearningPathTreeNode($learningPathTreeNode);
    }

    public function testAddLearningPathTreeNodeIncrementsStepNumber()
    {
        $learningPathTree = new LearningPathTree();

        new LearningPathTreeNode($learningPathTree, new LearningPath());
        $learningPathTreeNode2 = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $this->assertEquals(2, $learningPathTreeNode2->getStep());
    }

    public function testGetLearningPathTreeNodeByStep()
    {
        $learningPathTree = new LearningPathTree();

        $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, new LearningPath());

        $this->assertEquals($learningPathTreeNode, $learningPathTree->getLearningPathTreeNodeByStep(1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetLearningPathTreeNodeByStepWithInvalidStep()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTree->getLearningPathTreeNodeByStep('test');
    }

    /**
     * @expectedException \RangeException
     */
    public function testGetLearningPathTreeNodeByStepWithStepBelowOne()
    {
        $learningPathTree = new LearningPathTree();
        $learningPathTree->getLearningPathTreeNodeByStep(0);
    }

    /**
     * @expectedException \RangeException
     */
    public function testGetLearningPathTreeNodeByStepWithStepAboveMax()
    {
        $learningPathTree = new LearningPathTree();
        new LearningPathTreeNode($learningPathTree, new LearningPath());

        $learningPathTree->getLearningPathTreeNodeByStep(2);
    }
}