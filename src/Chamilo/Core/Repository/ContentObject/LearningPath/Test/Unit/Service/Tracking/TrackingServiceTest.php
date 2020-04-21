<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AssessmentTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptSummaryCalculator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\ProgressCalculator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;

/**
 * Tests the TrackingService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingServiceTest extends ChamiloTestCase
{

    /**
     *
     * @var TrackingService
     */
    protected $trackingService;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptTrackingService
     */
    protected $attemptTrackingServiceMock;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptSummaryCalculator
     */
    protected $attemptSummaryCalculatorMock;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject | AssessmentTrackingService
     */
    protected $assessmentTrackingServiceMock;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject | ProgressCalculator
     */
    protected $progressCalculatorMock;

    /**
     *
     * @var LearningPath
     */
    protected $learningPath;

    /**
     *
     * @var Tree
     */
    protected $tree;

    /**
     *
     * @var TreeNode
     */
    protected $treeNode;

    /**
     *
     * @var User
     */
    protected $user;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->attemptTrackingServiceMock = $this->getMockBuilder(AttemptTrackingService::class)->disableOriginalConstructor()->getMock();

        $this->attemptSummaryCalculatorMock = $this->getMockBuilder(AttemptSummaryCalculator::class)->disableOriginalConstructor()->getMock();

        $this->assessmentTrackingServiceMock = $this->getMockBuilder(AssessmentTrackingService::class)->disableOriginalConstructor()->getMock();

        $this->progressCalculatorMock = $this->getMockBuilder(ProgressCalculator::class)->disableOriginalConstructor()->getMock();

        $this->trackingService = new TrackingService(
            $this->attemptTrackingServiceMock,
            $this->attemptSummaryCalculatorMock,
            $this->assessmentTrackingServiceMock,
            $this->progressCalculatorMock);

        $this->learningPath = new LearningPath();
        $this->learningPath->setId(5);

        $this->user = new User();
        $this->user->setId(8);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(4);

        $this->tree = new Tree();
        $this->treeNode = new TreeNode($this->tree, $this->learningPath, $treeNodeData);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->trackingService);
        unset($this->progressCalculatorMock);
        unset($this->assessmentTrackingServiceMock);
        unset($this->attemptSummaryCalculatorMock);
        unset($this->attemptTrackingServiceMock);
        unset($this->treeNode);
        unset($this->tree);
        unset($this->user);
        unset($this->learningPath);
    }

    public function testTrackAttemptForUser()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('trackAttemptForUser')->with(
            $this->learningPath,
            $this->treeNode,
            $this->user);

        $this->trackingService->trackAttemptForUser($this->learningPath, $this->treeNode, $this->user);
    }

    public function testSetActiveAttemptCompleted()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('setActiveAttemptCompleted')->with(
            $this->learningPath,
            $this->treeNode,
            $this->user);

        $this->trackingService->setActiveAttemptCompleted($this->learningPath, $this->treeNode, $this->user);
    }

    public function testGetActiveAttemptId()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('getActiveAttemptId')->with(
            $this->learningPath,
            $this->treeNode,
            $this->user);

        $this->trackingService->getActiveAttemptId($this->learningPath, $this->treeNode, $this->user);
    }

    public function testSetActiveAttemptTotalTime()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('setActiveAttemptTotalTime')->with(
            $this->learningPath,
            $this->treeNode,
            $this->user);

        $this->trackingService->setActiveAttemptTotalTime($this->learningPath, $this->treeNode, $this->user);
    }

    public function testSetAttemptTotalTimeByTreeNodeAttemptId()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('setAttemptTotalTimeByTreeNodeAttemptId')->with(
            5);

        $this->trackingService->setAttemptTotalTimeByTreeNodeAttemptId(5);
    }

    public function testGetLearningPathProgress()
    {
        $this->progressCalculatorMock->expects($this->once())->method('getLearningPathProgress')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getLearningPathProgress($this->learningPath, $this->user, $this->treeNode);
    }

    public function testIsTreeNodeCompleted()
    {
        $this->progressCalculatorMock->expects($this->once())->method('isTreeNodeCompleted')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->isTreeNodeCompleted($this->learningPath, $this->user, $this->treeNode);
    }

    public function testIsMaximumAttemptsReachedForAssessment()
    {
        $this->assessmentTrackingServiceMock->expects($this->once())->method('isMaximumAttemptsReachedForAssessment')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->isMaximumAttemptsReachedForAssessment($this->learningPath, $this->user, $this->treeNode);
    }

    public function testSaveAnswerForQuestion()
    {
        $this->assessmentTrackingServiceMock->expects($this->once())->method('saveAnswerForQuestion')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            5,
            'test',
            1,
            'test');

        $this->trackingService->saveAnswerForQuestion(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            5,
            'test',
            1,
            'test');
    }

    public function testSaveAssessmentScore()
    {
        $this->assessmentTrackingServiceMock->expects($this->once())->method('saveAssessmentScore')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            1);

        $this->trackingService->saveAssessmentScore($this->learningPath, $this->user, $this->treeNode, 1);
    }

    public function testChangeAssessmentScore()
    {
        $this->assessmentTrackingServiceMock->expects($this->once())->method('changeAssessmentScore')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            5,
            1);

        $this->trackingService->changeAssessmentScore($this->learningPath, $this->user, $this->treeNode, 5, 1);
    }

    public function testChangeQuestionScoreAndFeedback()
    {
        $this->assessmentTrackingServiceMock->expects($this->once())->method('changeQuestionScoreAndFeedback')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            5,
            1,
            'test feedback');

        $this->trackingService->changeQuestionScoreAndFeedback(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            5,
            1,
            'test feedback');
    }

    public function testGetQuestionAttempts()
    {
        $this->assessmentTrackingServiceMock->expects($this->once())->method('getQuestionAttempts')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            5);

        $this->trackingService->getQuestionAttempts($this->learningPath, $this->user, $this->treeNode, 5);
    }

    public function testGegisterQuestionAttempts()
    {
        $this->assessmentTrackingServiceMock->expects($this->once())->method('registerQuestionAttempts')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            [5, 2, 8]);

        $this->trackingService->registerQuestionAttempts($this->learningPath, $this->user, $this->treeNode, [
            5,
            2,
            8]);
    }

    public function testGetTreeNodeAttemptById()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('getTreeNodeAttemptById')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode,
            5);

        $this->trackingService->getTreeNodeAttemptById($this->learningPath, $this->user, $this->treeNode, 5);
    }

    public function testDeleteTreeNodeAttemptById()
    {
        $reportingUser = new User();
        $reportingUser->setId(5);

        $this->attemptTrackingServiceMock->expects($this->once())->method('deleteTreeNodeAttemptById')->with(
            $this->learningPath,
            $this->user,
            $reportingUser,
            $this->treeNode,
            5);

        $this->trackingService->deleteTreeNodeAttemptById(
            $this->learningPath,
            $this->user,
            $reportingUser,
            $this->treeNode,
            5);
    }

    public function testDeleteTreeNodeAttemptsForTreeNode()
    {
        $reportingUser = new User();
        $reportingUser->setId(5);

        $this->attemptTrackingServiceMock->expects($this->once())->method('deleteTreeNodeAttemptsForTreeNode')->with(
            $this->learningPath,
            $this->user,
            $reportingUser,
            $this->treeNode);

        $this->trackingService->deleteTreeNodeAttemptsForTreeNode(
            $this->learningPath,
            $this->user,
            $reportingUser,
            $this->treeNode);
    }

    public function testCanDeleteLearningPathAttemptData()
    {
        $reportingUser = new User();
        $reportingUser->setId(5);

        $this->attemptTrackingServiceMock->expects($this->once())->method('canDeleteLearningPathAttemptData')->with(
            $this->user,
            $reportingUser);

        $this->trackingService->canDeleteLearningPathAttemptData($this->user, $reportingUser);
    }

    public function testIsCurrentTreeNodeBlocked()
    {
        $this->progressCalculatorMock->expects($this->once())->method('isCurrentTreeNodeBlocked')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->isCurrentTreeNodeBlocked($this->learningPath, $this->user, $this->treeNode);
    }

    public function testGetResponsibleNodesForBlockedTreeNode()
    {
        $this->progressCalculatorMock->expects($this->once())->method('getResponsibleNodesForBlockedTreeNode')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getResponsibleNodesForBlockedTreeNode($this->learningPath, $this->user, $this->treeNode);
    }

    public function testHasTreeNodeAttempts()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('hasTreeNodeAttempts')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->hasTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode);
    }

    public function testCountTreeNodeAttempts()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('countTreeNodeAttempts')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->countTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode);
    }

    public function testGetTreeNodeAttempts()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('getTreeNodeAttempts')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode);
    }

    public function testGetTotalTimeSpentInTreeNode()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method('getTotalTimeSpentInTreeNode')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getTotalTimeSpentInTreeNode($this->learningPath, $this->user, $this->treeNode);
    }

    public function testGetAverageScoreInTreeNode()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method('getAverageScoreInTreeNode')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getAverageScoreInTreeNode($this->learningPath, $this->user, $this->treeNode);
    }

    public function testGetMaximumScoreInTreeNode()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method('getMaximumScoreInTreeNode')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getMaximumScoreInTreeNode($this->learningPath, $this->user, $this->treeNode);
    }

    public function testGetMinimumScoreInTreeNode()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method('getMinimumScoreInTreeNode')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getMinimumScoreInTreeNode($this->learningPath, $this->user, $this->treeNode);
    }

    public function testGetLastAttemptScoreForTreeNode()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method('getLastAttemptScoreForTreeNode')->with(
            $this->learningPath,
            $this->user,
            $this->treeNode);

        $this->trackingService->getLastAttemptScoreForTreeNode($this->learningPath, $this->user, $this->treeNode);
    }

    public function testCountLearningPathAttemptsWithUsers()
    {
        $condition = new AndCondition([]);

        $this->attemptTrackingServiceMock->expects($this->once())->method('countLearningPathAttemptsWithUsers')->with(
            $this->learningPath,
            $this->treeNode,
            $condition);

        $this->trackingService->countLearningPathAttemptsWithUsers($this->learningPath, $this->treeNode, $condition);
    }

    public function testGetLearningPathAttemptsWithUser()
    {
        $condition = new AndCondition([]);

        $this->attemptTrackingServiceMock->expects($this->once())->method('getLearningPathAttemptsWithUser')->with(
            $this->learningPath,
            $this->treeNode,
            $condition,
            0,
            0,
            []);

        $this->trackingService->getLearningPathAttemptsWithUser(
            $this->learningPath,
            $this->treeNode,
            $condition,
            0,
            0,
            []);
    }

    public function testCountTargetUsersWithLearningPathAttempts()
    {
        $condition = new AndCondition([]);

        $this->attemptTrackingServiceMock->expects($this->once())->method('countTargetUsersWithLearningPathAttempts')->with(
            $this->learningPath,
            $condition);

        $this->trackingService->countTargetUsersWithLearningPathAttempts($this->learningPath, $condition);
    }

    public function testGetTargetUsersWithLearningPathAttempts()
    {
        $condition = new AndCondition([]);

        $this->attemptTrackingServiceMock->expects($this->once())->method('getTargetUsersWithLearningPathAttempts')->with(
            $this->learningPath,
            $this->treeNode,
            $condition,
            0,
            0,
            []);

        $this->trackingService->getTargetUsersWithLearningPathAttempts(
            $this->learningPath,
            $this->treeNode,
            $condition,
            0,
            0,
            []);
    }

    public function testFindTargetUsersWithoutLearningPathAttempts()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method(
            'findTargetUsersWithoutLearningPathAttempts')->with($this->learningPath, $this->treeNode);

        $this->trackingService->findTargetUsersWithoutLearningPathAttempts($this->learningPath, $this->treeNode);
    }

    public function testCountTargetUsersWithoutLearningPathAttempts()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method(
            'countTargetUsersWithoutLearningPathAttempts')->with($this->learningPath, $this->treeNode);

        $this->trackingService->countTargetUsersWithoutLearningPathAttempts($this->learningPath, $this->treeNode);
    }

    public function testCountTargetUsersWithFullLearningPathAttempts()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method(
            'countTargetUsersWithFullLearningPathAttempts')->with($this->learningPath, $this->treeNode);

        $this->trackingService->countTargetUsersWithFullLearningPathAttempts($this->learningPath, $this->treeNode);
    }

    public function testFindTargetUsersWithPartialLearningPathAttempts()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method(
            'findTargetUsersWithPartialLearningPathAttempts')->with($this->learningPath, $this->treeNode);

        $this->trackingService->findTargetUsersWithPartialLearningPathAttempts($this->learningPath, $this->treeNode);
    }

    public function testCountTargetUsersWithPartialLearningPathAttempts()
    {
        $this->attemptSummaryCalculatorMock->expects($this->once())->method(
            'countTargetUsersWithPartialLearningPathAttempts')->with($this->learningPath, $this->treeNode);

        $this->trackingService->countTargetUsersWithPartialLearningPathAttempts($this->learningPath, $this->treeNode);
    }

    public function testCountTargetUsers()
    {
        $this->attemptTrackingServiceMock->expects($this->once())->method('countTargetUsers')->with($this->learningPath);

        $this->trackingService->countTargetUsers($this->learningPath);
    }
}