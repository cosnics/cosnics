<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the TreeNodeDataService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeDataServiceTest extends ChamiloTestCase
{
    /**
     * @var TreeNodeDataRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $treeNodeDataRepositoryMock;

    /**
     * The subject under test
     *
     * @var TreeNodeDataService
     */
    protected $treeNodeDataService;

    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        $this->treeNodeDataRepositoryMock = $this->getMockBuilder(TreeNodeDataRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->treeNodeDataService = new TreeNodeDataService($this->treeNodeDataRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void
    {
        unset($this->treeNodeDataRepositoryMock);
        unset($this->treeNodeDataService);
    }

    public function testGetTreeNodesDataForLearningPath()
    {
        $learningPath = new LearningPath();
        $treeNodesData = array(new TreeNodeData());

        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('findTreeNodesDataForLearningPath')
            ->with($learningPath)
            ->will($this->returnValue($treeNodesData));

        $this->treeNodeDataService->getTreeNodesDataForLearningPath($learningPath);
    }

    public function testGetTreeNodesDataByContentObjects()
    {
        $contentObjectIds = array(5, 10);
        $treeNodesData = array(new TreeNodeData());

        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('findTreeNodesDataByContentObjects')
            ->with($contentObjectIds)
            ->will($this->returnValue($treeNodesData));

        $this->treeNodeDataService->getTreeNodesDataByContentObjects($contentObjectIds);
    }

    public function testGetTreeNodesDataByUserId()
    {
        $userId = 2;
        $treeNodesData = array(new TreeNodeData());

        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('findTreeNodesDataByUserId')
            ->with($userId)
            ->will($this->returnValue($treeNodesData));

        $this->treeNodeDataService->getTreeNodesDataByUserId(2);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTreeNodesDataWithInvalidUserId()
    {
        $this->treeNodeDataService->getTreeNodesDataByUserId('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTreeNodesDataWithUserId0()
    {
        $this->treeNodeDataService->getTreeNodesDataByUserId(0);
    }

    public function testGetTreeNodeDataById()
    {
        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(4);

        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('findTreeNodeData')
            ->with(4)
            ->will($this->returnValue($treeNodeData));

        $this->treeNodeDataService->getTreeNodeDataById(4);
    }

    /**
     * @expectedException \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    public function testGetTreeNodeDataByIdWithTreeNodeNotFound()
    {
        $this->treeNodeDataService->getTreeNodeDataById(4);
    }

    public function testCreateTreeNodeDataForLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(2);
        $user = new User();
        $user->setId(3);

        $this->mockCreate();

        $treeNodeData = $this->treeNodeDataService->createTreeNodeDataForLearningPath($learningPath, $user);
        $this->assertInstanceOf(TreeNodeData::class, $treeNodeData);
    }

    public function testCreateTreeNodeDataForLearningPathCorrectLearningPathId()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(2);
        $user = new User();
        $user->setId(3);

        $this->mockCreate();

        $treeNodeData = $this->treeNodeDataService->createTreeNodeDataForLearningPath($learningPath, $user);
        $this->assertEquals(2, $treeNodeData->getLearningPathId());
    }

    public function testCreateTreeNodeDataForLearningPathCorrectContentObjectId()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(2);
        $user = new User();
        $user->setId(3);

        $this->mockCreate();

        $treeNodeData = $this->treeNodeDataService->createTreeNodeDataForLearningPath($learningPath, $user);
        $this->assertEquals(2, $treeNodeData->getContentObjectId());
    }

    public function testCreateTreeNodeDataForLearningPathCorrectUserId()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(2);
        $user = new User();
        $user->setId(3);

        $this->mockCreate();

        $treeNodeData = $this->treeNodeDataService->createTreeNodeDataForLearningPath($learningPath, $user);
        $this->assertEquals(3, $treeNodeData->getUserId());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateThrowsExceptionWhenFailed()
    {
        $treeNodeData = new TreeNodeData();
        $this->mockCreate(false);
        $this->treeNodeDataService->createTreeNodeData($treeNodeData);
    }

    public function testUpdate()
    {
        $treeNodeData = new TreeNodeData();
        $this->mockUpdate();
        $this->treeNodeDataService->updateTreeNodeData($treeNodeData);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUpdateThrowsExceptionWhenFailed()
    {
        $treeNodeData = new TreeNodeData();
        $this->mockUpdate(false);
        $this->treeNodeDataService->updateTreeNodeData($treeNodeData);
    }

    public function testDelete()
    {
        $treeNodeData = new TreeNodeData();
        $this->mockDelete();
        $this->treeNodeDataService->deleteTreeNodeData($treeNodeData);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDeleteThrowsExceptionWhenFailed()
    {
        $treeNodeData = new TreeNodeData();
        $this->mockDelete(false);
        $this->treeNodeDataService->deleteTreeNodeData($treeNodeData);
    }

    public function testCountTreeNodesDataForLearningPath()
    {
        $learningPath = new LearningPath();
        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('countTreeNodesDataForLearningPath')
            ->with($learningPath)
            ->will($this->returnValue(5));

        $this->assertEquals(5, $this->treeNodeDataService->countTreeNodesDataForLearningPath($learningPath));
    }

    public function testDeleteTreeNodesFromLearningPath()
    {
        $learningPath = new LearningPath();

        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('deleteTreeNodesFromLearningPath')
            ->with($learningPath)
            ->will($this->returnValue(true));

        $this->treeNodeDataService->deleteTreeNodesFromLearningPath($learningPath);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDeleteTreeNodesFromLearningPathThrowsExceptionWhenFailed()
    {
        $learningPath = new LearningPath();
        $this->treeNodeDataService->deleteTreeNodesFromLearningPath($learningPath);
    }

    public function testDeleteTreeNodeDataForLearningPath()
    {
        $learningPath = new LearningPath();

        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('deleteTreeNodeDataForLearningPath')
            ->with($learningPath)
            ->will($this->returnValue(true));

        $this->treeNodeDataService->deleteTreeNodeDataForLearningPath($learningPath);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDeleteTreeNodeDataForLearningPathThrowsExceptionWhenFailed()
    {
        $learningPath = new LearningPath();
        $this->treeNodeDataService->deleteTreeNodeDataForLearningPath($learningPath);
    }

    protected function mockCreate($returnValue = true)
    {
        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($returnValue));
    }

    protected function mockDelete($returnValue = true)
    {
        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($returnValue));
    }

    protected function mockUpdate($returnValue = true)
    {
        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('update')
            ->will($this->returnValue($returnValue));
    }
}