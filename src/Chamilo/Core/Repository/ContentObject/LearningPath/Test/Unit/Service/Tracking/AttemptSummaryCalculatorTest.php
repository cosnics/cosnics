<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\Tracking;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptSummaryCalculator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\TreeTestDataGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the AttemptSummaryCalculator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttemptSummaryCalculatorTest extends ChamiloTestCase
{

    /**
     *
     * @var AttemptSummaryCalculator
     */
    protected $attemptSummaryCalculator;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptService
     */
    protected $attemptServiceMock;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject | TrackingRepositoryInterface
     */
    protected $trackingRepositoryMock;

    /**
     *
     * @var Tree
     */
    protected $tree;

    /**
     *
     * @var LearningPath
     */
    protected $learningPath;

    /**
     *
     * @var LearningPath[] | Section[] | Page[] | ContentObject[]
     */
    protected $contentObjects;

    /**
     *
     * @var TreeNodeData[]
     */
    protected $treeNodesData;

    /**
     *
     * @var TreeNode[]
     */
    protected $treeNodes;

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var TreeNodeAttempt[][]
     */
    protected $treeNodeAttempts;

    /**
     * Setup before each test
     * - Learning Path A - ID: 1
     * - Section A - ID: 2
     * - Page 1 - ID: 6
     * - Section B - ID: 3
     * - Section C - ID: 4
     * - Section D - ID: 5
     * - Page 2 - ID: 7
     */
    public function setUp()
    {
        $this->attemptServiceMock = $this->getMockBuilder(AttemptService::class)->disableOriginalConstructor()->getMock();

        $this->trackingRepositoryMock = $this->getMockBuilder(TrackingRepositoryInterface::class)->disableOriginalConstructor()->getMockForAbstractClass();

        $this->attemptSummaryCalculator = new AttemptSummaryCalculator(
            $this->attemptServiceMock,
            $this->trackingRepositoryMock);

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
        $treeNodeData->setId(12)->setMasteryScore(80)->setContentObjectId(14)->setLearningPathId(
            $this->learningPath->getId());

        $treeNode = new TreeNode($this->tree, $assessment, $treeNodeData);
        $this->treeNodes[2]->addChildNode($treeNode);

        $treeNodeAttempt6 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt6->setId(1)->setTreeNodeDataId(6)->setCompleted(false)->set_total_time(20);

        $treeNodeAttempt4 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt4->setId(2)->setTreeNodeDataId(4)->set_total_time(38)->setCompleted(true);

        $treeNodeAttempt5 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt5->setId(3)->setTreeNodeDataId(5)->set_total_time(42)->setCompleted(true);

        $treeNodeAttempt7 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt7->setId(4)->setTreeNodeDataId(7)->set_total_time(16)->setCompleted(true);

        $treeNodeAttempt12 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt12->setId(5)->setTreeNodeDataId(12)->set_score(20)->set_total_time(123)->setCompleted(true);

        $treeNodeAttempt12b = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt12b->setId(6)->setTreeNodeDataId(12)->set_score(80)->set_total_time(150)->setCompleted(true);

        $this->treeNodeAttempts = [
            6 => [$treeNodeAttempt6],
            4 => [$treeNodeAttempt4],
            5 => [$treeNodeAttempt5],
            7 => [$treeNodeAttempt7],
            12 => [$treeNodeAttempt12, $treeNodeAttempt12b]];
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->attemptSummaryCalculator);
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

    public function testGetTotalTimeSpentInTreeNode()
    {
        $this->mockGetTreeNodeAttemptsForTreeNodes([$this->tree->getTreeNodeById(6)]);
        $this->assertEquals(
            20,
            $this->attemptSummaryCalculator->getTotalTimeSpentInTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(6)));
    }

    public function testGetTotalTimeSpentInTreeNodeWithChildNodes()
    {
        $this->mockGetTreeNodeAttemptsForTreeNodes(
            [$this->tree->getTreeNodeById(4), $this->tree->getTreeNodeById(5), $this->tree->getTreeNodeById(7)]);

        $this->assertEquals(
            96,
            $this->attemptSummaryCalculator->getTotalTimeSpentInTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testGetAverageScoreInTreeNode()
    {
        $this->mockGetTreeNodeAttemptsForTreeNodes([$this->tree->getTreeNodeById(12)]);

        $this->assertEquals(
            50,
            $this->attemptSummaryCalculator->getAverageScoreInTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(12)));
    }

    public function testGetAverageScoreInTreeNodeWithoutAttempts()
    {
        $this->assertEquals(
            0,
            $this->attemptSummaryCalculator->getAverageScoreInTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(12)));
    }

    public function testGetMaximumScoreInTreeNode()
    {
        $this->mockGetTreeNodeAttemptsForTreeNodes([$this->tree->getTreeNodeById(12)]);

        $this->assertEquals(
            80,
            $this->attemptSummaryCalculator->getMaximumScoreInTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(12)));
    }

    public function testGetMinimumScoreInTreeNode()
    {
        $this->mockGetTreeNodeAttemptsForTreeNodes([$this->tree->getTreeNodeById(12)]);

        $this->assertEquals(
            20,
            $this->attemptSummaryCalculator->getMinimumScoreInTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(12)));
    }

    public function testGetMinimumScoreInTreeNodeWithoutAttempts()
    {
        $this->assertEquals(
            0,
            $this->attemptSummaryCalculator->getMinimumScoreInTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(12)));
    }

    public function testFindTargetUsersWithoutLearningPathAttempts()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->assertCount(
            3,
            $this->attemptSummaryCalculator->findTargetUsersWithoutLearningPathAttempts($this->learningPath, $treeNode));
    }

    public function testFindTargetUsersWithoutLearningPathAttemptsUsesCache()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->attemptSummaryCalculator->findTargetUsersWithoutLearningPathAttempts($this->learningPath, $treeNode);
        $this->attemptSummaryCalculator->findTargetUsersWithoutLearningPathAttempts($this->learningPath, $treeNode);
    }

    public function testCountTargetUsersWithoutLearningPathAttempts()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->assertEquals(
            3,
            $this->attemptSummaryCalculator->countTargetUsersWithoutLearningPathAttempts($this->learningPath, $treeNode));
    }

    public function testCountTargetUsersWithoutLearningPathAttemptsUsesCache()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->attemptSummaryCalculator->countTargetUsersWithoutLearningPathAttempts($this->learningPath, $treeNode);
        $this->attemptSummaryCalculator->countTargetUsersWithoutLearningPathAttempts($this->learningPath, $treeNode);
    }

    public function testCountTargetUsersWithFullLearningPathAttempts()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->assertEquals(
            2,
            $this->attemptSummaryCalculator->countTargetUsersWithFullLearningPathAttempts(
                $this->learningPath,
                $treeNode));
    }

    public function testCountTargetUsersWithFullLearningPathAttemptsUsesCache()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->attemptSummaryCalculator->countTargetUsersWithFullLearningPathAttempts($this->learningPath, $treeNode);
        $this->attemptSummaryCalculator->countTargetUsersWithFullLearningPathAttempts($this->learningPath, $treeNode);
    }

    public function testCountTargetUsersWithPartialLearningPathAttempts()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->assertEquals(
            4,
            $this->attemptSummaryCalculator->countTargetUsersWithPartialLearningPathAttempts(
                $this->learningPath,
                $treeNode));
    }

    public function testCountTargetUsersWithPartialLearningPathAttemptsUsesCache()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->attemptSummaryCalculator->countTargetUsersWithPartialLearningPathAttempts($this->learningPath, $treeNode);

        $this->attemptSummaryCalculator->countTargetUsersWithPartialLearningPathAttempts($this->learningPath, $treeNode);
    }

    public function testFindTargetUsersWithPartialLearningPathAttempts()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->assertCount(
            4,
            $this->attemptSummaryCalculator->findTargetUsersWithPartialLearningPathAttempts(
                $this->learningPath,
                $treeNode));
    }

    public function testFindTargetUsersWithPartialLearningPathAttemptsUsesCache()
    {
        $treeNode = $this->tree->getTreeNodeById(4);
        $nodesCompletedPerUser = [2 => 0, 4 => 3, 8 => 2, 10 => 3, 16 => 1, 20 => 0, 25 => 0, 38 => 2, 40 => 1];
        $this->mockFindTargetUsersWithLearningPathAttempts($treeNode, $nodesCompletedPerUser);

        $this->attemptSummaryCalculator->findTargetUsersWithPartialLearningPathAttempts($this->learningPath, $treeNode);

        $this->attemptSummaryCalculator->findTargetUsersWithPartialLearningPathAttempts($this->learningPath, $treeNode);
    }

    protected function mockFindTargetUsersWithLearningPathAttempts(TreeNode $treeNode, $nodesCompletedPerUser = array())
    {
        $usersWithCompletedNodesCount = array();

        foreach ($nodesCompletedPerUser as $userId => $nodesCompletedForUser)
        {
            $usersWithCompletedNodesCount[] = ['user_id' => $userId, 'nodes_completed' => $nodesCompletedForUser];
        }

        $this->trackingRepositoryMock->expects($this->once())->method('findTargetUsersWithLearningPathAttempts')->with(
            $this->learningPath,
            $treeNode->getTreeNodeDataIdsFromSelfAndDescendants())->will(
            $this->returnValue($usersWithCompletedNodesCount));
    }

    /**
     *
     * @param TreeNode[] $treeNodes
     */
    protected function mockGetTreeNodeAttemptsForTreeNodes($treeNodes = array())
    {
        foreach ($treeNodes as $index => $treeNode)
        {
            $this->attemptServiceMock->expects($this->at($index))->method('getTreeNodeAttemptsForTreeNode')->with(
                $this->learningPath,
                $this->user,
                $treeNode)->will($this->returnValue($this->treeNodeAttempts[$treeNode->getId()]));
        }
    }
}