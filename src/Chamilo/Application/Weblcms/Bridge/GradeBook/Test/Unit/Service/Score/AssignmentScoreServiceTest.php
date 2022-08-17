<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Test\Unit\Service\Score;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository as AssignmentPublicationRepository;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentScoreService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\EntityDataService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication as AssignmentPublication;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;

/**
 * Tests the AssignmentScoreService
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssignmentScoreServiceTest extends ChamiloTestCase
{
    /**
     * @var AssignmentScoreService
     */
    protected $assignmentScoreService;

    /**
     * @var AssignmentPublicationRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $assignmentPublicationRepositoryMock;

    /**
     * @var AssignmentService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $assignmentServiceMock;

    /**
     * @var EntityDataService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityDataServiceMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->assignmentPublicationRepositoryMock = $this->getMockBuilder(AssignmentPublicationRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->assignmentServiceMock = $this->getMockBuilder(AssignmentService::class)
            ->disableOriginalConstructor()->getMock();
        $this->entityDataServiceMock = $this->getMockBuilder(EntityDataService::class)
            ->disableOriginalConstructor()->getMock();
        $this->assignmentScoreService = new AssignmentScoreService(
            $this->assignmentPublicationRepositoryMock, $this->assignmentServiceMock, $this->entityDataServiceMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->assignmentPublicationRepositoryMock);
        unset($this->assignmentServiceMock);
        unset($this->entityDataServiceMock);
        unset($this->assignmentScoreService);
    }

    public function testAssignmentScoresEmpty()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $this->mockFindPublicationByContentObjectPublication($contentObjectPublication, 0);
        $this->mockGetMaxScoresForContentObjectPublicationEntityType($contentObjectPublication, 0, []);
        $this->assertEquals([], $this->assignmentScoreService->getScores($contentObjectPublication));
    }

    public function testAssignmentScoresUserEntity()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityScores = [['entity_id' => 7, 'maximum_score' => 80], ['entity_id' => 5, 'maximum_score' => 75], ['entity_id' => 24, 'maximum_score' => 83]];
        $this->mockFindPublicationByContentObjectPublication($contentObjectPublication, 0);
        $this->mockGetMaxScoresForContentObjectPublicationEntityType($contentObjectPublication, 0, $entityScores);
        $this->assertEquals([7 => 80, 5 => 75, 24 => 83], $this->assignmentScoreService->getScores($contentObjectPublication));
    }

    public function testAssignmentScoresExamAssignment()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $contentObjectPublication->set_tool('ExamAssignment');
        $entityScores = [['entity_id' => 7, 'maximum_score' => 80], ['entity_id' => 5, 'maximum_score' => 75], ['entity_id' => 24, 'maximum_score' => 83]];
        $this->mockGetMaxScoresForContentObjectPublicationEntityType($contentObjectPublication, 0, $entityScores);
        $this->assertEquals([7 => 80, 5 => 75, 24 => 83], $this->assignmentScoreService->getScores($contentObjectPublication));
    }

    public function testAssignmentScoresPlatformGroupEntity()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityScores = [['entity_id' => 101, 'maximum_score' => 59], ['entity_id' => 103, 'maximum_score' => 68]];
        $this->mockFindPublicationByContentObjectPublication($contentObjectPublication, 2);
        $this->mockGetMaxScoresForContentObjectPublicationEntityType($contentObjectPublication, 2, $entityScores);
        $this->mockGetUserEntitiesFromPlatformGroup([101, 103], [[7, 5], [24, 28]]);
        $this->assertEquals([7 => 59, 5 => 59, 24 => 68, 28 => 68], $this->assignmentScoreService->getScores($contentObjectPublication));
    }

    public function testAssignmentScoresCourseGroupEntity()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [101, 103];
        $entityScores = [['entity_id' => 101, 'maximum_score' => 59], ['entity_id' => 103, 'maximum_score' => 68]];
        $courseGroupIdsRecursive = [101 => [7, 5], 103 => [24, 28]];
        $this->mockServicesWithCourseGroups($contentObjectPublication, $entityScores, $entityIds, $courseGroupIdsRecursive);
        $this->assertEquals([7 => 59, 5 => 59, 24 => 68, 28 => 68], $this->assignmentScoreService->getScores($contentObjectPublication));
    }

    public function testAssignmentScoresGroupEntityUserInMultipleGroups()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [101, 103, 104];
        $entityScores = [['entity_id' => 101, 'maximum_score' => 59], ['entity_id' => 103, 'maximum_score' => 55], ['entity_id' => 104, 'maximum_score' => 72]];
        $courseGroupIdsRecursive = [101 => [7, 5], 103 => [24, 5], 104 => [28, 29]];
        $this->mockServicesWithCourseGroups($contentObjectPublication, $entityScores, $entityIds, $courseGroupIdsRecursive);
        $this->assertEquals([7 => 59, 5 => 59, 24 => 55, 28 => 72, 29 => 72], $this->assignmentScoreService->getScores($contentObjectPublication));
    }

    public function testAssignmentScoresGroupEntityUserInMultipleGroupsDifferentOrder()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [103, 101, 104];
        $entityScores = [['entity_id' => 103, 'maximum_score' => 55], ['entity_id' => 101, 'maximum_score' => 59], ['entity_id' => 104, 'maximum_score' => 72]];
        $courseGroupIdsRecursive = [103 => [24, 5], 101 => [7, 5], 104 => [28, 29]];
        $this->mockServicesWithCourseGroups($contentObjectPublication, $entityScores, $entityIds, $courseGroupIdsRecursive);
        $this->assertEquals([7 => 59, 5 => 59, 24 => 55, 28 => 72, 29 => 72], $this->assignmentScoreService->getScores($contentObjectPublication));
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param array $entityScores
     * @param array $entityIds
     * @param array $courseGroupIdsRecursive
     */
    protected function mockServicesWithCourseGroups(ContentObjectPublication $contentObjectPublication, array $entityScores, array $entityIds, array $courseGroupIdsRecursive): void
    {
        $this->mockFindPublicationByContentObjectPublication($contentObjectPublication, 1);
        $this->mockGetMaxScoresForContentObjectPublicationEntityType($contentObjectPublication, 1, $entityScores);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $courseGroupIdsRecursive);
    }

    protected function mockFindPublicationByContentObjectPublication(ContentObjectPublication $contentObjectPublication, int $entityType)
    {
        $assignmentPublication = $this->createAssignmentPublication($contentObjectPublication, $entityType);

        $this->assignmentPublicationRepositoryMock->expects($this->once())
            ->method('findPublicationByContentObjectPublication')->with($contentObjectPublication)
            ->will($this->returnValue($assignmentPublication));
    }

    protected function mockGetMaxScoresForContentObjectPublicationEntityType(ContentObjectPublication $contentObjectPublication, int $entityType, array $entityScores)
    {
        $this->assignmentServiceMock->expects($this->once())
            ->method('getMaxScoresForContentObjectPublicationEntityType')->with($contentObjectPublication, $entityType)
            ->will($this->returnValue(new RecordIterator(Entry::class_name(), $entityScores)));
    }

    protected function mockGetCourseGroupUserEntitiesRecursiveFromCourse(ContentObjectPublication $contentObjectPublication, array $returnValues)
    {
        $this->entityDataServiceMock
            ->method('getCourseGroupUserEntitiesRecursiveFromCourse')
            ->with($contentObjectPublication->get_course_id())
            ->will($this->returnValue($returnValues));
    }

    protected function mockGetUserEntitiesFromPlatformGroup(array $args, array $returnValues)
    {
        $argValues = array_map(function ($arg) { return [$arg]; }, $args);
        $returnValues = array_map($this->returnValue, $returnValues);

        $this->entityDataServiceMock
            ->method('getUserEntitiesFromPlatformGroup')
            ->withConsecutive(...$argValues)
            ->willReturnOnConsecutiveCalls(...$returnValues);
    }

    /**
     * @return ContentObjectPublication
     */
    protected function createContentObjectPublication(): ContentObjectPublication
    {
        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(11);
        $contentObjectPublication->set_course_id(4);
        $contentObjectPublication->set_content_object_id(29);
        $contentObjectPublication->set_tool('Assignment');
        return $contentObjectPublication;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     *
     * @return AssignmentPublication
     */
    protected function createAssignmentPublication(ContentObjectPublication $contentObjectPublication, int $entityType): AssignmentPublication
    {
        $assignmentPublication = new AssignmentPublication();
        $assignmentPublication->setPublicationId($contentObjectPublication->getId());
        $assignmentPublication->setEntityType($entityType);
        return $assignmentPublication;
    }
}