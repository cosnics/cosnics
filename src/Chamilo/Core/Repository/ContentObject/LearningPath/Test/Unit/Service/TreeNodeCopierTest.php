<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeCopier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\TreeTestDataGenerator;
use Chamilo\Core\Repository\Service\ContentObjectCopierWrapper;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the TreeNodeCopier class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeCopierTest extends ChamiloTestCase
{

    /**
     *
     * @var TreeNodeCopier
     */
    protected $treeNodeCopier;

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
     * @var ContentObjectCopierWrapper | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentObjectCopierWrapper;

    /**
     *
     * @var Tree
     */
    protected $tree;

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
     * Setup before each test
     * - Learning Path A - ID: 1
     * - Section A - ID: 2
     * - Page 1 - ID: 6
     * - Section B - ID: 3
     * - Section C - ID: 4
     * - Section D - ID: 5
     * - Page 2 - ID: 7
     */
    protected function setUp()
    {
        $treeTestDataGenerator = new TreeTestDataGenerator();

        $this->tree = $treeTestDataGenerator->getTree();
        $this->contentObjects = $treeTestDataGenerator->getContentObjects();
        $this->treeNodesData = $treeTestDataGenerator->getTreeNodesData();
        $this->treeNodes = $treeTestDataGenerator->getTreeNodes();

        $this->contentObjectRepositoryMock = $this->getMockBuilder(ContentObjectRepository::class)->disableOriginalConstructor()->getMock();

        $this->treeBuilderMock = $this->getMockBuilder(TreeBuilder::class)->disableOriginalConstructor()->getMock();

        $this->treeNodeDataServiceMock = $this->getMockBuilder(TreeNodeDataService::class)->disableOriginalConstructor()->getMock();

        $this->contentObjectCopierWrapper = $this->getMockBuilder(ContentObjectCopierWrapper::class)->disableOriginalConstructor()->getMock();

        $this->treeNodeCopier = new TreeNodeCopier(
            $this->contentObjectRepositoryMock,
            $this->treeBuilderMock,
            $this->treeNodeDataServiceMock,
            $this->contentObjectCopierWrapper);

        $this->treeBuilderMock->expects($this->once())->method('buildTree')->with($this->contentObjects[1])->will(
            $this->returnValue($this->tree));

        $this->treeNodeDataServiceMock->expects($this->exactly(7))->method('createTreeNodeData');

        $this->contentObjectRepositoryMock->expects($this->once())->method('create')->will(
            $this->returnCallback(
                function (ContentObject $contentObject)
                {
                    $contentObject->setId(28);

                    return true;
                }));
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->treeNodeCopier);
        unset($this->contentObjectCopierWrapper);
        unset($this->treeNodeDataServiceMock);
        unset($this->treeBuilderMock);
        unset($this->contentObjectRepositoryMock);
        unset($this->treeNodes);
        unset($this->treeNodesData);
        unset($this->contentObjects);
        unset($this->tree);
    }

    public function testCopyNodesFromLearningPath()
    {
        $this->contentObjectRepositoryMock->expects($this->exactly(6))->method('findById')->will(
            $this->onConsecutiveCalls(
                $this->contentObjects[2],
                $this->contentObjects[6],
                $this->contentObjects[3],
                $this->contentObjects[4],
                $this->contentObjects[5],
                $this->contentObjects[7]));

        $this->contentObjectCopierWrapper->expects($this->exactly(6))->method('copyContentObject')->will(
            $this->returnCallback(
                function (ContentObject $contentObject)
                {
                    return $contentObject->getId();
                }));

        $newTree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(50);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(51);

        $newTreeNode = new TreeNode($newTree, $learningPath, $treeNodeData);

        $user = new User();
        $user->setId(32);

        $this->treeNodeCopier->copyNodesFromLearningPath($newTreeNode, $this->contentObjects[1], $user, [1], true);

        $this->assertEquals(8, count($newTree->getTreeNodes()));
    }

    public function testCopyNodesFromLearningPathWithoutCopy()
    {
        $newTree = new Tree();

        $learningPath = new LearningPath();
        $learningPath->setId(50);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(51);

        $newTreeNode = new TreeNode($newTree, $learningPath, $treeNodeData);

        $user = new User();
        $user->setId(32);

        $this->treeNodeCopier->copyNodesFromLearningPath($newTreeNode, $this->contentObjects[1], $user, [1], false);
    }
}