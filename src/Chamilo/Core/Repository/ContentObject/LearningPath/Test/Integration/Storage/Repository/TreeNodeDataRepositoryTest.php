<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Integration\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloFixturesBasedTestCase;

/**
 * Tests the TreeNodeDataRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeDataRepositoryTest extends ChamiloFixturesBasedTestCase
{

    /**
     *
     * @var TreeNodeDataRepository
     */
    protected $treeNodeDataRepository;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        parent::setUp();

        $this->treeNodeDataRepository = new TreeNodeDataRepository($this->getTestDataClassRepository());
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        parent::tearDown();

        unset($this->treeNodeDataRepository);
    }

    /**
     * Returns the storage units that need to be created.
     * This method requires a multidimensional array with the
     * names of the storage units per context
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    protected function getStorageUnitsToCreate()
    {
        return ['Chamilo\Core\Repository\ContentObject\LearningPath' => ['tree_node_data']];
    }

    /**
     * Returns the fixture files that need to be inserted.
     * This method requires a multidimensional array with the
     * names of the fixture files per context
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    protected function getFixtureFiles()
    {
        return ['Chamilo\Core\Repository\ContentObject\LearningPath' => ['TreeNodeData']];
    }

    public function testFindTreeNodesDataForLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertCount(9, $this->treeNodeDataRepository->findTreeNodesDataForLearningPath($learningPath));
    }

    public function testCountTreeNodesDataForLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->assertEquals(9, $this->treeNodeDataRepository->countTreeNodesDataForLearningPath($learningPath));
    }

    public function testFindTreeNodesDataByContentObjects()
    {
        $this->assertCount(1, $this->treeNodeDataRepository->findTreeNodesDataByContentObjects([4]));
    }

    public function testFindTreeNodesDataByUserId()
    {
        $this->assertCount(5, $this->treeNodeDataRepository->findTreeNodesDataByUserId(3));
    }

    public function testFindTreeNodeData()
    {
        $this->assertEquals(6, $this->treeNodeDataRepository->findTreeNodeData(6)->getId());
    }

    public function testClearTreeNodesDataCache()
    {
        $this->treeNodeDataRepository->clearTreeNodesDataCache();
        $this->assertTrue(true);
    }

    public function testDeleteTreeNodesFromLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->treeNodeDataRepository->deleteTreeNodesFromLearningPath($learningPath);
        $this->assertEquals(0, $this->treeNodeDataRepository->countTreeNodesDataForLearningPath($learningPath));
    }

    public function testDeleteTreeNodeDataForLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $this->treeNodeDataRepository->deleteTreeNodeDataForLearningPath($learningPath);
        $this->assertEquals(8, $this->treeNodeDataRepository->countTreeNodesDataForLearningPath($learningPath));
    }
}

