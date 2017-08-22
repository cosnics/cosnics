<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Storage\Repository;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManagerWrapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloFixturesBasedTestCase;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Tests the TrackingRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingRepositoryTest extends ChamiloFixturesBasedTestCase
{
    /**
     * @var TrackingRepository
     */
    protected $trackingRepository;

    /**
     * @var DataManagerWrapper | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataManagerWrapperMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        parent::setUp();

        $this->dataManagerWrapperMock = $this->getMockBuilder(DataManagerWrapper::class)
            ->disableOriginalConstructor()->getMock();

        $trackingParameters = new TrackingParameters(1, $this->dataManagerWrapperMock);
        $this->trackingRepository = new TrackingRepository($this->getTestDataClassRepository(), $trackingParameters);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->trackingRepository);
    }

    /**
     * Returns the storage units that need to be created. This method requires a multidimensional array with the
     * names of the storage units per context
     *
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return string[][]
     */
    protected function getStorageUnitsToCreate()
    {
        return [
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking' => [
                'learning_path_tree_node_attempt', 'learning_path_tree_node_question_attempt'
            ],
            'Chamilo\Core\User' => ['user']
        ];
    }

    /**
     * Returns the fixture files that need to be inserted. This method requires a multidimensional array with the
     * names of the fixture files per context
     *
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    protected function getFixtureFiles()
    {
        return [
            'Chamilo\Core\Repository\ContentObject\LearningPath' => [
                'TreeNodeAttempt', 'TreeNodeQuestionAttempt', 'User'
            ]
        ];
    }

    public function testClearTreeNodeAttemptCache()
    {
        $this->trackingRepository->clearTreeNodeAttemptCache();
        $this->assertTrue(true);
    }

    public function testFindTreeNodeAttempts()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $user = new User();
        $user->setId(2);

        $this->assertCount(11, $this->trackingRepository->findTreeNodeAttempts($learningPath, $user));
    }

    public function testFindTreeNodeAttemptsForLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertCount(16, $this->trackingRepository->findTreeNodeAttemptsForLearningPath($learningPath));
    }

    public function testFindActiveTreeNodeAttempt()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $lpTreeNodeData = new TreeNodeData();
        $lpTreeNodeData->setId(1);

        $user = new User();
        $user->setId(2);

        $tree = new Tree();
        $treeNode = new TreeNode($tree, $learningPath, $lpTreeNodeData);

        $this->assertEquals(
            16, $this->trackingRepository->findActiveTreeNodeAttempt($learningPath, $treeNode, $user)->getId()
        );
    }

    public function testFindTreeNodeAttemptById()
    {
        $this->assertEquals(16, $this->trackingRepository->findTreeNodeAttemptById(16)->getId());
    }

    public function testFindTreeNodeQuestionAttempts()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt->setId(1);

        $this->assertCount(10, $this->trackingRepository->findTreeNodeQuestionAttempts($treeNodeAttempt));
    }

    public function testFindLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertCount(
            10,
            $this->trackingRepository->findLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts(
                $learningPath
            )
        );
    }

    public function testFindLearningPathAttemptsWithUser()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $result = $this->trackingRepository->findLearningPathAttemptsWithUser($learningPath, [1]);
        $this->assertEquals(1, $result[0]['nodes_completed']);
    }

    public function testFindLearningPathAttemptsWithUserWithMultipleNodes()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $result = $this->trackingRepository->findLearningPathAttemptsWithUser($learningPath, [1, 2, 3]);
        $this->assertEquals(2, $result[0]['nodes_completed']);
    }

    public function testCountLearningPathAttemptsWithUser()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertEquals(2, $this->trackingRepository->countLearningPathAttemptsWithUser($learningPath, [1, 3]));
    }

    public function testFindTargetUsersWithLearningPathAttempts()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->dataManagerWrapperMock->expects($this->once())
            ->method('getPublicationTargetUserIds')
            ->will($this->returnValue([2]));

        $this->assertCount(1, $this->trackingRepository->findTargetUsersWithLearningPathAttempts($learningPath, [1]));
    }

    public function testFindTargetUsersWithLearningPathAttemptsWithCondition()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->dataManagerWrapperMock->expects($this->once())
            ->method('getPublicationTargetUserIds')
            ->will($this->returnValue([2, 3]));

        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
            new StaticConditionVariable('Jamey')
        );

        $this->assertCount(
            1, $this->trackingRepository->findTargetUsersWithLearningPathAttempts($learningPath, [1], $condition)
        );
    }

    public function testCountTargetUsersForLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->dataManagerWrapperMock->expects($this->once())
            ->method('getPublicationTargetUserIds')
            ->will($this->returnValue([2]));

        $this->assertEquals(1, $this->trackingRepository->countTargetUsersForLearningPath($learningPath));
    }

    public function testCountTargetUsersForLearningPathWithCondition()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->dataManagerWrapperMock->expects($this->once())
            ->method('getPublicationTargetUserIds')
            ->will($this->returnValue([2, 3]));

        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
            new StaticConditionVariable('Jamey')
        );

        $this->assertEquals(1, $this->trackingRepository->countTargetUsersForLearningPath($learningPath, $condition));
    }
}


