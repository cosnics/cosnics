<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathChildRepository;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the LearningPathChildService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChildServiceTest extends Test
{
    /**
     * @var LearningPathChildRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $learningPathChildRepositoryMock;

    /**
     * The subject under test
     *
     * @var LearningPathChildService
     */
    protected $learningPathChildService;

    /**
     * Set up before each test
     */
    protected function setUp()
    {
        $this->learningPathChildRepositoryMock = $this->getMockBuilder(LearningPathChildRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->learningPathChildService =
            new LearningPathChildService($this->learningPathChildRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->learningPathChildRepositoryMock);
        unset($this->learningPathChildService);
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

        $this->learningPathChildService->addContentObjectToLearningPath($treeNode, $page);
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

        $this->learningPathChildService->addContentObjectToLearningPath($treeNode, $page);
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

        $this->learningPathChildService->addContentObjectToLearningPath($treeNode, $page);
    }

    /**
     * Mocks the canContentObjectBeAdded function of the LearningPathChildValidator service
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
     * Mocks the create function of the LearningPathChildRepository service
     *
     * @param bool $returnValue
     */
    protected function mockCreate($returnValue = true)
    {
        $this->learningPathChildRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($returnValue));
    }
}