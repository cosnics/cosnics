<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Integration\Display\Preview\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\Fixtures\ChamiloFixtureLoader;
use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Tests the TrackingRepository
 *
 * @group  preview
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingRepositoryTest extends DependencyInjectionBasedTestCase
{

    /**
     * @var TrackingRepository
     */
    protected $trackingRepository;

    /**
     * Inserts the fixture data for the tests in the created storage units
     */
    protected function createSessionFixtures()
    {
        $chamiloFixtureLoader = new ChamiloFixtureLoader();

        /**
         * @var \Chamilo\Libraries\File\SystemPathBuilder $systemPathBuilder
         */
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $basePath = $systemPathBuilder->namespaceToFullPath(
                'Chamilo\Core\Repository\ContentObject\LearningPath\Test'
            ) . 'Fixtures/';

        $data = [];

        $dummyTreeNodeAttemptsData = [];
        $dummyTreeNodeAttempts = $chamiloFixtureLoader->loadFile($basePath . 'DummyTreeNodeAttempt.yml');
        foreach ($dummyTreeNodeAttempts->getObjects() as $dummyTreeNodeAttempt)
        {
            /** @var DummyTreeNodeAttempt $dummyTreeNodeAttempt */

            $dummyTreeNodeAttemptsData[$dummyTreeNodeAttempt->getLearningPathId()][$dummyTreeNodeAttempt->getUserId(
            )][$dummyTreeNodeAttempt->getId()] = $dummyTreeNodeAttempt;
        }

        $data[DummyTreeNodeAttempt::class] = $dummyTreeNodeAttemptsData;

        $dummyQuestionAttemptsData = [];
        $dummyQuestionAttempts = $chamiloFixtureLoader->loadFile($basePath . 'DummyQuestionAttempt.yml');
        foreach ($dummyQuestionAttempts->getObjects() as $dummyQuestionAttempt)
        {
            /** @var DummyQuestionAttempt $dummyQuestionAttempt */

            $dummyQuestionAttemptsData[$dummyQuestionAttempt->getTreeNodeAttemptId()][$dummyQuestionAttempt->getId()] =
                $dummyQuestionAttempt;
        }

        $data[DummyQuestionAttempt::class] = $dummyQuestionAttemptsData;

        $this->getSession()->set(
            'Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository', serialize($data)
        );
    }

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->trackingRepository = new TrackingRepository();
        $this->createSessionFixtures();
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->trackingRepository);
        $this->getSession()->clear();
    }

    public function testClearTreeNodeAttemptCache()
    {
        $this->trackingRepository->clearTreeNodeAttemptCache();
        $this->assertTrue(true);
    }

    public function testCountLearningPathAttemptsWithUser()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertEmpty($this->trackingRepository->countLearningPathAttemptsWithUser($learningPath));
    }

    public function testCountTargetUsersForLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertEmpty($this->trackingRepository->countTargetUsersForLearningPath($learningPath));
    }

    public function testCreateWithOtherDataClass()
    {
        $this->assertFalse($this->trackingRepository->create(new LearningPath()));
    }

    public function testCreateWithQuestionAttempt()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $questionAttempt = new DummyQuestionAttempt();
        $questionAttempt->setTreeNodeAttemptId(1);

        $this->trackingRepository->create($questionAttempt);

        $treeNodeAttempt = new DummyTreeNodeAttempt();
        $treeNodeAttempt->setId(1);

        $this->assertCount(11, $this->trackingRepository->findTreeNodeQuestionAttempts($treeNodeAttempt));
    }

    public function testCreateWithTreeNodeAttempt()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeAttempt = new DummyTreeNodeAttempt();
        $treeNodeAttempt->setTreeNodeDataId(1);
        $treeNodeAttempt->setLearningPathId(1);
        $treeNodeAttempt->setUserId(2);

        $this->trackingRepository->create($treeNodeAttempt);

        $this->assertCount(17, $this->trackingRepository->findTreeNodeAttemptsForLearningPath($learningPath));
    }

    public function testDeleteWithOtherDataClass()
    {
        $this->assertFalse($this->trackingRepository->delete(new LearningPath()));
    }

    public function testDeleteWithQuestionAttempt()
    {
        $questionAttempt = new DummyQuestionAttempt();
        $questionAttempt->setTreeNodeAttemptId(1);
        $questionAttempt->setId(1);

        $treeNodeAttempt = new DummyTreeNodeAttempt();
        $treeNodeAttempt->setId(1);

        $this->trackingRepository->delete($questionAttempt);

        $this->assertCount(9, $this->trackingRepository->findTreeNodeQuestionAttempts($treeNodeAttempt));
    }

    public function testDeleteWithTreeNodeAttempt()
    {
        $treeNodeAttempt = new DummyTreeNodeAttempt();
        $treeNodeAttempt->setId(1);
        $treeNodeAttempt->setTreeNodeDataId(1);
        $treeNodeAttempt->setLearningPathId(1);
        $treeNodeAttempt->setUserId(2);

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->trackingRepository->delete($treeNodeAttempt);

        $this->assertCount(15, $this->trackingRepository->findTreeNodeAttemptsForLearningPath($learningPath));
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

    public function testFindActiveTreeNodeAttemptWithoutActiveAttempt()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $lpTreeNodeData = new TreeNodeData();
        $lpTreeNodeData->setId(2);

        $user = new User();
        $user->setId(2);

        $tree = new Tree();
        $treeNode = new TreeNode($tree, $learningPath, $lpTreeNodeData);

        $this->assertEmpty($this->trackingRepository->findActiveTreeNodeAttempt($learningPath, $treeNode, $user));
    }

    public function testFindLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertCount(
            10, $this->trackingRepository->findLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts(
            $learningPath
        )
        );
    }

    public function testFindLearningPathAttemptsWithUser()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertEmpty($this->trackingRepository->findLearningPathAttemptsWithUser($learningPath, [1]));
    }

    public function testFindTargetUsersWithLearningPathAttempts()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertEmpty($this->trackingRepository->findTargetUsersWithLearningPathAttempts($learningPath, [1]));
    }

    public function testFindTreeNodeAttemptById()
    {
        $this->assertEquals(16, $this->trackingRepository->findTreeNodeAttemptById(16)->getId());
    }

    public function testFindTreeNodeAttemptByIdWithNonExistingId()
    {
        $this->assertEmpty($this->trackingRepository->findTreeNodeAttemptById(999));
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

    public function testFindTreeNodeQuestionAttempts()
    {
        $treeNodeAttempt = new DummyTreeNodeAttempt();
        $treeNodeAttempt->setId(1);

        $this->assertCount(10, $this->trackingRepository->findTreeNodeQuestionAttempts($treeNodeAttempt));
    }

    public function testResetStorage()
    {
        $this->trackingRepository->resetStorage();
        $this->assertEmpty(
            unserialize(
                $this->getSession()->get(
                    'Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository'
                )
            )
        );
    }

    public function testUpdateWithOtherDataClass()
    {
        $this->assertFalse($this->trackingRepository->update(new LearningPath()));
    }

    public function testUpdateWithQuestionAttempt()
    {
        $questionAttempt = new DummyQuestionAttempt();
        $questionAttempt->setTreeNodeAttemptId(1);
        $questionAttempt->setId(20);

        $treeNodeAttempt = new DummyTreeNodeAttempt();
        $treeNodeAttempt->setId(1);

        $this->trackingRepository->update($questionAttempt);

        $this->assertEquals(
            $questionAttempt,
            $this->trackingRepository->findTreeNodeQuestionAttempts($treeNodeAttempt)[$questionAttempt->getId()]
        );
    }

    public function testUpdateWithTreeNodeAttempt()
    {
        $treeNodeAttempt = new DummyTreeNodeAttempt();
        $treeNodeAttempt->setId(1);
        $treeNodeAttempt->setTreeNodeDataId(1);
        $treeNodeAttempt->setLearningPathId(1);
        $treeNodeAttempt->setUserId(2);

        $this->trackingRepository->update($treeNodeAttempt);

        $this->assertEquals($treeNodeAttempt, $this->trackingRepository->findTreeNodeAttemptById(1));
    }
}

