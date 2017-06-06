<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the TreeNodeDataService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeDataServiceTest extends Test
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
    protected function setUp()
    {
        $this->treeNodeDataRepositoryMock = $this->getMockBuilder(TreeNodeDataRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->treeNodeDataService =
            new TreeNodeDataService($this->treeNodeDataRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->treeNodeDataRepositoryMock);
        unset($this->treeNodeDataService);
    }

    public function testAddContentObjectToLearningPath()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(5);

        $page = new Page();
        $page->setId(15);

        $tree = new Tree();
        $treeNode = new TreeNode($tree, $learningPath);

        $this->mockCanContentObjectBeAdded($treeNode, $page, true);
        $this->mockCreate(true);

        $this->treeNodeDataService->addContentObjectToLearningPath($treeNode, $page);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddContentObjectToLearningPathNotAllowed()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(5);

        $page = new Page();
        $page->setId(15);

        $tree = new Tree();
        $treeNode = new TreeNode($tree, $learningPath);

        $this->mockCanContentObjectBeAdded($treeNode, $page, false);

        $this->treeNodeDataService->addContentObjectToLearningPath($treeNode, $page);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddContentObjectToLearningPathCreateFailed()
    {
        $learningPath = new LearningPath();
        $learningPath->setId(5);

        $page = new Page();
        $page->setId(15);

        $tree = new Tree();
        $treeNode = new TreeNode($tree, $learningPath);

        $this->mockCanContentObjectBeAdded($treeNode, $page, true);
        $this->mockCreate(false);

        $this->treeNodeDataService->addContentObjectToLearningPath($treeNode, $page);
    }

    /**
     * Mocks the canContentObjectBeAdded function of the TreeNodeDataValidator service
     *
     * @param TreeNode $parentTreeNode
     * @param ContentObject $childContentObject
     * @param bool $returnValue
     */
    protected function mockCanContentObjectBeAdded(
        TreeNode $parentTreeNode, ContentObject $childContentObject, $returnValue = true
    )
    {
    }

    /**
     * Mocks the create function of the TreeNodeDataRepository service
     *
     * @param bool $returnValue
     */
    protected function mockCreate($returnValue = true)
    {
        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($returnValue));
    }
}