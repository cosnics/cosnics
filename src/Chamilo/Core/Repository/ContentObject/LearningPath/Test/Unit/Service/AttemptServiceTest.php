<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttemptServiceTest extends ChamiloTestCase
{
    /**
     * @var AttemptService
     */
    protected $attemptService;

    /**
     * @var TrackingRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackingRepositoryMock;

    /**
     * @var TrackingParametersInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackingParametersMock;

    /**
     * @var User
     */
    protected $user;

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
     * Setup before each test
     */
    protected function setUp(): void
    {
        $this->trackingRepositoryMock = $this->getMockBuilder(TrackingRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->trackingParametersMock = $this->getMockBuilder(TrackingParametersInterface::class)
            ->getMockForAbstractClass();

        $this->attemptService = new AttemptService($this->trackingRepositoryMock, $this->trackingParametersMock);

        $this->user = new User();
        $this->user->setId(3);

        $this->learningPath = new LearningPath();
        $this->learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $this->tree = new Tree();
        $this->treeNode = new TreeNode($this->tree, $this->learningPath, $treeNodeData);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void
    {
        unset($this->trackingParametersMock);
        unset($this->trackingRepositoryMock);
        unset($this->attemptService);
        unset($this->treeNode);
        unset($this->tree);
        unset($this->learningPath);
        unset($this->user);
    }

    public function testGetOrCreateActiveTreeNodeAttempt()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findActiveTreeNodeAttempt')
            ->with($this->learningPath, $this->treeNode, $this->user)
            ->will($this->returnValue($treeNodeAttempt));

        $this->assertEquals(
            $treeNodeAttempt,
            $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user)
        );
    }

    public function testGetOrCreateActiveTreeNodeAttemptWillCreateAttempt()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findActiveTreeNodeAttempt')
            ->with($this->learningPath, $this->treeNode, $this->user)
            ->will($this->returnValue(null));

        $this->trackingParametersMock->expects($this->once())
            ->method('createTreeNodeAttemptInstance')
            ->will($this->returnValue($treeNodeAttempt));

        $this->trackingRepositoryMock->expects($this->once())
            ->method('create')
            ->with($treeNodeAttempt);

        $this->assertEquals(
            $treeNodeAttempt,
            $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user)
        );
    }

    public function testGetOrCreateActiveTreeNodeAttemptUsesCache()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findActiveTreeNodeAttempt')
            ->with($this->learningPath, $this->treeNode, $this->user)
            ->will($this->returnValue($treeNodeAttempt));

        $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user);

        $this->assertEquals(
            $treeNodeAttempt,
            $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user)
        );
    }

    public function testGetActiveTreeNodeAttempt()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findActiveTreeNodeAttempt')
            ->with($this->learningPath, $this->treeNode, $this->user)
            ->will($this->returnValue($treeNodeAttempt));

        $this->assertEquals(
            $treeNodeAttempt,
            $this->attemptService->getActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user)
        );
    }

    public function testCreateTreeNodeAttempt()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingParametersMock->expects($this->once())
            ->method('createTreeNodeAttemptInstance')
            ->will($this->returnValue($treeNodeAttempt));

        $this->assertEquals(
            $treeNodeAttempt,
            $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user)
        );
    }

    public function testCreateTreeNodeAttemptSetsLearningPathId()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingParametersMock->expects($this->once())
            ->method('createTreeNodeAttemptInstance')
            ->will($this->returnValue($treeNodeAttempt));

        $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user);
        $this->assertEquals($this->learningPath->getId(), $treeNodeAttempt->getLearningPathId());
    }

    public function testCreateTreeNodeAttemptSetsUserId()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingParametersMock->expects($this->once())
            ->method('createTreeNodeAttemptInstance')
            ->will($this->returnValue($treeNodeAttempt));

        $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user);
        $this->assertEquals($this->user->getId(), $treeNodeAttempt->getUserId());
    }

    public function testCreateTreeNodeAttemptSetsTreeNodeId()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();

        $this->trackingParametersMock->expects($this->once())
            ->method('createTreeNodeAttemptInstance')
            ->will($this->returnValue($treeNodeAttempt));

        $this->attemptService->getOrCreateActiveTreeNodeAttempt($this->learningPath, $this->treeNode, $this->user);
        $this->assertEquals($this->treeNode->getId(), $treeNodeAttempt->getTreeNodeDataId());
    }

    public function testClearTreeNodeAttemptCache()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('clearTreeNodeAttemptCache');

        $this->attemptService->clearTreeNodeAttemptCache();
    }

    public function testGetTreeNodeAttempts()
    {
        $treeNodeAttempt1 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt1->setTreeNodeDataId(5);

        $treeNodeAttempt2 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt2->setTreeNodeDataId(5);

        $treeNodeAttempt3 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt3->setTreeNodeDataId(6);

        $attempts = [$treeNodeAttempt1, $treeNodeAttempt2, $treeNodeAttempt3];

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttempts')
            ->with($this->learningPath, $this->user)
            ->will($this->returnValue($attempts));

        $this->assertEquals(
            [5 => [$treeNodeAttempt1, $treeNodeAttempt2], 6 => [$treeNodeAttempt3]],
            $this->attemptService->getTreeNodeAttempts($this->learningPath, $this->user)
        );
    }

    public function testGetTreeNodeAttemptsUsesCache()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttempts')
            ->with($this->learningPath, $this->user)
            ->will($this->returnValue([]));

        $this->attemptService->getTreeNodeAttempts($this->learningPath, $this->user);
        $this->attemptService->getTreeNodeAttempts($this->learningPath, $this->user);
    }

    public function testGetTreeNodeAttemptsForTreeNode()
    {
        $treeNodeAttempt1 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt1->setTreeNodeDataId(5);

        $treeNodeAttempt2 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt2->setTreeNodeDataId(5);

        $treeNodeAttempt3 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt3->setTreeNodeDataId(6);

        $attempts = [$treeNodeAttempt1, $treeNodeAttempt2, $treeNodeAttempt3];

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttempts')
            ->with($this->learningPath, $this->user)
            ->will($this->returnValue($attempts));

        $this->assertEquals(
            [$treeNodeAttempt1, $treeNodeAttempt2],
            $this->attemptService->getTreeNodeAttemptsForTreeNode($this->learningPath, $this->user, $this->treeNode)
        );
    }

    public function testGetTreeNodeAttemptsForTreeNodeWithoutAttempts()
    {
        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttempts')
            ->with($this->learningPath, $this->user)
            ->will($this->returnValue([]));

        $this->assertEmpty(
            $this->attemptService->getTreeNodeAttemptsForTreeNode($this->learningPath, $this->user, $this->treeNode)
        );
    }

    public function testGetTreeNodeQuestionAttempts()
    {
        $questionAttempt1 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt1->set_question_complex_id(5);

        $questionAttempt2 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt2->set_question_complex_id(6);

        $questionAttempt3 = new LearningPathTreeNodeQuestionAttempt();
        $questionAttempt3->set_question_complex_id(7);

        $questionAttempts = [$questionAttempt1, $questionAttempt2, $questionAttempt3];

        $treeNodeAttempt1 = new LearningPathTreeNodeAttempt();

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeQuestionAttempts')
            ->with($treeNodeAttempt1)
            ->will($this->returnValue($questionAttempts));

        $this->assertEquals(
            [5 => $questionAttempt1, 6 => $questionAttempt2, 7=> $questionAttempt3],
            $this->attemptService->getTreeNodeQuestionAttempts($treeNodeAttempt1)
        );
    }

    public function testCreateTreeNodeQuestionAttempt()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();

        $this->trackingParametersMock->expects($this->once())
            ->method('createTreeNodeQuestionAttemptInstance')
            ->will($this->returnValue($questionAttempt));

        $this->trackingRepositoryMock->expects($this->once())
            ->method('create')
            ->with($questionAttempt);

        $treeNodeAttempt1 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt1->setId(5);

        $this->attemptService->createTreeNodeQuestionAttempt($treeNodeAttempt1, 8);
        $this->assertEquals(5, $questionAttempt->getTreeNodeAttemptId());
    }

    public function testCreateTreeNodeQuestionAttemptSetsQuestionId()
    {
        $questionAttempt = new LearningPathTreeNodeQuestionAttempt();

        $this->trackingParametersMock->expects($this->once())
            ->method('createTreeNodeQuestionAttemptInstance')
            ->will($this->returnValue($questionAttempt));

        $treeNodeAttempt = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt->setId(5);

        $this->attemptService->createTreeNodeQuestionAttempt($treeNodeAttempt, 8);
        $this->assertEquals(8, $questionAttempt->get_question_complex_id());
    }

    public function testDeleteTreeNodeAttempt()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt->setId(5);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($treeNodeAttempt);

        $this->attemptService->deleteTreeNodeAttempt($treeNodeAttempt);
    }

}