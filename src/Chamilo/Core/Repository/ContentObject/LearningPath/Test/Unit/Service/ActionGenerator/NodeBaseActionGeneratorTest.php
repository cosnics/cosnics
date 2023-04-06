<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\ActionGenerator;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeBaseActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Translation\Translation;

/**
 * Tests the NodeBaseActionGenerator class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NodeBaseActionGeneratorTest extends ChamiloTestCase
{
    /**
     * @var NodeBaseActionGenerator
     */
    protected $nodeBaseActionGenerator;

    /**
     * @var Translation | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;

    /**
     * @var NodeActionGenerator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentObjectTypeNodeActionGeneratorMock;

    /**
     * @var array
     */
    protected $baseParameters;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->translatorMock = $this->getMockBuilder(Translation::class)->disableOriginalConstructor()->getMock();
        $this->contentObjectTypeNodeActionGeneratorMock = $this->getMockBuilder(NodeActionGenerator::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->baseParameters = ['parameter1' => 'value1'];

        $this->nodeBaseActionGenerator = new NodeBaseActionGenerator(
            $this->translatorMock, $this->baseParameters,
            [
                'Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page' =>
                    $this->contentObjectTypeNodeActionGeneratorMock
            ]
        );

        $this->translatorMock->expects($this->any())
            ->method('getTranslation')
            ->will(
                $this->returnCallback(
                    function ($variable)
                    {
                        return $variable;
                    }
                )
            );

        $learningPath = new LearningPath();
        $learningPath->setId(5);

        $page = new Page();
        $page->setId(9);

        $this->tree = new Tree();

        $treeNodeDataLP = new TreeNodeData();
        $treeNodeDataLP->setId(1);

        $treeNodeDataPage = new TreeNodeData();
        $treeNodeDataPage->setId(2);

        $lpTreeNode = new TreeNode($this->tree, $learningPath, $treeNodeDataLP);
        $pageTreeNode = new TreeNode($this->tree, $page, $treeNodeDataPage);

        $lpTreeNode->addChildNode($pageTreeNode);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->nodeBaseActionGenerator);
        unset($this->baseParameters);
        unset($this->contentObjectTypeNodeActionGeneratorMock);
        unset($this->translatorMock);
        unset($this->tree);
    }

    public function testGenerateNodeActions()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), false);
        $this->assertCount(3, $actions);
    }

    public function testProgressActionAdded()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), false);

        $expectedData = array (
            'name' => 'progress',
            'title' => 'MyProgress',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=Reporting&child_id=2',
            'image' => 'fa-pie-chart',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[0]->toArray());
    }

    public function testActivityActionAdded()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), false);

        $expectedData = array (
            'name' => 'activity',
            'title' => 'ActivityComponent',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=Activity&child_id=2',
            'image' => 'fa-mouse-pointer',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[1]->toArray());
    }

    public function testViewActionAdded()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), false);

        $expectedData = array (
            'name' => 'view',
            'title' => 'ReturnToLearningPath',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=Viewer&child_id=2',
            'image' => 'fa-file',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[2]->toArray());
    }

    public function testManageNodesActionWhenNodeHasChildren()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(1), false);

        $expectedData = array (
            'name' => 'manage',
            'title' => 'ManagerComponent',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=Manager&child_id=1',
            'image' => 'fa-bars',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[2]->toArray());
    }

    public function testNodeSpecificActions()
    {
        $specificAction = new Action('test', 'Test Action', 'index.php?go=test', 'fa fa-user', 'Are you sure?');

        $this->contentObjectTypeNodeActionGeneratorMock->expects($this->once())
            ->method('generateNodeActions')
            ->with($this->tree->getTreeNodeByStep(2), false)
            ->will($this->returnValue([$specificAction]));

        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), false);

        $expectedData = array (
            'name' => 'test',
            'title' => 'Test Action',
            'url' => 'index.php?go=test',
            'image' => 'fa fa-user',
            'confirm' => true,
            'confirmation_message' => 'Are you sure?',
        );

        $this->assertEquals($expectedData, $actions[3]->toArray());
    }

    public function testGenerateNodeActionsWithEditRight()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), true, true);
        $this->assertCount(12, $actions);
    }

    public function testUpdateAction()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), true);

        $expectedData = array (
            'name' => 'edit',
            'title' => 'UpdaterComponent',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=Updater&child_id=2',
            'image' => 'fa-pencil',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[0]->toArray());
    }

    public function testReportingAction()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), true, true);

        $expectedData = array (
            'name' => 'reporting',
            'title' => 'Reporting',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=UserProgress&child_id=2',
            'image' => 'fa-bar-chart',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[8]->toArray());
    }

    public function testBlockUnblockNodeAction()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), true);

        $expectedData = array (
            'name' => 'block',
            'title' => 'MarkAsRequired',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=ToggleBlockedStatus&child_id=2',
            'image' => 'fa-ban',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[1]->toArray());
    }

    public function testDeleteNodeAction()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), true);

        $expectedData = array (
            'name' => 'delete',
            'title' => 'DeleterComponent',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=Deleter&child_id=2',
            'image' => 'fa-times',
            'confirm' => true,
            'confirmation_message' => 'Confirm',
        );

        $this->assertEquals($expectedData, $actions[2]->toArray());
    }

    public function testMoveNodeAction()
    {
        $actions = $this->nodeBaseActionGenerator->generateNodeActions($this->tree->getTreeNodeByStep(2), true);

        $expectedData = array (
            'name' => 'move',
            'title' => 'Move',
            'url' => 'http://bin/phpunit?parameter1=value1&learning_path_action=Mover&child_id=2',
            'image' => 'fa-random',
            'confirm' => false,
            'confirmation_message' => NULL,
        );

        $this->assertEquals($expectedData, $actions[3]->toArray());
    }
}