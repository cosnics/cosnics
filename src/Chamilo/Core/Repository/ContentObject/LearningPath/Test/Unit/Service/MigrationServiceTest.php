<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\MigrationService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Exception;
use PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls;

/**
 * Tests the MigrationService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MigrationServiceTest extends ChamiloTestCase
{
    /**
     * @var MigrationService
     */
    protected $migrationService;

    /**
     * @var LearningPathService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $learningPathServiceMock;

    /**
     * @var TreeNodeDataService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $treeNodeDataServiceMock;

    /**
     * @var TrackingRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackingRepositoryMock;

    /**
     * @var ContentObjectRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentObjectRepositoryMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        $this->learningPathServiceMock = $this->getMockBuilder(LearningPathService::class)
            ->disableOriginalConstructor()->getMock();

        $this->treeNodeDataServiceMock = $this->getMockBuilder(TreeNodeDataService::class)
            ->disableOriginalConstructor()->getMock();

        $this->trackingRepositoryMock = $this->getMockBuilder(TrackingRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->contentObjectRepositoryMock = $this->getMockBuilder(ContentObjectRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->migrationService = new MigrationService(
            $this->learningPathServiceMock, $this->treeNodeDataServiceMock, $this->trackingRepositoryMock,
            $this->contentObjectRepositoryMock
        );

        ob_start();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void
    {
        unset($this->learningPathServiceMock);
        unset($this->treeNodeDataServiceMock);
        unset($this->trackingRepositoryMock);
        unset($this->contentObjectRepositoryMock);
        unset($this->migrationService);

        ob_get_clean();
    }

    /**
     * Prepares the learning path data
     *
     * - Learning Path A - ID: 1
     *    - Learning Path B - ID: 2
     *        - Page 1 - ID: 6
     *    - Learning Path C - ID: 3
     *    - Learning Path D - ID: 4
     *        - Learning Path E - ID: 5
     *            - Page 2 - ID: 7
     */
    protected function prepareLearningPathData()
    {
        $learningPathA = new LearningPath();
        $learningPathA->setId(1);

        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(15);
        $treeNodeData->setLearningPathId((int) $learningPathA->getId());
        $treeNodeData->setContentObjectId((int) $learningPathA->getId());

        $this->learningPathServiceMock->expects($this->once())
            ->method('getLearningPaths')
            ->will($this->returnValue([$learningPathA]));

        $this->treeNodeDataServiceMock->expects($this->exactly(1))
            ->method('createTreeNodeDataForLearningPath')
            ->will($this->returnValue($treeNodeData));

        $learningPathB = new LearningPath();
        $learningPathB->setId(2);

        $learningPathC = new LearningPath();
        $learningPathC->setId(3);

        $learningPathD = new LearningPath();
        $learningPathD->setId(4);

        $learningPathE = new LearningPath();
        $learningPathE->setId(5);

        $pageA = new Page();
        $pageA->setId(6);

        $pageB = new Page();
        $pageB->setId(7);

        $learningPathItemA = new LearningPathItem();
        $learningPathItemA->setId(8);
        $learningPathItemA->set_reference(6);

        $learningPathItemB = new LearningPathItem();
        $learningPathItemB->setId(9);
        $learningPathItemB->set_reference(7);

        $complexContentObjectItemA = new ComplexContentObjectItem();
        $complexContentObjectItemA->setId(70);
        $complexContentObjectItemA->set_parent($learningPathA->getId());
        $complexContentObjectItemA->set_ref($learningPathB->getId());

        $complexContentObjectItemB = new ComplexContentObjectItem();
        $complexContentObjectItemB->setId(71);
        $complexContentObjectItemB->set_parent($learningPathB->getId());
        $complexContentObjectItemB->set_ref($pageA->getId());

        $complexContentObjectItemC = new ComplexContentObjectItem();
        $complexContentObjectItemC->setId(72);
        $complexContentObjectItemC->set_parent($learningPathA->getId());
        $complexContentObjectItemC->set_ref($learningPathC->getId());

        $complexContentObjectItemD = new ComplexContentObjectItem();
        $complexContentObjectItemD->setId(73);
        $complexContentObjectItemD->set_parent($learningPathA->getId());
        $complexContentObjectItemD->set_ref($learningPathD->getId());

        $complexContentObjectItemE = new ComplexContentObjectItem();
        $complexContentObjectItemE->setId(74);
        $complexContentObjectItemE->set_parent($learningPathD->getId());
        $complexContentObjectItemE->set_ref($learningPathE->getId());

        $complexContentObjectItemF = new ComplexContentObjectItem();
        $complexContentObjectItemF->setId(75);
        $complexContentObjectItemF->set_parent($learningPathE->getId());
        $complexContentObjectItemF->set_ref($pageB->getId());

        $resultSets = [
            [$complexContentObjectItemA, $complexContentObjectItemC, $complexContentObjectItemD],
            [$complexContentObjectItemB],
            [],
            [$complexContentObjectItemE],
            [$complexContentObjectItemF]
        ];

        $arrayResultSets = [];
        foreach ($resultSets as $resultSet)
        {
            $arrayResultSets[] = new DataClassIterator(ComplexContentObjectItem::class, $resultSet);
        }

        $contentObjects = [
            $learningPathB, $learningPathItemA, $pageA, $learningPathC, $learningPathD, $learningPathE,
            $learningPathItemB, $pageB
        ];

        return [
            'learningPathA' => $learningPathA,
            'contentObjects' => $contentObjects,
            'resultSets' => $arrayResultSets
        ];
    }

    /**
     * Migrates the following Learning Path
     */
    public function testMigrateLearningPaths()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->exactly(5))
            ->method('findAll')
            ->with(ComplexContentObjectItem::class)
            ->will(new ConsecutiveCalls($testData['resultSets']));

        $this->contentObjectRepositoryMock->expects($this->exactly(8))
            ->method('findById')
            ->will(new ConsecutiveCalls($testData['contentObjects']));

        $sectionId = 20;
        $this->contentObjectRepositoryMock->expects($this->exactly(4))
            ->method('create')
            ->will(
                $this->returnCallback(
                    function (Section $section) use (&$sectionId)
                    {
                        $section->setId($sectionId);
                        $sectionId ++;

                        return true;
                    }
                )
            );

        $this->treeNodeDataServiceMock->expects($this->exactly(6))
            ->method('createTreeNodeData');

        $this->migrationService->migrateLearningPaths();
    }

    public function testLearningPathPrerequisites()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(new DataClassIterator(ComplexLearningPathItem::class, [])));

        $this->contentObjectRepositoryMock->expects($this->once())
            ->method('countAll')
            ->with(ComplexLearningPathItem::class)
            ->will($this->returnValue(5));

        $this->migrationService->migrateLearningPaths();

        $this->assertTrue($testData['learningPathA']->enforcesDefaultTraversingOrder());
    }

    public function testMigrateLearningPathsWithChildContentObjectNotFound()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->exactly(1))
            ->method('findAll')
            ->with(ComplexContentObjectItem::class)
            ->will(new ConsecutiveCalls($testData['resultSets']));

        $this->contentObjectRepositoryMock->expects($this->at(1))
            ->method('findById')
            ->will($this->throwException(new Exception()));

        for ($i = 2; $i <= 5; $i ++)
        {
            $this->contentObjectRepositoryMock->expects($this->at($i))
                ->method('findById')
                ->will($this->returnValue($testData['contentObjects'][1]));
        }

        $this->migrationService->migrateLearningPaths();
    }

    public function testMigrateLearningPathsWithNoLearningPathItem()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->exactly(1))
            ->method('findAll')
            ->with(ComplexContentObjectItem::class)
            ->will(new ConsecutiveCalls($testData['resultSets']));

        $this->contentObjectRepositoryMock->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($testData['contentObjects'][2]));

        $this->migrationService->migrateLearningPaths();
    }

    public function testMigrateLearningPathsWithInvalidReferenceForLearningPathItem()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->exactly(1))
            ->method('findAll')
            ->with(ComplexContentObjectItem::class)
            ->will(new ConsecutiveCalls($testData['resultSets']));

        for ($i = 1; $i <= 3; $i ++)
        {
            $this->contentObjectRepositoryMock->expects($this->at($i))
                ->method('findById')
                ->will($this->returnValue($testData['contentObjects'][1]));
        }

        $this->contentObjectRepositoryMock->expects($this->at(4))
            ->method('findById')
            ->will($this->throwException(new Exception()));

        $this->migrationService->migrateLearningPaths();
    }

    /**
     * @expectedException \Exception
     */
    public function testMigrateLearningPathsSectionCreateFails()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->exactly(1))
            ->method('findAll')
            ->with(ComplexContentObjectItem::class)
            ->will($this->returnValue($testData['resultSets'][3]));

        $this->contentObjectRepositoryMock->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($testData['contentObjects'][0]));

        $this->contentObjectRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(false));

        $this->migrationService->migrateLearningPaths();
    }

    public function testMigrateLearningPathsFixLearningPathTracking()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->exactly(1))
            ->method('findAll')
            ->with(ComplexContentObjectItem::class)
            ->will($this->returnValue($testData['resultSets'][4]));

        $this->contentObjectRepositoryMock->expects($this->at(1))
            ->method('findById')
            ->will($this->returnValue($testData['contentObjects'][1]));

        $this->contentObjectRepositoryMock->expects($this->at(2))
            ->method('findById')
            ->will($this->returnValue($testData['contentObjects'][2]));

        $attempt = new LearningPathTreeNodeAttempt();
        $attempt->setTreeNodeDataId(75);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttemptsForLearningPath')
            ->with($testData['learningPathA'])
            ->will($this->returnValue([$attempt]));

        $this->treeNodeDataServiceMock->expects($this->once())
            ->method('createTreeNodeData')
            ->will(
                $this->returnCallback(
                    function (TreeNodeData $treeNodeData)
                    {
                        $treeNodeData->setId(108);
                        return true;
                    }
                )
            );

        $this->migrationService->migrateLearningPaths();

        $this->assertEquals(108, $attempt->getTreeNodeDataId());
    }

    public function testMigrateLearningPathsFixLearningPathTrackingWitNoMapping()
    {
        $testData = $this->prepareLearningPathData();

        $this->contentObjectRepositoryMock->expects($this->exactly(1))
            ->method('findAll')
            ->with(ComplexContentObjectItem::class)
            ->will($this->returnValue($testData['resultSets'][4]));

        $this->contentObjectRepositoryMock->expects($this->at(1))
            ->method('findById')
            ->will($this->returnValue($testData['contentObjects'][1]));

        $this->contentObjectRepositoryMock->expects($this->at(2))
            ->method('findById')
            ->will($this->returnValue($testData['contentObjects'][2]));

        $attempt = new LearningPathTreeNodeAttempt();
        $attempt->setTreeNodeDataId(78);

        $this->trackingRepositoryMock->expects($this->once())
            ->method('findTreeNodeAttemptsForLearningPath')
            ->with($testData['learningPathA'])
            ->will($this->returnValue([$attempt]));

        $this->migrationService->migrateLearningPaths();
        $this->assertEquals(78, $attempt->getTreeNodeDataId());
    }

}