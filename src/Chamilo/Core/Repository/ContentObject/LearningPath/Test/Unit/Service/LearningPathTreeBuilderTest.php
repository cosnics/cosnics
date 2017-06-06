<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the TreeBuilder class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeBuilderTest extends Test
{
    /**
     * @var TreeNodeDataRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $treeNodeDataRepositoryMock;

    /**
     * @var ContentObjectRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentObjectRepositoryMock;

    /**
     * @var TreeBuilder
     */
    protected $treeBuilder;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $this->treeNodeDataRepositoryMock = $this->getMockBuilder(TreeNodeDataRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contentObjectRepositoryMock = $this->getMockBuilder(ContentObjectRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->treeBuilder = new TreeBuilder(
            $this->treeNodeDataRepositoryMock, $this->contentObjectRepositoryMock
        );
    }

    /**
     * Teardown after each test
     */
    protected function tearDown()
    {
        unset($this->treeNodeDataRepositoryMock);
        unset($this->contentObjectRepositoryMock);
        unset($this->treeBuilder);
    }

    /**
     * Tests the BuilderTree function
     */
    public function testBuildTree()
    {
        $rootLearningPath = new LearningPath();
        $page = new Page();

        $pageChild = new TreeNodeData();
        $pageChild->setContentObjectId(5);

        $this->treeNodeDataRepositoryMock->expects($this->exactly(1))
            ->method('retrieveTreeNodesDataForLearningPath')
            ->withConsecutive($rootLearningPath)
            ->willReturnOnConsecutiveCalls(array($pageChild));

        $this->contentObjectRepositoryMock->expects($this->exactly(1))
            ->method('findById')
            ->withConsecutive(array(5))
            ->willReturnOnConsecutiveCalls($page);

        $tree = $this->treeBuilder->buildTree($rootLearningPath);

        $this->assertEquals(2, count($tree->getTreeNodes()));
    }

    /**
     * Tests the BuilderTree function with sub learning paths
     */
    public function testBuilderTreeWithSubLearningPaths()
    {
        $rootLearningPath = new LearningPath();
        $subLearningPath = new LearningPath();
        $page = new Page();

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setContentObjectId(10);

        $pageChild = new TreeNodeData();
        $pageChild->setContentObjectId(5);

        $this->treeNodeDataRepositoryMock->expects($this->exactly(2))
            ->method('retrieveTreeNodesDataForLearningPath')
            ->withConsecutive($rootLearningPath, $subLearningPath)
            ->willReturnOnConsecutiveCalls(array($treeNodeData), array($pageChild));

        $this->contentObjectRepositoryMock->expects($this->exactly(2))
            ->method('findById')
            ->withConsecutive(10, 5)
            ->willReturnOnConsecutiveCalls($subLearningPath, $page);

        $tree = $this->treeBuilder->buildTree($rootLearningPath);

        $this->assertEquals(3, count($tree->getTreeNodes()));
    }

}