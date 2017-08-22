<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\Tracking;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AssessmentTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\TreeTestDataGenerator;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the AssessmentTrackingService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentTrackingServiceTest extends ChamiloTestCase
{
    /**
     * @var AssessmentTrackingService
     */
    protected $assessmentTrackingService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptService
     */
    protected $attemptServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptTrackingService
     */
    protected $attemptTrackingServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | TrackingRepositoryInterface
     */
    protected $trackingRepositoryMock;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var LearningPath
     */
    protected $learningPath;

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
     * @var User
     */
    protected $user;

    /**
     * @var TreeNodeAttempt[][]
     */
    protected $treeNodeAttempts;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->attemptServiceMock = $this->getMockBuilder(AttemptService::class)
            ->disableOriginalConstructor()->getMock();

        $this->attemptTrackingServiceMock = $this->getMockBuilder(AttemptTrackingService::class)
            ->disableOriginalConstructor()->getMock();

        $this->trackingRepositoryMock = $this->getMockBuilder(TrackingRepositoryInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->assessmentTrackingService = new AssessmentTrackingService(
            $this->attemptServiceMock, $this->attemptTrackingServiceMock, $this->trackingRepositoryMock
        );

        $this->user = new User();
        $this->user->setId(2);

        $treeTestDataGenerator = new TreeTestDataGenerator();

        $this->tree = $treeTestDataGenerator->getTree();
        $this->contentObjects = $treeTestDataGenerator->getContentObjects();
        $this->treeNodesData = $treeTestDataGenerator->getTreeNodesData();
        $this->treeNodes = $treeTestDataGenerator->getTreeNodes();
        $this->learningPath = $this->tree->getRoot()->getContentObject();

        $assessment = new Assessment();
        $assessment->setId(14);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(12)
            ->setMasteryScore(80)
            ->setContentObjectId(14)
            ->setLearningPathId($this->learningPath->getId());

        $treeNode = new TreeNode($this->tree, $assessment, $treeNodeData);
        $this->treeNodes[12] = $treeNode;
        $this->treeNodes[2]->addChildNode($treeNode);

        $treeNodeAttempt6 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt6->setId(1)
            ->setTreeNodeDataId(6)
            ->setCompleted(false)
            ->set_total_time(20);

        $treeNodeAttempt4 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt4->setId(2)
            ->setTreeNodeDataId(4)
            ->set_total_time(38)
            ->setCompleted(true);

        $treeNodeAttempt5 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt5->setId(3)
            ->setTreeNodeDataId(5)
            ->set_total_time(42)
            ->setCompleted(true);

        $treeNodeAttempt7 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt7->setId(4)
            ->setTreeNodeDataId(7)
            ->set_total_time(16)
            ->setCompleted(true);

        $treeNodeAttempt12 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt12->setId(5)
            ->setTreeNodeDataId(12)
            ->set_score(20)
            ->set_total_time(123)
            ->setCompleted(true);

        $treeNodeAttempt12b = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt12b->setId(6)
            ->setTreeNodeDataId(12)
            ->set_score(80)
            ->set_total_time(150)
            ->setCompleted(true);

        $this->treeNodeAttempts = [
            6 => [$treeNodeAttempt6], 4 => [$treeNodeAttempt4], 5 => [$treeNodeAttempt5],
            7 => [$treeNodeAttempt7], 12 => [$treeNodeAttempt12, $treeNodeAttempt12b]
        ];
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->assessmentTrackingService);
        unset($this->attemptTrackingServiceMock);
        unset($this->trackingRepositoryMock);
        unset($this->attemptServiceMock);
        unset($this->user);
        unset($this->treeNodes);
        unset($this->treeNodesData);
        unset($this->contentObjects);
        unset($this->tree);
        unset($this->treeNodeAttempts);
        unset($this->learningPath);
    }

    public function testIsMaximumAttemptsReachedForAssessment()
    {
        $assessmentTreeNode = $this->treeNodes[12];

        /** @var Assessment $assessment */
        $assessment = $assessmentTreeNode->getContentObject();
        $assessment->set_maximum_attempts(1);

        $this->mockGetTreeNodeAttemptsForTreeNodes([$assessmentTreeNode]);

        $this->assertTrue(
            $this->assessmentTrackingService->isMaximumAttemptsReachedForAssessment(
                $this->learningPath, $this->user, $assessmentTreeNode
            )
        );
    }

    public function testIsMaximumAttemptsReachedForAssessmentReturnsFalse()
    {
        $assessmentTreeNode = $this->treeNodes[12];

        $this->mockGetTreeNodeAttemptsForTreeNodes([$assessmentTreeNode]);

        $this->assertFalse(
            $this->assessmentTrackingService->isMaximumAttemptsReachedForAssessment(
                $this->learningPath, $this->user, $assessmentTreeNode
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsMaximumAttemptsReachedForAssessmentNoAssessmentNode()
    {
        $this->assessmentTrackingService->isMaximumAttemptsReachedForAssessment(
            $this->learningPath, $this->user, $this->treeNodes[1]
        );
    }

    public function testSaveAnswerForQuestion()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt->set_question_complex_id(3);

        $this->mockGetOrCreateActiveTreeNodeAttempt();
        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt]);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('update')
            ->with($questionAttempt);

        $this->assessmentTrackingService->saveAnswerForQuestion(
            $this->learningPath, $this->user, $this->treeNodes[12], 3, 'Answer Test', 2, 'Hint'
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSaveAnswerForQuestionWithoutAttempt()
    {
        $this->mockGetOrCreateActiveTreeNodeAttempt();
        $this->mockGetTreeNodeQuestionAttempts();

        $this->assessmentTrackingService->saveAnswerForQuestion(
            $this->learningPath, $this->user, $this->treeNodes[12], 3, 'Answer Test', 2, 'Hint'
        );
    }

    public function testSaveAnswerForQuestionSetsAnswer()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt->set_question_complex_id(3);

        $this->mockGetOrCreateActiveTreeNodeAttempt();
        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt]);

        $this->assessmentTrackingService->saveAnswerForQuestion(
            $this->learningPath, $this->user, $this->treeNodes[12], 3, 'Answer Test', 2, 'Hint'
        );

        $this->assertEquals('Answer Test', $questionAttempt->get_answer());
    }

    public function testSaveAnswerForQuestionSetsScore()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt->set_question_complex_id(3);

        $this->mockGetOrCreateActiveTreeNodeAttempt();
        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt]);

        $this->assessmentTrackingService->saveAnswerForQuestion(
            $this->learningPath, $this->user, $this->treeNodes[12], 3, 'Answer Test', 2, 'Hint'
        );

        $this->assertEquals(2, $questionAttempt->get_score());
    }

    public function testSaveAnswerForQuestionSetsHint()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt->set_question_complex_id(3);

        $this->mockGetOrCreateActiveTreeNodeAttempt();
        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt]);

        $this->assessmentTrackingService->saveAnswerForQuestion(
            $this->learningPath, $this->user, $this->treeNodes[12], 3, 'Answer Test', 2, 'Hint'
        );

        $this->assertEquals('Hint', $questionAttempt->get_hint());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSaveAnswerForQuestionWithNoAssessmentNode()
    {
        $this->assessmentTrackingService->isMaximumAttemptsReachedForAssessment(
            $this->learningPath, $this->user, $this->treeNodes[1]
        );
    }

    public function testSaveAssessmentScore()
    {
        $activeAttempt = $this->treeNodeAttempts[12][1];

        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $this->trackingRepositoryMock->expects($this->once())
            ->method('update')
            ->with($activeAttempt);

        $this->assessmentTrackingService->saveAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[12], 45
        );
    }

    public function testSaveAssessmentScoreSetsScore()
    {
        $activeAttempt = $this->treeNodeAttempts[12][1];

        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $this->assessmentTrackingService->saveAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[12], 45
        );

        $this->assertEquals(45, $activeAttempt->get_score());
    }

    public function testSaveAssessmentScoreSetsCompleted()
    {
        $activeAttempt = $this->treeNodeAttempts[12][1];

        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $this->assessmentTrackingService->saveAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[12], 45
        );

        $this->assertTrue($activeAttempt->isCompleted());
    }

    public function testSaveAssessmentScoreSetsTotalTime()
    {
        $activeAttempt = $this->treeNodeAttempts[12][1];
        $activeAttempt->set_start_time(time() - 53);
        $activeAttempt->set_total_time(0);

        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $this->assessmentTrackingService->saveAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[12], 45
        );

        $this->assertTrue($activeAttempt->get_total_time() == 53 || $activeAttempt->get_total_time() == 54);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSaveAssessmentScoreWithNoAssessmentNode()
    {
        $this->assessmentTrackingService->saveAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[1]
        );
    }

    public function testChangeAssessmentScore()
    {
        $activeAttempt = $this->treeNodeAttempts[12][1];

        $this->mockGetTreeNodeAttemptById();

        $this->trackingRepositoryMock->expects($this->once())
            ->method('update')
            ->with($activeAttempt);

        $this->assessmentTrackingService->changeAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[12], 48, 67
        );

        $this->assertEquals(67, $activeAttempt->get_score());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testChangeAssessmentScoreWithNoAssessmentNode()
    {
        $this->assessmentTrackingService->changeAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[1], 48, 67
        );
    }

    public function testChangeQuestionScoreAndFeedback()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt->set_question_complex_id(3);

        $this->mockGetTreeNodeAttemptById();
        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt]);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('update')
            ->with($questionAttempt);

        $this->assessmentTrackingService->changeQuestionScoreAndFeedback(
            $this->learningPath, $this->user, $this->treeNodes[12], 48, 3, 5, 'Feedback'
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testChangeQuestionScoreAndFeedbackWithoutAttempt()
    {
        $this->mockGetTreeNodeAttemptById();
        $this->mockGetTreeNodeQuestionAttempts();

        $this->assessmentTrackingService->changeQuestionScoreAndFeedback(
            $this->learningPath, $this->user, $this->treeNodes[12], 48, 3, 5, 'Feedback'
        );
    }

    public function testChangeQuestionScoreAndFeedbackSetsScore()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt->set_question_complex_id(3);

        $this->mockGetTreeNodeAttemptById();
        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt]);

        $this->assessmentTrackingService->changeQuestionScoreAndFeedback(
            $this->learningPath, $this->user, $this->treeNodes[12], 48, 3, 5, 'Feedback'
        );

        $this->assertEquals(5, $questionAttempt->get_score());
    }

    public function testChangeQuestionScoreAndFeedbackSetsFeedback()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt->set_question_complex_id(3);

        $this->mockGetTreeNodeAttemptById();
        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt]);

        $this->assessmentTrackingService->changeQuestionScoreAndFeedback(
            $this->learningPath, $this->user, $this->treeNodes[12], 48, 3, 5, 'Feedback'
        );

        $this->assertEquals('Feedback', $questionAttempt->get_feedback());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testChangeQuestionScoreAndFeedbackWithNoAssessmentNode()
    {
        $this->assessmentTrackingService->changeQuestionScoreAndFeedback(
            $this->learningPath, $this->user, $this->treeNodes[1], 48, 3, 5, 'Feedback'
        );
    }

    public function testGetQuestionAttempts()
    {
        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $questionAttempt1 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt1->set_question_complex_id(3);

        $questionAttempt2 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt2->set_question_complex_id(6);

        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt1, $questionAttempt2]);

        $this->assertEquals(
            [3 => $questionAttempt1, 6 => $questionAttempt2],
            $this->assessmentTrackingService->getQuestionAttempts(
                $this->learningPath, $this->user, $this->treeNodes[12]
            )
        );
    }

    public function testGetQuestionAttemptsById()
    {
        $this->mockGetTreeNodeAttemptById();

        $questionAttempt1 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt1->set_question_complex_id(3);

        $questionAttempt2 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt2->set_question_complex_id(6);

        $this->mockGetTreeNodeQuestionAttempts([$questionAttempt1, $questionAttempt2]);

        $this->assertEquals(
            [3 => $questionAttempt1, 6 => $questionAttempt2],
            $this->assessmentTrackingService->getQuestionAttempts(
                $this->learningPath, $this->user, $this->treeNodes[12], 48
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetQuestionAttemptsByIdWithNoAssessmentNode()
    {
        $this->assessmentTrackingService->changeAssessmentScore(
            $this->learningPath, $this->user, $this->treeNodes[1], 48, 67
        );
    }

    public function testRegisterQuestionAttempts()
    {
        $activeAttempt = $this->treeNodeAttempts[12][1];
        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $questionAttempt1 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt1->set_question_complex_id(3);

        $questionAttempt2 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt2->set_question_complex_id(6);

        $this->attemptServiceMock->expects($this->exactly(2))
            ->method('createTreeNodeQuestionAttempt')
            ->with($activeAttempt)
            ->will($this->onConsecutiveCalls($questionAttempt1, $questionAttempt2));

        $this->assertEquals(
            [3 => $questionAttempt1, 6 => $questionAttempt2],
            $this->assessmentTrackingService->registerQuestionAttempts(
                $this->learningPath, $this->user, $this->treeNodes[12], [3, 6]
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterQuestionAttemptsWithNoAssessmentNode()
    {
        $this->assessmentTrackingService->registerQuestionAttempts(
            $this->learningPath, $this->user, $this->treeNodes[1], [3, 6]
        );
    }

    /**
     * @param TreeNode[] $treeNodes
     */
    protected function mockGetTreeNodeAttemptsForTreeNodes($treeNodes = array())
    {
        foreach ($treeNodes as $index => $treeNode)
        {
            $this->attemptServiceMock->expects($this->at($index))
                ->method('getTreeNodeAttemptsForTreeNode')
                ->with($this->learningPath, $this->user, $treeNode)
                ->will($this->returnValue($this->treeNodeAttempts[$treeNode->getId()]));
        }
    }

    protected function mockGetOrCreateActiveTreeNodeAttempt()
    {
        $this->attemptServiceMock->expects($this->once())
            ->method('getOrCreateActiveTreeNodeAttempt')
            ->with($this->learningPath, $this->treeNodes[12], $this->user)
            ->will($this->returnValue($this->treeNodeAttempts[12][1]));
    }

    protected function mockGetTreeNodeAttemptById()
    {
        $this->attemptTrackingServiceMock->expects($this->once())
            ->method('getTreeNodeAttemptById')
            ->with($this->learningPath, $this->user, $this->treeNodes[12], 48)
            ->will($this->returnValue($this->treeNodeAttempts[12][1]));
    }

    protected function mockGetTreeNodeQuestionAttempts($questionAttempts = array())
    {
        $this->attemptServiceMock->expects($this->once())
            ->method('getTreeNodeQuestionAttempts')
            ->with($this->treeNodeAttempts[12][1])
            ->will($this->returnValue($questionAttempts));
    }
}

