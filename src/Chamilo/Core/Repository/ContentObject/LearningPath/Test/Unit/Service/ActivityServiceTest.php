<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActivityService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\TreeTestDataGenerator;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\Repository\ActivityRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the ActivityService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ActivityServiceTest extends ChamiloTestCase
{
    /**
     * @var ActivityService
     */
    protected $activityService;

    /**
     * @var ActivityRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $activityRepositoryMock;

    /**
     * @var AutomaticNumberingService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $automaticNumberingServiceMock;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var LearningPath[] | Section[] | Page[] | ContentObject[]
     */
    protected $contentObjects;

    /**
     * @var TreeNodeData[]
     */
    protected $treeNodesData;

    /**
     * @var TreeNode[]
     */
    protected $treeNodes;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $this->activityRepositoryMock = $this->getMockBuilder(ActivityRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->automaticNumberingServiceMock = $this->getMockBuilder(AutomaticNumberingService::class)
            ->disableOriginalConstructor()->getMock();

        $this->activityService =
            new ActivityService($this->activityRepositoryMock, $this->automaticNumberingServiceMock);

        $treeTestDataGenerator = new TreeTestDataGenerator();

        $this->tree = $treeTestDataGenerator->getTree();
        $this->contentObjects = $treeTestDataGenerator->getContentObjects();
        $this->treeNodesData = $treeTestDataGenerator->getTreeNodesData();
        $this->treeNodes = $treeTestDataGenerator->getTreeNodes();
        $this->learningPath = $this->tree->getRoot()->getContentObject();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->activityRepositoryMock);
        unset($this->automaticNumberingServiceMock);
        unset($this->activityService);
        unset($this->treeNodes);
        unset($this->treeNodesData);
        unset($this->contentObjects);
        unset($this->tree);
        unset($this->learningPath);
    }

    public function testCountActivitiesForTreeNode()
    {
        $this->activityRepositoryMock->expects($this->exactly(2))
            ->method('countActivitiesForContentObject')
            ->withConsecutive([$this->contentObjects[2]], [$this->contentObjects[6]])
            ->will($this->onConsecutiveCalls(2, 5));

        $this->assertEquals(7, $this->activityService->countActivitiesForTreeNode($this->treeNodes[2]));
    }

    public function testRetrieveActivitiesForTreeNode()
    {
        $activities2 = [new Activity(), new Activity()];
        $activities5 = [new Activity(), new Activity(), new Activity(), new Activity(), new Activity()];

        $this->activityRepositoryMock->expects($this->exactly(2))
            ->method('retrieveActivitiesForContentObject')
            ->withConsecutive([$this->contentObjects[2]], [$this->contentObjects[6]])
            ->will($this->onConsecutiveCalls($activities2, $activities5));

        $this->activityRepositoryMock->expects($this->once())
            ->method('filterActivities')
            ->will($this->returnArgument(0));

        $this->assertCount(7, $this->activityService->retrieveActivitiesForTreeNode($this->treeNodes[2]));
    }
}