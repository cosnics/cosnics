<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\Tracking;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\ProgressCalculator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\TreeTestDataGenerator;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the ProgressCalculator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProgressCalculatorTest extends ChamiloTestCase
{

    /**
     *
     * @var ProgressCalculator
     */
    protected $progressCalculator;

    /**
     *
     * @var \PHPUnit_Framework_MockObject_MockObject | AttemptService
     */
    protected $attemptServiceMock;

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
     *    - Section A - ID: 2
     *        - Page 1 - ID: 6
     *        - Section E - ID: 8
     *              - Page 3 - ID: 9
     *    - Section B - ID: 3
     *    - Section C - ID: 4
     *        - Section D - ID: 5
     *            - Page 2 - ID: 7
     */
    protected function setUp(): void    {
        $this->attemptServiceMock = $this->getMockBuilder(AttemptService::class)->disableOriginalConstructor()->getMock();

        $this->progressCalculator = new ProgressCalculator($this->attemptServiceMock);

        $treeTestDataGenerator = new TreeTestDataGenerator();

        $this->tree = new Tree();
        $this->contentObjects = $treeTestDataGenerator->getContentObjects();
        $this->treeNodesData = $treeTestDataGenerator->getTreeNodesData();
        $this->treeNodes = $treeTestDataGenerator->getTreeNodes();

        $this->user = new User();
        $this->user->setId(2);

        $this->contentObjects[8] = new Section();
        $this->contentObjects[8]->setId(8);

        $this->treeNodesData[8] = new TreeNodeData();
        $this->treeNodesData[8]->setId(8);

        $this->contentObjects[9] = new Page();
        $this->contentObjects[9]->setId(9);

        $this->treeNodesData[9] = new TreeNodeData();
        $this->treeNodesData[9]->setId(9);

        $this->treeNodes[1] = new TreeNode($this->tree, $this->contentObjects[1], $this->treeNodesData[1]);
        $this->treeNodes[2] = new TreeNode($this->tree, $this->contentObjects[2], $this->treeNodesData[2]);
        $this->treeNodes[6] = new TreeNode($this->tree, $this->contentObjects[6], $this->treeNodesData[6]);
        $this->treeNodes[8] = new TreeNode($this->tree, $this->contentObjects[8], $this->treeNodesData[8]);
        $this->treeNodes[9] = new TreeNode($this->tree, $this->contentObjects[9], $this->treeNodesData[9]);
        $this->treeNodes[3] = new TreeNode($this->tree, $this->contentObjects[3], $this->treeNodesData[3]);
        $this->treeNodes[4] = new TreeNode($this->tree, $this->contentObjects[4], $this->treeNodesData[4]);
        $this->treeNodes[5] = new TreeNode($this->tree, $this->contentObjects[5], $this->treeNodesData[5]);
        $this->treeNodes[7] = new TreeNode($this->tree, $this->contentObjects[7], $this->treeNodesData[7]);

        $this->treeNodes[1]->addChildNode($this->treeNodes[2]);
        $this->treeNodes[2]->addChildNode($this->treeNodes[6]);
        $this->treeNodes[2]->addChildNode($this->treeNodes[8]);
        $this->treeNodes[8]->addChildNode($this->treeNodes[9]);
        $this->treeNodes[1]->addChildNode($this->treeNodes[3]);
        $this->treeNodes[1]->addChildNode($this->treeNodes[4]);
        $this->treeNodes[4]->addChildNode($this->treeNodes[5]);
        $this->treeNodes[5]->addChildNode($this->treeNodes[7]);

        $this->learningPath = $this->tree->getRoot()->getContentObject();

        $assessment = new Assessment();
        $assessment->setId(14);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(12)->setMasteryScore(80)->setContentObjectId(14)->setLearningPathId(
            $this->learningPath->getId());

        $treeNode = new TreeNode($this->tree, $assessment, $treeNodeData);
        $this->treeNodes[2]->addChildNode($treeNode);

        $treeNodeAttempt6 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt6->setId(1)->setTreeNodeDataId(6)->setCompleted(false);

        $treeNodeAttempt4 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt4->setId(2)->setTreeNodeDataId(4)->setCompleted(true);

        $treeNodeAttempt5 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt5->setId(3)->setTreeNodeDataId(5)->setCompleted(true);

        $treeNodeAttempt7 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt7->setId(4)->setTreeNodeDataId(7)->setCompleted(true);

        $treeNodeAttempt12 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt12->setId(5)->setTreeNodeDataId(12)->set_score(5)->setCompleted(true);

        $this->treeNodeAttempts = [
            6 => [$treeNodeAttempt6],
            4 => [$treeNodeAttempt4],
            5 => [$treeNodeAttempt5],
            7 => [$treeNodeAttempt7],
            12 => [$treeNodeAttempt12]];
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->progressCalculator);
        unset($this->attemptServiceMock);
        unset($this->user);
        unset($this->treeNodes);
        unset($this->treeNodesData);
        unset($this->contentObjects);
        unset($this->tree);
        unset($this->treeNodeAttempts);
        unset($this->learningPath);
    }

    public function testGetLearningPathProgress()
    {
        $this->mockGetTreeNodeAttempts(10, $this->treeNodeAttempts);
        $this->assertEquals(
            30,
            $this->progressCalculator->getLearningPathProgress($this->learningPath, $this->user, $this->tree->getRoot())
        );
    }

    public function testGetLearningPathProgressForTreeNode()
    {
        $this->mockGetTreeNodeAttempts(3, $this->treeNodeAttempts);
        $this->assertEquals(
            100,
            $this->progressCalculator->getLearningPathProgress(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testGetLearningPathProgressSectionNotCompletedUntilAllChildrenCompleted()
    {
        $treeNodeAttempt4 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt4->setId(2)->setTreeNodeDataId(4)->setCompleted(true);

        $treeNodeAttempt5 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt5->setId(3)->setTreeNodeDataId(5)->setCompleted(false);

        $treeNodeAttempt7 = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt7->setId(4)->setTreeNodeDataId(7)->setCompleted(true);

        $this->treeNodeAttempts = [
            4 => [$treeNodeAttempt4],
            5 => [$treeNodeAttempt5],
            7 => [$treeNodeAttempt7]];

        $this->mockGetTreeNodeAttempts(3, $this->treeNodeAttempts);
        $this->assertEquals(
            33,
            $this->progressCalculator->getLearningPathProgress(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testGetLearningPathProgressWithAssessmentAndMasteryScore()
    {
        $this->mockGetTreeNodeAttempts(5, $this->treeNodeAttempts);

        $this->assertEquals(
            0,
            $this->progressCalculator->getLearningPathProgress(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(2)));
    }

    public function testGetLearningPathProgressWithAssessmentAndMasteryScorePassed()
    {
        $this->treeNodeAttempts[12][0]->set_score(80);
        $this->mockGetTreeNodeAttempts(5, $this->treeNodeAttempts);

        $this->assertEquals(
            20,
            $this->progressCalculator->getLearningPathProgress(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(2)));
    }

    /**
     * @expectedException \TypeError
     */
    public function testGetLearningPathProgressWithoutTreeNode()
    {
        $this->progressCalculator->getLearningPathProgress($this->learningPath, $this->user, null);
    }

    public function testIsTreeNodeCompleted()
    {
        $this->mockGetTreeNodeAttempts(1, $this->treeNodeAttempts);

        $this->assertTrue(
            $this->progressCalculator->isTreeNodeCompleted(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(7)));
    }

    public function testIsTreeNodeCompletedReturnsFalse()
    {
        $this->mockGetTreeNodeAttempts(1, $this->treeNodeAttempts);

        $this->assertFalse(
            $this->progressCalculator->isTreeNodeCompleted(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(6)));
    }

    public function testIsTreeNodeCompletedOnCompletedSection()
    {
        $this->mockGetTreeNodeAttempts(3, $this->treeNodeAttempts);

        $this->assertTrue(
            $this->progressCalculator->isTreeNodeCompleted(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testIsTreeNodeCompletedOnNotCompletedSection()
    {
        $this->mockGetTreeNodeAttempts(5, $this->treeNodeAttempts);

        $this->assertFalse(
            $this->progressCalculator->isTreeNodeCompleted(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(2)));
    }

    public function testIsTreeNodeCompletedOnNotCompletedLearningPath()
    {
        $this->mockGetTreeNodeAttempts(10, $this->treeNodeAttempts);

        $this->assertFalse(
            $this->progressCalculator->isTreeNodeCompleted(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(1)));
    }

    public function testIsTreeNodeCompletedOnAssessmentWithMasteryScore()
    {
        $this->mockGetTreeNodeAttempts(1, $this->treeNodeAttempts);

        $this->assertFalse(
            $this->progressCalculator->isTreeNodeCompleted(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(12)));
    }

    public function testIsTreeNodeCompletedOnAssessmentWithMasteryScorePassed()
    {
        $this->treeNodeAttempts[12][0]->set_score(80);
        $this->mockGetTreeNodeAttempts(1, $this->treeNodeAttempts);

        $this->assertTrue(
            $this->progressCalculator->isTreeNodeCompleted(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(12)));
    }

    public function testIsTreeNodeCompletedUsesCache()
    {
        $this->mockGetTreeNodeAttempts(1, $this->treeNodeAttempts);

        $this->progressCalculator->isTreeNodeCompleted(
            $this->learningPath,
            $this->user,
            $this->tree->getTreeNodeById(7));

        $this->progressCalculator->isTreeNodeCompleted(
            $this->learningPath,
            $this->user,
            $this->tree->getTreeNodeById(7));
    }

    public function testIsCurrentTreeNodeBlocked()
    {
        $this->mockGetTreeNodeAttempts(5, $this->treeNodeAttempts);

        $this->treeNodes[2]->getTreeNodeData()->setBlocked(true);
        $this->assertTrue(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testIsCurrentTreeNodeBlockedReturnsFalse()
    {
        $this->assertFalse(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testIsCurrentTreeNodeBlockedReturnsFalseWhenChildOfBlockedNode()
    {
        $this->treeNodes[2]->getTreeNodeData()->setBlocked(true);

        $this->assertFalse(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(6)));
    }

    public function testIsCurrentTreeNodeBlockedReturnsTrueWhenEnforcingDefaultTraversingOrder()
    {
        $this->learningPath->setEnforceDefaultTraversingOrder(true);

        $this->assertTrue(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testIsCurrentTreeNodeBlockedReturnsFalseWhenEnforcingDefaultTraversingOrder()
    {
        $this->learningPath->setEnforceDefaultTraversingOrder(true);

        $this->assertFalse(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(2)));
    }

    public function testIsCurrentTreeNodeBlockedSectionEnforcesDefaultTraversingOrder()
    {
        $this->treeNodesData[2]->setEnforceDefaultTraversingOrder(true);

        $this->assertTrue(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath, $this->user, $this->tree->getTreeNodeById(9)
            )
        );
    }

    public function testIsCurrentTreeNodeBlockedSectionEnforcesDefaultTraversingOrderWhenNoParent()
    {
        $this->treeNodesData[2]->setEnforceDefaultTraversingOrder(true);

        $this->assertFalse(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath, $this->user, $this->tree->getTreeNodeById(4)
            )
        );
    }

    public function testIsCurrentTreeNodeBlockedSectionEnforcesDefaultTraversingOrderCompleted()
    {
        $this->treeNodesData[2]->setEnforceDefaultTraversingOrder(true);
        $this->treeNodeAttempts[6][0]->setCompleted(true);
        $this->mockGetTreeNodeAttempts(1, $this->treeNodeAttempts);

        $this->assertFalse(
            $this->progressCalculator->isCurrentTreeNodeBlocked(
                $this->learningPath, $this->user, $this->tree->getTreeNodeById(8)
            )
        );
    }

    public function testGetResponsibleNodesForBlockedTreeNode()
    {
        $this->mockGetTreeNodeAttempts(5, $this->treeNodeAttempts);

        $this->treeNodes[2]->getTreeNodeData()->setBlocked(true);

        $this->assertEquals(
            [$this->treeNodes[2]],
            $this->progressCalculator->getResponsibleNodesForBlockedTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testGetResponsibleNodesForBlockedTreeNodeWhenNotBlocked()
    {
        $this->assertEquals(
            [],
            $this->progressCalculator->getResponsibleNodesForBlockedTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testGetResponsibleNodesForBlockedTreeNodeWhenChildOfBlockedNode()
    {
        $this->treeNodes[2]->getTreeNodeData()->setBlocked(true);

        $this->assertEquals(
            [],
            $this->progressCalculator->getResponsibleNodesForBlockedTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(6)));
    }

    public function testGetResponsibleNodesForBlockedTreeNodeWhenEnforcingDefaultTraversingOrder()
    {
        $this->learningPath->setEnforceDefaultTraversingOrder(true);

        $this->assertEquals(
            [$this->treeNodes[2], $this->treeNodes[6], $this->treeNodes[8], $this->treeNodes[9], $this->treeNodes[3]],
            $this->progressCalculator->getResponsibleNodesForBlockedTreeNode(
                $this->learningPath,
                $this->user,
                $this->tree->getTreeNodeById(4)));
    }

    public function testGetResponsibleNodesForBlockedTreeNodeSectionEnforcesDefaultTraversingOrder()
    {
        $this->treeNodesData[2]->setEnforceDefaultTraversingOrder(true);

        $this->assertEquals(
            [$this->treeNodes[6]],
            $this->progressCalculator->getResponsibleNodesForBlockedTreeNode(
                $this->learningPath, $this->user, $this->tree->getTreeNodeById(9)
            )
        );
    }

    public function testGetResponsibleNodesForBlockedTreeNodeSectionEnforcesDefaultTraversingOrderSearchesFirstParent()
    {
        $this->treeNodesData[2]->setEnforceDefaultTraversingOrder(true);
        $this->treeNodesData[8]->setEnforceDefaultTraversingOrder(true);

        $this->assertEquals(
            [$this->treeNodes[6]],
            $this->progressCalculator->getResponsibleNodesForBlockedTreeNode(
                $this->learningPath, $this->user, $this->tree->getTreeNodeById(9)
            )
        );
    }

    protected function mockGetTreeNodeAttempts($callCount = 1, $treeNodeAttempts = [])
    {
        $this->attemptServiceMock->expects($this->exactly($callCount))->method('getTreeNodeAttempts')->with(
            $this->tree->getRoot()->getContentObject(),
            $this->user)->will($this->returnValue($treeNodeAttempts));
    }
}
