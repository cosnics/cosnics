<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildValidator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the LearningPathChildValidator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChildValidatorTest extends Test
{
    /**
     * @var LearningPathTreeBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $learningPathTreeBuilderMock;

    /**
     * The Subject Under Test
     *
     * @var LearningPathChildValidator
     */
    protected $learningPathChildValidator;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $this->learningPathTreeBuilderMock = $this->getMockBuilder(LearningPathTreeBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->learningPathChildValidator = new LearningPathChildValidator($this->learningPathTreeBuilderMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->learningPathTreeBuilderMock);
        unset($this->learningPathChildValidator);
    }

    public function testCanContentObjectBeAdded()
    {
        $learningPathA = new LearningPath();
        $learningPathA->setId(5);

        $learningPathB = new LearningPath();
        $learningPathB->setId(15);

        $learningPathTreeA = new LearningPathTree();
        $learningPathTreeNodeA = new LearningPathTreeNode($learningPathTreeA, $learningPathA);

        $learningPathTreeB = new LearningPathTree();
        new LearningPathTreeNode($learningPathTreeB, $learningPathB);

        $this->mockBuildLearningPathTree($learningPathB, $learningPathTreeB);

        $this->assertTrue(
            $this->learningPathChildValidator->canContentObjectBeAdded($learningPathTreeNodeA, $learningPathB)
        );
    }

    /**
     * Tests that a content object which is not of the type LearningPath
     * can always be added to the parent learning path
     */
    public function testCanContentObjectBeAddedWithNoLearningPath()
    {
        $learningPathA = new LearningPath();
        $learningPathA->setId(5);

        $pageB = new Page();
        $pageB->setId(15);

        $learningPathTreeA = new LearningPathTree();
        $learningPathTreeNodeA = new LearningPathTreeNode($learningPathTreeA, $learningPathA);

        $this->assertTrue(
            $this->learningPathChildValidator->canContentObjectBeAdded($learningPathTreeNodeA, $pageB)
        );
    }

    /**
     * Tests that a sublearningpath can be added when it is already added in the parents
     *
     * Learning Path A
     *      - Learning Path B
     *
     * Learning Path C
     *      - Learning Path A
     *
     * Try to add C to B, should not be allowed
     */
    public function testCanContentObjectBeAddedWhenAlreadyAdded()
    {
        $learningPathA = new LearningPath();
        $learningPathA->setId(uniqid());

        $learningPathB = new LearningPath();
        $learningPathB->setId(uniqid());

        $learningPathC = new LearningPath();
        $learningPathC->setId(uniqid());

        $learningPathTreeA = new LearningPathTree();
        $learningPathTreeNodeA = new LearningPathTreeNode($learningPathTreeA, $learningPathA);
        $learningPathTreeNodeB = new LearningPathTreeNode($learningPathTreeA, $learningPathB);
        $learningPathTreeNodeA->addChildNode($learningPathTreeNodeB);

        $learningPathTreeC = new LearningPathTree();
        $learningPathTreeNodeC = new LearningPathTreeNode($learningPathTreeC, $learningPathC);
        $learningPathTreeNodeA2 = new LearningPathTreeNode($learningPathTreeC, $learningPathA);
        $learningPathTreeNodeC->addChildNode($learningPathTreeNodeA2);

        $this->mockBuildLearningPathTree($learningPathC, $learningPathTreeC);

        $this->assertFalse(
            $this->learningPathChildValidator->canContentObjectBeAdded($learningPathTreeNodeB, $learningPathC)
        );
    }

    public function testCanContentObjectBeAddedWhenParentIsNoLearningPath()
    {
        $learningPathA = new LearningPath();
        $learningPathA->setId(uniqid());

        $pageB = new Page();
        $pageB->setId(15);

        $learningPathC = new LearningPath();
        $learningPathC->setId(uniqid());

        $learningPathTreeA = new LearningPathTree();
        $learningPathTreeNodeA = new LearningPathTreeNode($learningPathTreeA, $learningPathA);
        $pageLearningPathTreeNode = new LearningPathTreeNode($learningPathTreeA, $pageB);
        $learningPathTreeNodeA->addChildNode($pageLearningPathTreeNode);

        $this->assertFalse(
            $this->learningPathChildValidator->canContentObjectBeAdded($pageLearningPathTreeNode, $learningPathC)
        );
    }

    public function testGetContentObjectIdsThatCanNotBeAddedTo()
    {
        $learningPathA = new LearningPath();
        $learningPathA->setId(uniqid());

        $learningPathB = new LearningPath();
        $learningPathB->setId(uniqid());

        $learningPathC = new LearningPath();
        $learningPathC->setId(uniqid());

        $learningPathTreeA = new LearningPathTree();

        $learningPathTreeNodeA = new LearningPathTreeNode($learningPathTreeA, $learningPathA);
        $learningPathTreeNodeB = new LearningPathTreeNode($learningPathTreeA, $learningPathB);
        $learningPathTreeNodeC = new LearningPathTreeNode($learningPathTreeA, $learningPathC);

        $learningPathTreeNodeA->addChildNode($learningPathTreeNodeB);
        $learningPathTreeNodeB->addChildNode($learningPathTreeNodeC);

        $this->assertEquals(
            array(
                $learningPathA->getId(), $learningPathB->getId(), $learningPathC->getId()
            ),
            $this->learningPathChildValidator->getContentObjectIdsThatCanNotBeAddedTo(
                $learningPathTreeNodeC
            )
        );
    }

    /**
     * Tests that the system returns the content object ids that can not be added to a learning path also blocks
     * the direct children, even if they are not a sub learning path
     */
    public function testGetContentObjectsIdsThatCanNotBeAddedToForDirectChildren()
    {
        $learningPathA = new LearningPath();
        $learningPathA->setId(5);

        $pageB = new Page();
        $pageB->setId(15);

        $learningPathTreeA = new LearningPathTree();
        $learningPathTreeNodeA = new LearningPathTreeNode($learningPathTreeA, $learningPathA);
        $pageLearningPathTreeNode = new LearningPathTreeNode($learningPathTreeA, $pageB);

        $learningPathTreeNodeA->addChildNode($pageLearningPathTreeNode);

        $this->assertEquals(
            array(
                $learningPathA->getId(), $pageB->getId()
            ),
            $this->learningPathChildValidator->getContentObjectIdsThatCanNotBeAddedTo(
                $learningPathTreeNodeA
            )
        );
    }

    /**
     * Helper function to mock the buildLearningPathTree function of the LearningPathTreeBuilder service
     *
     * @param ContentObject $contentObject
     * @param LearningPathTree $returnLearningPathTree
     */
    protected function mockBuildLearningPathTree(ContentObject $contentObject, LearningPathTree $returnLearningPathTree)
    {
        $this->learningPathTreeBuilderMock->expects($this->once())
            ->method('buildLearningPathTree')
            ->with($contentObject)
            ->will($this->returnValue($returnLearningPathTree));
    }
}