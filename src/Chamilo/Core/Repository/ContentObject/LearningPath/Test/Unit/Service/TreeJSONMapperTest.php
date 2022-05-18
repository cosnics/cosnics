<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\TreeTestDataGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the TreeJSONMapper
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeJSONMapperTest extends ChamiloTestCase
{

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
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var TrackingService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackingServiceMock;

    /**
     *
     * @var AutomaticNumberingService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $automaticNumberingServiceMock;

    /**
     *
     * @var NodeActionGenerator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $nodeActionGeneratorMock;

    /**
     *
     * @var TreeNode
     */
    protected $currentTreeNode;

    /**
     *
     * @var TreeJSONMapper
     */
    protected $treeJSONMapper;

    /**
     *
     * @var array
     */
    protected $nodesArray;

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
    protected function setUp(): void
    {
        $treeTestDataGenerator = new TreeTestDataGenerator();

        $this->tree = $treeTestDataGenerator->getTree();
        $this->contentObjects = $treeTestDataGenerator->getContentObjects();
        $this->treeNodesData = $treeTestDataGenerator->getTreeNodesData();
        $this->treeNodes = $treeTestDataGenerator->getTreeNodes();

        $this->user = new User();
        $this->user->setId(2);

        $this->trackingServiceMock = $this->getMockBuilder(TrackingService::class)->disableOriginalConstructor()->getMock();

        $this->automaticNumberingServiceMock = $this->getMockBuilder(AutomaticNumberingService::class)->disableOriginalConstructor()->getMock();

        $this->nodeActionGeneratorMock = $this->getMockBuilder(NodeActionGenerator::class)->disableOriginalConstructor()->getMock();

        $currentTreeNode = $this->treeNodes[3];

        $this->trackingServiceMock->expects($this->exactly(14))->method('isTreeNodeCompleted')->will(
            $this->onConsecutiveCalls(
                false,
                false,
                true,
                true,
                true,
                true,
                true,
                true,
                false,
                false,
                false,
                false,
                false,
                false));

        $this->automaticNumberingServiceMock->expects($this->exactly(7))->method('getAutomaticNumberingForTreeNode')->will(
            $this->returnValue(5));

        $actions = [new Action('remove', 'Remove', 'fas fa-times', 'https://remove-url', 'Are you sure?')];

        $this->nodeActionGeneratorMock->expects($this->exactly(7))->method('generateNodeActions')->will(
            $this->returnValue($actions));

        $this->contentObjects[1]->set_title('Test Learning Path');
        $this->treeNodes[4]->getTreeNodeData()->setBlocked(true);

        $this->treeJSONMapper = new TreeJSONMapper(
            $this->tree,
            $this->user,
            $this->trackingServiceMock,
            $this->automaticNumberingServiceMock,
            $this->nodeActionGeneratorMock,
            'local/index.php?node=__NODE__',
            $currentTreeNode,
            true,
            true);

        $this->nodesArray = $this->treeJSONMapper->getNodes();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void
    {
        unset($this->treeJSONMapper);
        unset($this->currentTreeNode);
        unset($this->nodeActionGeneratorMock);
        unset($this->automaticNumberingServiceMock);
        unset($this->trackingServiceMock);
        unset($this->user);
        unset($this->treeNodes);
        unset($this->treeNodesData);
        unset($this->contentObjects);
        unset($this->tree);
    }

    public function testNodesMapped()
    {
        $this->assertEquals(1, count($this->nodesArray));
    }

    public function testNodeChildren()
    {
        $this->assertEquals(3, count($this->nodesArray[0]['children']));
    }

    public function testTitle()
    {
        $this->assertEquals('Test Learning Path', $this->nodesArray[0]['title']);
    }

    public function testNumber()
    {
        $this->assertEquals(5, $this->nodesArray[0]['number']);
    }

    public function testKey()
    {
        $this->assertEquals(6, $this->nodesArray[0]['children'][0]['children'][0]['key']);
    }

    public function testIcon()
    {
        $this->assertEquals('type_learning_path', $this->nodesArray[0]['icon']);
    }

    public function testIconCompleted()
    {
        $this->assertEquals('type_section type_completed', $this->nodesArray[0]['children'][0]['icon']);
    }

    public function testFolder()
    {
        $this->assertTrue($this->nodesArray[0]['folder']);
    }

    public function testFolderOnEndNode()
    {
        $this->assertArrayNotHasKey('folder', $this->nodesArray[0]['children'][0]['children'][0]);
    }

    public function testHref()
    {
        $this->assertEquals('local/index.php?node=6', $this->nodesArray[0]['children'][0]['children'][0]['href']);
    }

    public function testExpanded()
    {
        $this->assertTrue($this->nodesArray[0]['children'][1]['expanded']);
    }

    public function testCompleted()
    {
        $this->assertTrue($this->nodesArray[0]['children'][0]['completed']);
    }

    public function testCompletedOnNotCompletedNode()
    {
        $this->assertArrayNotHasKey('completed', $this->nodesArray[0]);
    }

    public function testAction()
    {
        $action = [
            'name' => 'remove',
            'title' => 'Remove',
            'url' => 'fas fa-times',
            'image' => 'https://remove-url',
            'confirm' => true,
            'confirmation_message' => 'Are you sure?'];

        $this->assertEquals($action, $this->nodesArray[0]['actions']['remove']);
    }
}