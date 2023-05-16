<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\Tracking;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\AttemptTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;

/**
 * Tests the AttemptTrackingService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttemptTrackingServiceTest extends ChamiloTestCase
{
    /**
     * @var AttemptTrackingService
     */
    protected $attemptTrackingService;

    /**
     * @var AttemptService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attemptServiceMock;

    /**
     * @var TrackingRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackingRepositoryMock;

    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var TreeNodeAttempt
     */
    protected $treeNodeAttempt;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->attemptServiceMock = $this->getMockBuilder(AttemptService::class)
            ->disableOriginalConstructor()->getMock();

        $this->trackingRepositoryMock = $this->getMockBuilder(TrackingRepositoryInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->attemptTrackingService =
            new AttemptTrackingService($this->attemptServiceMock, $this->trackingRepositoryMock);

        $this->learningPath = new LearningPath();
        $this->learningPath->setId(5);

        $this->user = new User();
        $this->user->setId(8);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(4);

        $this->tree = new Tree();
        $this->treeNode = new TreeNode($this->tree, $this->learningPath, $treeNodeData);

        $this->treeNodeAttempt = new LearningPathTreeNodeAttempt();
        $this->treeNodeAttempt->setId(10);
        $this->treeNodeAttempt->setTreeNodeDataId(4);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->attemptTrackingService);
        unset($this->trackingRepositoryMock);
        unset($this->attemptTrackingService);
        unset($this->treeNode);
        unset($this->tree);
        unset($this->user);
        unset($this->learningPath);
    }

    public function testTrackAttemptForUser()
    {
        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $this->attemptTrackingService->trackAttemptForUser($this->learningPath, $this->treeNode, $this->user);
    }

    public function testSetActiveAttemptCompleted()
    {
        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $this->attemptTrackingService->setActiveAttemptCompleted($this->learningPath, $this->treeNode, $this->user);

        $this->assertTrue($this->treeNodeAttempt->isCompleted());
    }

    public function testGetActiveAttemptId()
    {
        $this->mockGetOrCreateActiveTreeNodeAttempt();

        $this->assertEquals(
            $this->treeNodeAttempt->getId(),
            $this->attemptTrackingService->getActiveAttemptId($this->learningPath, $this->treeNode, $this->user)
        );
    }

    public function testSetActiveAttemptTotalTime()
    {
        $this->mockGetOrCreateActiveTreeNodeAttempt();
        $this->mockUpdate();

        $time = time();
        $this->treeNodeAttempt->set_start_time($time - 15);

        $this->attemptTrackingService->setActiveAttemptTotalTime($this->learningPath, $this->treeNode, $this->user);

        $this->assertTrue(
            $this->treeNodeAttempt->get_total_time() == 15 || $this->treeNodeAttempt->get_total_time() == 16
        );
    }

    public function testSetAttemptTotalTimeByTreeNodeAttemptId()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttemptById')
            ->with(10)
            ->will($this->returnValue($this->treeNodeAttempt));

        $this->mockUpdate();

        $time = time();
        $this->treeNodeAttempt->set_start_time($time - 19);

        $this->attemptTrackingService->setAttemptTotalTimeByTreeNodeAttemptId(10);

        $this->assertTrue(
            $this->treeNodeAttempt->get_total_time() == 19 || $this->treeNodeAttempt->get_total_time() == 20
        );
    }

    /**
     * @expectedException \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function testSetAttemptTotalTimeByTreeNodeAttemptIdThrowsExceptionWhenAttemptNotFound()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttemptById')
            ->with(10)
            ->will($this->returnValue(null));

        $this->attemptTrackingService->setAttemptTotalTimeByTreeNodeAttemptId(10);
    }

    public function testGetTreeNodeAttemptById()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();

        $this->assertEquals(
            $this->treeNodeAttempt,
            $this->attemptTrackingService->getTreeNodeAttemptById($this->learningPath, $this->user, $this->treeNode, 10)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetTreeNodeAttemptByIdWithInvalidId()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();
        $this->attemptTrackingService->getTreeNodeAttemptById($this->learningPath, $this->user, $this->treeNode, 5);
    }

    public function testDeleteTreeNodeAttemptById()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();

        $this->attemptServiceMock->expects($this->once())
            ->method('deleteTreeNodeAttempt')
            ->with($this->treeNodeAttempt);

        $this->attemptTrackingService->deleteTreeNodeAttemptById(
            $this->learningPath, $this->user, $this->user, $this->treeNode, 10
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDeleteTreeNodeAttemptByIdWithInvalidId()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();
        $this->attemptTrackingService->deleteTreeNodeAttemptById(
            $this->learningPath, $this->user, $this->user, $this->treeNode, 5
        );
    }

    /**
     * @expectedException \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function testDeleteTreeNodeAttemptByIdWithInvalidRights()
    {
        $this->attemptTrackingService->deleteTreeNodeAttemptById(
            $this->learningPath, $this->user, new User(), $this->treeNode, 5
        );
    }

    public function testDeleteTreeNodeAttemptsForTreeNode()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();

        $this->attemptServiceMock->expects($this->once())
            ->method('deleteTreeNodeAttempt')
            ->with($this->treeNodeAttempt);

        $this->attemptTrackingService->deleteTreeNodeAttemptsForTreeNode(
            $this->learningPath, $this->user, $this->user, $this->treeNode
        );
    }

    /**
     * @expectedException \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function testDeleteTreeNodeAttemptsForTreeNodeWithInvalidRights()
    {
        $this->attemptTrackingService->deleteTreeNodeAttemptsForTreeNode(
            $this->learningPath, $this->user, new User(), $this->treeNode
        );
    }

    public function testCanDeleteLearningPathAttemptDataWithSameUser()
    {
        $this->assertTrue($this->attemptTrackingService->canDeleteLearningPathAttemptData($this->user, $this->user));
    }

    public function testCanDeleteLearningPathAttemptDataWithPlatformAdmin()
    {
        $this->user->set_platformadmin(true);
        $this->assertTrue($this->attemptTrackingService->canDeleteLearningPathAttemptData($this->user, new User()));
    }

    public function testCanDeleteLearningPathAttemptDataWithOtherUser()
    {
        $this->assertFalse($this->attemptTrackingService->canDeleteLearningPathAttemptData($this->user, new User()));
    }

    public function testHasTreeNodeAttempts()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();
        $this->assertTrue(
            $this->attemptTrackingService->hasTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode)
        );
    }

    public function testHasTreeNodeAttemptsReturnsFalse()
    {
        $this->attemptServiceMock->expects($this->once())
            ->method('getTreeNodeAttemptsForTreeNode')
            ->will($this->returnValue([]));

        $this->assertFalse(
            $this->attemptTrackingService->hasTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode)
        );
    }

    public function testCountTreeNodeAttempts()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();
        $this->assertEquals(
            1,
            $this->attemptTrackingService->countTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode)
        );
    }

    public function testCountTreeNodeAttemptsReturnsEmpty()
    {
        $this->attemptServiceMock->expects($this->once())
            ->method('getTreeNodeAttemptsForTreeNode')
            ->will($this->returnValue([]));

        $this->assertEquals(
            0,
            $this->attemptTrackingService->countTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode)
        );
    }

    public function testGetTreeNodeAttempts()
    {
        $this->mockGetTreeNodeAttemptsForTreeNode();
        $this->assertEquals(
            [$this->treeNodeAttempt],
            $this->attemptTrackingService->getTreeNodeAttempts($this->learningPath, $this->user, $this->treeNode)
        );
    }

    public function testCountLearningPathAttemptsWithUsers()
    {
        $condition = new AndCondition([]);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('countLearningPathAttemptsWithUser')
            ->with($this->learningPath, [4], $condition)
            ->will($this->returnValue(5));

        $this->assertEquals(
            5,
            $this->attemptTrackingService->countLearningPathAttemptsWithUsers(
                $this->learningPath, $this->treeNode, $condition
            )
        );
    }

    public function testCountLearningPathAttemptsWithUsersWithoutTreeNode()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('countLearningPathAttemptsWithUser')
            ->with($this->learningPath, [])
            ->will($this->returnValue(10));

        $this->assertEquals(
            10,
            $this->attemptTrackingService->countLearningPathAttemptsWithUsers($this->learningPath)
        );
    }

    public function testGetLearningPathAttemptsWithUser()
    {
        $condition = new AndCondition([]);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findLearningPathAttemptsWithUser')
            ->with($this->learningPath, [4], $condition, 1, 8, [])
            ->will($this->returnValue([$this->treeNodeAttempt]));

        $this->assertEquals(
            [$this->treeNodeAttempt],
            $this->attemptTrackingService->getLearningPathAttemptsWithUser(
                $this->learningPath, $this->treeNode, $condition, 1, 8, []
            )
        );
    }

    public function testGetLearningPathAttemptsWithUserWithoutTreeNode()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('findLearningPathAttemptsWithUser')
            ->with($this->learningPath, [])
            ->will($this->returnValue([$this->treeNodeAttempt]));

        $this->assertEquals(
            [$this->treeNodeAttempt],
            $this->attemptTrackingService->getLearningPathAttemptsWithUser($this->learningPath)
        );
    }

    public function testCountTargetUsersWithLearningPathAttempts()
    {
        $condition = new AndCondition([]);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('countTargetUsersForLearningPath')
            ->with($this->learningPath, $condition)
            ->will($this->returnValue(5));

        $this->assertEquals(
            5,
            $this->attemptTrackingService->countTargetUsersWithLearningPathAttempts($this->learningPath, $condition)
        );
    }

    public function testGetTargetUsersWithLearningPathAttempts()
    {
        $condition = new AndCondition([]);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTargetUsersWithLearningPathAttempts')
            ->with($this->learningPath, [4], $condition, 1, 8, [])
            ->will($this->returnValue([$this->treeNodeAttempt]));

        $this->assertEquals(
            [$this->treeNodeAttempt],
            $this->attemptTrackingService->getTargetUsersWithLearningPathAttempts(
                $this->learningPath, $this->treeNode, $condition, 1, 8, []
            )
        );
    }

    public function testGetTargetUsersWithLearningPathAttemptsWithoutTreeNode()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTargetUsersWithLearningPathAttempts')
            ->with($this->learningPath, [])
            ->will($this->returnValue([$this->treeNodeAttempt]));

        $this->assertEquals(
            [$this->treeNodeAttempt],
            $this->attemptTrackingService->getTargetUsersWithLearningPathAttempts($this->learningPath)
        );
    }

    public function testcountTargetUsers()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('countTargetUsersForLearningPath')
            ->with($this->learningPath)
            ->will($this->returnValue(5));

        $this->assertEquals(
            5,
            $this->attemptTrackingService->countTargetUsers($this->learningPath)
        );
    }

    protected function mockGetOrCreateActiveTreeNodeAttempt()
    {
        $this->attemptServiceMock->expects($this->once())
            ->method('getOrCreateActiveTreeNodeAttempt')
            ->with($this->learningPath, $this->treeNode, $this->user)
            ->will($this->returnValue($this->treeNodeAttempt));
    }

    protected function mockGetTreeNodeAttemptsForTreeNode()
    {
        $this->attemptServiceMock->expects($this->once())
            ->method('getTreeNodeAttemptsForTreeNode')
            ->with($this->learningPath, $this->user, $this->treeNode)
            ->will($this->returnValue([$this->treeNodeAttempt]));
    }

    protected function mockUpdate()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('update')
            ->with($this->treeNodeAttempt)
            ->will($this->returnValue(true));
    }

}

