<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests the TreeBuilder class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeBuilderTest extends ChamiloTestCase
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
     * @var Tree
     */
    protected $tree;

    /**
     * Setup before each test
     *
     * Builds a complex learning path for each test
     *
     * - Learning Path A - ID: 1
     *    - Section A - ID: 2
     *        - Page 1 - ID: 6
     *    - Section B - ID: 3
     *    - Section C - ID: 4
     *        - Section D - ID: 5
     *            - Page 2 - ID: 7
     */
    protected function setUp(): void
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

        $nodesData = [
            [
                'id' => 1, 'content_object_id' => 4, 'parent_tree_node_data_id' => 0, 'type' => LearningPath::class,
                'display_order' => 1
            ],
            [
                'id' => 2, 'content_object_id' => 8, 'parent_tree_node_data_id' => 1, 'type' => Section::class,
                'display_order' => 3
            ],
            [
                'id' => 3, 'content_object_id' => 16, 'parent_tree_node_data_id' => 1, 'type' => Section::class,
                'display_order' => 1
            ],
            [
                'id' => 4, 'content_object_id' => 19, 'parent_tree_node_data_id' => 1, 'type' => Section::class,
                'display_order' => 2
            ],
            [
                'id' => 5, 'content_object_id' => 32, 'parent_tree_node_data_id' => 2, 'type' => Section::class,
                'display_order' => 1
            ],
            [
                'id' => 6, 'content_object_id' => 46, 'parent_tree_node_data_id' => 3, 'type' => Page::class,
                'display_order' => 1
            ],
            [
                'id' => 7, 'content_object_id' => 78, 'parent_tree_node_data_id' => 5, 'type' => Page::class,
                'display_order' => 1
            ],
        ];

        $contentObjects = $treeNodesData = [];

        foreach($nodesData as $nodeData)
        {
            /** @var ContentObject $contentObject */
            $contentObject = new $nodeData['type'];
            $contentObject->setId($nodeData['content_object_id']);

            $contentObjects[] = $contentObject;

            $treeNodeData = new TreeNodeData();
            $treeNodeData->setId($nodeData['id']);
            $treeNodeData->setParentTreeNodeDataId($nodeData['parent_tree_node_data_id']);
            $treeNodeData->setContentObjectId($nodeData['content_object_id']);
            $treeNodeData->setDisplayOrder($nodeData['display_order']);

            $treeNodesData[] = $treeNodeData;
        }

        $resultSet = new ArrayCollection($contentObjects);

        $this->treeNodeDataRepositoryMock->expects($this->once())
            ->method('findTreeNodesDataForLearningPath')
            ->with($contentObjects[0])
            ->willReturn($treeNodesData);

        $this->contentObjectRepositoryMock->expects($this->once())
            ->method('findAll')
            ->with(ContentObject::class)
            ->will($this->returnValue($resultSet));

        $this->tree = $this->treeBuilder->buildTree($contentObjects[0]);
    }

    /**
     * Teardown after each test
     */
    protected function tearDown(): void
    {
        unset($this->treeNodeDataRepositoryMock);
        unset($this->contentObjectRepositoryMock);
        unset($this->treeBuilder);
    }

    public function testTreeHasAllNodes()
    {
        $this->assertEquals(7, count($this->tree->getTreeNodes()));
    }

    public function testParentsCorrectlyMapped()
    {
        $this->assertEquals(3, $this->tree->getTreeNodeByStep(3)->getParentNode()->getId());
    }

    public function testDisplayOrderCorrectlyMapped()
    {
        $this->assertEquals(2, $this->tree->getTreeNodeByStep(5)->getId());
    }

    public function testChildNodes()
    {
        $this->assertEquals(3, count($this->tree->getTreeNodeByStep(1)->getChildNodes()));
    }
}