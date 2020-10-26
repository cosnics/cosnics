<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

/**
 * Test the LearningPathService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathServiceTest extends ChamiloTestCase
{

    /**
     *
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     *
     * @var ContentObjectRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentObjectRepositoryMock;

    /**
     *
     * @var TreeBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $treeBuilderMock;

    /**
     *
     * @var TreeNodeDataService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $treeNodeDataServiceMock;

    /**
     *
     * @return TreeNodeData
     */
    protected function addContentObjectToLearningPath()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $page = new Page();
        $page->setId(2);

        $user = new User();
        $user->setId(10);

        $this->treeNodeDataServiceMock->expects($this->once())->method('createTreeNodeData');

        return $this->learningPathService->addContentObjectToLearningPath($learningPath, $treeNode, $page, $user);
    }

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $this->contentObjectRepositoryMock =
            $this->getMockBuilder(ContentObjectRepository::class)->disableOriginalConstructor()->getMock();

        $this->treeBuilderMock = $this->getMockBuilder(TreeBuilder::class)->disableOriginalConstructor()->getMock();

        $this->treeNodeDataServiceMock =
            $this->getMockBuilder(TreeNodeDataService::class)->disableOriginalConstructor()->getMock();

        $this->learningPathService = new LearningPathService(
            $this->contentObjectRepositoryMock, $this->treeBuilderMock, $this->treeNodeDataServiceMock
        );
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->learningPathService);
        unset($this->treeNodeDataServiceMock);
        unset($this->treeBuilderMock);
        unset($this->contentObjectRepositoryMock);
    }

    public function testAddContentObjectToLearningPath()
    {
        $this->addContentObjectToLearningPath();
    }

    public function testAddContentObjectToLearningPathSetsContentObjectId()
    {
        $treeNodeData = $this->addContentObjectToLearningPath();
        $this->assertEquals(2, $treeNodeData->getContentObjectId());
    }

    public function testAddContentObjectToLearningPathSetsLearningPathId()
    {
        $treeNodeData = $this->addContentObjectToLearningPath();
        $this->assertEquals(1, $treeNodeData->getLearningPathId());
    }

    public function testAddContentObjectToLearningPathSetsParentTreeNodeDataId()
    {
        $treeNodeData = $this->addContentObjectToLearningPath();
        $this->assertEquals(5, $treeNodeData->getParentTreeNodeDataId());
    }

    public function testAddContentObjectToLearningPathSetsUserId()
    {
        $treeNodeData = $this->addContentObjectToLearningPath();
        $this->assertEquals(10, $treeNodeData->getUserId());
    }

    public function testBuildTree()
    {
        $learningPath = new LearningPath();
        $tree = new Tree();

        $this->treeBuilderMock->expects($this->once())->method('buildTree')->with($learningPath)->will(
            $this->returnValue($tree)
        );

        $this->assertEquals($tree, $this->learningPathService->buildTree($learningPath));
    }

    public function testCreateAndAddContentObjectToLearningPath()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $user = new User();
        $user->setId(10);

        $contentObjectType = Page::class;

        $this->contentObjectRepositoryMock->expects($this->once())->method('create')->with(
            $this->callback(
                function (ContentObject $contentObject) {
                    $contentObject->setId(5);

                    return $contentObject->get_title() == 'test' && $contentObject->get_owner_id() == 10;
                }
            )
        )->will($this->returnValue(true));

        $this->treeNodeDataServiceMock->expects($this->once())->method('createTreeNodeData');

        $this->learningPathService->createAndAddContentObjectToLearningPath(
            $contentObjectType, $learningPath, $treeNode, $user, 'test'
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateAndAddContentObjectToLearningPathWhenCreateFails()
    {
        $tree = new Tree();
        $learningPath = new LearningPath();
        $treeNode = new TreeNode($tree, $learningPath);
        $user = new User();

        $this->learningPathService->createAndAddContentObjectToLearningPath(
            Page::class, $learningPath, $treeNode, $user, 'test'
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateAndAddContentObjectToLearningPathWithInvalidContentObjectType()
    {
        $tree = new Tree();
        $learningPath = new LearningPath();
        $treeNode = new TreeNode($tree, $learningPath);
        $user = new User();

        $this->learningPathService->createAndAddContentObjectToLearningPath(
            self::class, $learningPath, $treeNode, $user, 'test'
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateAndAddContentObjectToLearningPathWithNonExistingContentObjectType()
    {
        $tree = new Tree();
        $learningPath = new LearningPath();
        $treeNode = new TreeNode($tree, $learningPath);
        $user = new User();

        $this->learningPathService->createAndAddContentObjectToLearningPath(
            'test', $learningPath, $treeNode, $user, 'test'
        );
    }

    public function testDeleteContentObjectFromLearningPath()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $this->treeNodeDataServiceMock->expects($this->once())->method('deleteTreeNodeData')->with($treeNodeData);

        $this->learningPathService->deleteContentObjectFromLearningPath($treeNode);
    }

    public function testDeleteContentObjectFromLearningPathWithChildNodes()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $page = new Page();
        $page->setId(3);

        $pageTreeNodeData = new TreeNodeData();
        $pageTreeNodeData->setId(8);

        $pageTreeNode = new TreeNode($tree, $page, $pageTreeNodeData);

        $treeNode->addChildNode($pageTreeNode);

        $this->treeNodeDataServiceMock->expects($this->exactly(2))->method('deleteTreeNodeData')->withConsecutive(
            [$treeNodeData], [$pageTreeNodeData]
        );

        $this->learningPathService->deleteContentObjectFromLearningPath($treeNode);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteContentObjectWithInvalidTreeNodeData()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNode = new TreeNode($tree, $learningPath);

        $this->learningPathService->deleteContentObjectFromLearningPath($treeNode);
    }

    public function testEmptyLearningPath()
    {
        $learningPath = new LearningPath();

        $this->treeNodeDataServiceMock->expects($this->once())->method('deleteTreeNodesFromLearningPath')->with(
            $learningPath
        );

        $this->learningPathService->emptyLearningPath($learningPath);
    }

    public function testGetLearningPaths()
    {
        $learningPath = new LearningPath();
        $resultSet = new DataClassIterator(LearningPath::class, [$learningPath]);

        $this->contentObjectRepositoryMock->expects($this->once())->method('findAll')->with(LearningPath::class)->will(
            $this->returnValue($resultSet)
        );

        $this->assertEquals([$learningPath], $this->learningPathService->getLearningPaths());
    }

    public function testGetTree()
    {
        $learningPath = new LearningPath();
        $tree = new Tree();

        $this->treeBuilderMock->expects($this->once())->method('buildTree')->with($learningPath)->will(
            $this->returnValue($tree)
        );

        $this->assertEquals($tree, $this->learningPathService->getTree($learningPath));
    }

    public function testGetTreeCachesRequests()
    {
        $learningPath = new LearningPath();
        $tree = new Tree();

        $this->treeBuilderMock->expects($this->once())->method('buildTree')->with($learningPath)->will(
            $this->returnValue($tree)
        );

        $this->assertEquals(
            $this->learningPathService->getTree($learningPath), $this->learningPathService->getTree($learningPath)
        );
    }

    public function testIsLearningPathEmpty()
    {
        $learningPath = new LearningPath();

        $this->treeNodeDataServiceMock->expects($this->once())->method('countTreeNodesDataForLearningPath')->will(
            $this->returnValue(0)
        );

        $this->assertTrue($this->learningPathService->isLearningPathEmpty($learningPath));
    }

    public function testIsLearningPathEmptyWhenNot()
    {
        $learningPath = new LearningPath();

        $this->treeNodeDataServiceMock->expects($this->once())->method('countTreeNodesDataForLearningPath')->will(
            $this->returnValue(5)
        );

        $this->assertFalse($this->learningPathService->isLearningPathEmpty($learningPath));
    }

    public function testMoveContentObjectToNewParent()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $page = new Page();
        $page->setId(10);

        $pageTreeNodeData = new TreeNodeData();
        $pageTreeNodeData->setId(8);

        $pageTreeNode = new TreeNode($tree, $page, $pageTreeNodeData);

        $this->treeNodeDataServiceMock->expects($this->once())->method('updateTreeNodeData')->with(
            $this->callback(
                function (TreeNodeData $treeNodeData) {
                    return $treeNodeData->getParentTreeNodeDataId() == 5 && $treeNodeData->getDisplayOrder() == 3;
                }
            )
        );

        $this->learningPathService->moveContentObjectToNewParent($pageTreeNode, $treeNode, 3);
    }

    public function testToggleContentObjectBlockedStatus()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $this->treeNodeDataServiceMock->expects($this->once())->method('updateTreeNodeData')->with(
            $this->callback(
                function (TreeNodeData $treeNodeData) {
                    return $treeNodeData->isBlocked();
                }
            )
        );

        $this->learningPathService->toggleContentObjectBlockedStatus($treeNode);
    }

    public function testToggleContentObjectBlockedStatusWhenBlocked()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);
        $treeNodeData->setBlocked(true);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $this->treeNodeDataServiceMock->expects($this->once())->method('updateTreeNodeData')->with(
            $this->callback(
                function (TreeNodeData $treeNodeData) {
                    return !$treeNodeData->isBlocked();
                }
            )
        );

        $this->learningPathService->toggleContentObjectBlockedStatus($treeNode);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testToggleContentObjectBlockedStatusWithNoTreeNodeData()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNode = new TreeNode($tree, $learningPath);

        $this->learningPathService->toggleContentObjectBlockedStatus($treeNode);
    }

    public function testUpdateContentObjectInTreeNode()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(5);

        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);

        $page = new Page();
        $page->setId(5);

        $this->treeNodeDataServiceMock->expects($this->once())->method('updateTreeNodeData')->with(
            $this->callback(
                function (TreeNodeData $treeNodeData) {
                    return $treeNodeData->getContentObjectId() == 5;
                }
            )
        );

        $this->learningPathService->updateContentObjectInTreeNode($treeNode, $page);
    }

    public function testUpdateContentObjectTitle()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNode = new TreeNode($tree, $learningPath);

        $this->contentObjectRepositoryMock->expects($this->once())->method('update')->with(
            $this->callback(
                function (LearningPath $learningPath) {
                    return $learningPath->getId() == 1 && $learningPath->get_title() == 'New Title';
                }
            )
        )->will($this->returnValue(true));

        $this->learningPathService->updateContentObjectTitle($treeNode, 'New Title');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUpdateContentObjectTitleWhenUpdateFails()
    {
        $tree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(1);

        $treeNode = new TreeNode($tree, $learningPath);

        $this->contentObjectRepositoryMock->expects($this->once())->method('update')->will($this->returnValue(false));

        $this->learningPathService->updateContentObjectTitle($treeNode, 'New Title');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateContentObjectTitleWithEmptyTitle()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree);

        $this->learningPathService->updateContentObjectTitle($treeNode, '');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateContentObjectTitleWithInvalidTitle()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree);

        $this->learningPathService->updateContentObjectTitle($treeNode, false);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUpdateContentObjectTitleWithoutContentObject()
    {
        $tree = new Tree();
        $treeNode = new TreeNode($tree);

        $this->learningPathService->updateContentObjectTitle($treeNode, 'New Title');
    }
}
