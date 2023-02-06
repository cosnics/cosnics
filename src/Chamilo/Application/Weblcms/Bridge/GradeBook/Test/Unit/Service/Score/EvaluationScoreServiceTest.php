<?php

namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Test\Unit\Service\Score;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\EntityDataService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\EvaluationScoreService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\EvaluationConfiguration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository as EvaluationPublicationRepository;
use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceManager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication as EvaluationPublication;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceInterface;
use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceInterface;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;

/**
 * Tests the EvaluationScoreService
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationScoreServiceTest extends ChamiloTestCase
{
    /**
     * @var EvaluationScoreService
     */
    protected $evaluationScoreService;

    /**
     * @var EvaluationPublicationRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $evaluationPublicationRepositoryMock;

    /**
     * @var PublicationEntityServiceManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $publicationEntityServiceManagerMock;

    /**
     * @var EvaluationEntityServiceManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $evaluationEntityServiceManagerMock;

    /**
     * @var LearningPathStepContextService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $learningPathStepContextServiceMock;

    /**
     * @var PublicationEntityServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $publicationEntityServiceMock;

    /**
     * @var EvaluationEntityServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $evaluationEntityServiceMock;

    /**
     * @var EntityDataService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityDataServiceMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->evaluationPublicationRepositoryMock = $this->getMockBuilder(EvaluationPublicationRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->publicationEntityServiceManagerMock = $this->getMockBuilder(PublicationEntityServiceManager::class)
            ->disableOriginalConstructor()->getMock();
        $this->evaluationEntityServiceManagerMock = $this->getMockBuilder(EvaluationEntityServiceManager::class)
            ->disableOriginalConstructor()->getMock();
        $this->learningPathStepContextServiceMock = $this->getMockBuilder(LearningPathStepContextService::class)
            ->disableOriginalConstructor()->getMock();
        $this->publicationEntityServiceMock = $this->getMockForAbstractClass(
            '\Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceInterface'
        );
        $this->evaluationEntityServiceMock = $this->getMockForAbstractClass(
            'Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceInterface'
        );
        $this->entityDataServiceMock = $this->getMockBuilder(EntityDataService::class)
            ->disableOriginalConstructor()->getMock();

        $this->evaluationScoreService = new EvaluationScoreService($this->evaluationPublicationRepositoryMock, $this->publicationEntityServiceManagerMock, $this->evaluationEntityServiceManagerMock, $this->learningPathStepContextServiceMock, $this->entityDataServiceMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->evaluationPublicationRepositoryMock);
        unset($this->publicationEntityServiceManagerMock);
        unset($this->evaluationEntityServiceManagerMock);
        unset($this->learningPathStepContextServiceMock);
        unset($this->publicationEntityServiceMock);
        unset($this->evaluationEntityServiceMock);
        unset($this->entityDataServiceMock);
        unset($this->evaluationScoreService);
    }

    public function testEvaluationScoresEmpty()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $this->mockServices($contentObjectPublication, 0, [], new RecordIterator(User::class_name(), []));
        $this->assertEquals([], $this->evaluationScoreService->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresUserEntity()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [10, 15, 30];
        $entityType = 0;
        $entitiesFromIds = new RecordIterator(User::class_name(), [
            ['id' => '10', 'score' => '75', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '15', 'score' => '76', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '30', 'score' => '70', 'score_registered' => '1', 'is_absent' => '0']
        ]);

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);

        $this->assertEquals([10 => 75, 15 => 76, 30 => 70], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresCourseGroupEntity()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => null, 'score_registered' => '0', 'is_absent' => '1'],
            ['id' => '22', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [9, 18], 22 => [37, 55]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => EvaluationScoreService::AUTH_ABSENT, 18 => EvaluationScoreService::AUTH_ABSENT, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresPlatformGroupEntity()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 2;
        $entitiesFromIds = new RecordIterator(Group::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => null, 'score_registered' => '0', 'is_absent' => '1'],
            ['id' => '22', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
        ]);

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetUserEntitiesFromPlatformGroup($entityIds, [[10, 4], [9, 18], [37, 55]]);

        $this->assertEquals([10 => 90, 4 => 90, 9 => EvaluationScoreService::AUTH_ABSENT, 18 => EvaluationScoreService::AUTH_ABSENT, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithScoreOverAbsencePresedence()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => null, 'score_registered' => '0', 'is_absent' => '1'],
            ['id' => '22', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [9, 18], 22 => [37, 55, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => EvaluationScoreService::AUTH_ABSENT, 18 => 80, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithScoreOverAbsencePresedenceDifferentOrder()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '22', 'score' => null, 'score_registered' => '0', 'is_absent' => '1'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [37, 55, 18], 22 => [9, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => EvaluationScoreService::AUTH_ABSENT, 18 => 80, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithScoreOverNullPresedence()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => null, 'score_registered' => '0', 'is_absent' => '0'],
            ['id' => '22', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [9, 18], 22 => [37, 55, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => null, 18 => 80, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithScoreOverNullPresedenceDifferentOrder()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '22', 'score' => null, 'score_registered' => '0', 'is_absent' => '0'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [37, 55, 18], 22 => [9, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => null, 18 => 80, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithAbsenceOverNullPresedence()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => null, 'score_registered' => '0', 'is_absent' => '0'],
            ['id' => '22', 'score' => null, 'score_registered' => '0', 'is_absent' => '1'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [9, 18], 22 => [37, 55, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => null, 18 => EvaluationScoreService::AUTH_ABSENT, 37 => EvaluationScoreService::AUTH_ABSENT, 55 => EvaluationScoreService::AUTH_ABSENT], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithAbsenceOverNullPresedenceDifferentOrder()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => null, 'score_registered' => '1', 'is_absent' => '1'],
            ['id' => '22', 'score' => null, 'score_registered' => '0', 'is_absent' => '0'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [37, 55, 18], 22 => [9, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => null, 18 => EvaluationScoreService::AUTH_ABSENT, 37 => EvaluationScoreService::AUTH_ABSENT, 55 => EvaluationScoreService::AUTH_ABSENT], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithHighestScorePresedence()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => '70', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '22', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [9, 18], 22 => [37, 55, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => 70, 18 => 80, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testEvaluationScoresGroupEntityWithHighestScorePresedenceDifferentOrder()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityIds = [20, 21, 22];
        $entityType = 1;
        $entitiesFromIds = new RecordIterator(CourseGroup::class_name(), [
            ['id' => '20', 'score' => '90', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '21', 'score' => '80', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '22', 'score' => '70', 'score_registered' => '1', 'is_absent' => '0'],
        ]);
        $returnValues = [20 => [10, 4], 21 => [37, 55, 18], 22 => [9, 18]];

        $this->mockServices($contentObjectPublication, $entityType, $entityIds, $entitiesFromIds);
        $this->mockGetCourseGroupUserEntitiesRecursiveFromCourse($contentObjectPublication, $returnValues);

        $this->assertEquals([10 => 90, 4 => 90, 9 => 70, 18 => 80, 37 => 80, 55 => 80], $this->getScores($contentObjectPublication));
    }

    public function testLearningPathEvaluationScores()
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $entityType = 0;
        $entitiesFromIds = new RecordIterator(User::class_name(), [
            ['id' => '10', 'score' => '75', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '15', 'score' => '76', 'score_registered' => '1', 'is_absent' => '0'],
            ['id' => '30', 'score' => '70', 'score_registered' => '1', 'is_absent' => '0']
        ]);
        $entityIds = [10, 15, 30];
        $stepId = 29;
        $learningPathStepContextId = 301;
        $treeNodeMock = $this->createTreeNodeMock();
        $configurationMock = $this->createEvaluationConfigurationMock();
        $learningPathStepContextMock = $this->createLearningPathStepContextMock();
        $contextIdentifier = new ContextIdentifier(get_class($learningPathStepContextMock), $learningPathStepContextId);

        $treeNodeMock->expects($this->once())->method('getConfiguration')->will($this->returnValue($configurationMock));
        $treeNodeMock->expects($this->once())->method('getId')->will($this->returnValue($stepId));
        $configurationMock->expects($this->once())->method('getEntityType')->will($this->returnValue($entityType));
        $this->mockGetOrCreateLearningPathStepContext($contentObjectPublication, $learningPathStepContextMock, $stepId);
        $learningPathStepContextMock->expects($this->once())->method('getId')->will($this->returnValue($learningPathStepContextId));
        $this->mockSetContentObjectPublication($contentObjectPublication);
        $this->mockGetPublicationEntityService($entityType);
        $this->mockGetTargetEntityIds($entityIds);
        $this->mockGetEvaluationEntityService($entityType);
        $this->mockGetEntitiesFromIds($contentObjectPublication, $entityIds, $entitiesFromIds, $contextIdentifier);

        $this->assertEquals([10 => 75, 15 => 76, 30 => 70], $this->getScoresFromTreeNode($contentObjectPublication, $treeNodeMock));
    }

    protected function mockServices(ContentObjectPublication $contentObjectPublication, int $entityType, array $entityIds, RecordIterator $entitiesFromIds)
    {
        $this->mockFindPublicationByContentObjectPublication($contentObjectPublication, $entityType);
        $this->mockSetContentObjectPublication($contentObjectPublication);
        $this->mockGetPublicationEntityService($entityType);
        $this->mockGetTargetEntityIds($entityIds);
        $this->mockGetEvaluationEntityService($entityType);
        $this->mockGetEntitiesFromIds($contentObjectPublication, $entityIds, $entitiesFromIds);
    }

    protected function mockFindPublicationByContentObjectPublication(ContentObjectPublication $contentObjectPublication, int $entityType)
    {
        $evaluationPublication = $this->createEvaluationPublication($contentObjectPublication, $entityType);

        $this->evaluationPublicationRepositoryMock->expects($this->once())
            ->method('findPublicationByContentObjectPublication')->with($contentObjectPublication)
            ->will($this->returnValue($evaluationPublication));
    }

    protected function mockSetContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->publicationEntityServiceManagerMock->expects($this->once())
            ->method('setContentObjectPublication')->with($contentObjectPublication);
    }

    protected function mockGetPublicationEntityService(int $entityType)
    {
        $this->publicationEntityServiceManagerMock->expects($this->once())
            ->method('getEntityServiceByType')->with($entityType)
            ->will($this->returnValue($this->publicationEntityServiceMock));
    }

    protected function mockGetTargetEntityIds(array $entityIds)
    {
        $this->publicationEntityServiceMock->expects($this->once())
            ->method('getTargetEntityIds')
            ->will($this->returnValue($entityIds));
    }

    protected function mockGetEvaluationEntityService(int $entityType)
    {
        $this->evaluationEntityServiceManagerMock->expects($this->once())
            ->method('getEntityServiceByType')->with($entityType)
            ->will($this->returnValue($this->evaluationEntityServiceMock));
    }

    protected function mockGetEntitiesFromIds(ContentObjectPublication $contentObjectPublication, array $entityIds, RecordIterator $entitiesFromIds, ContextIdentifier $contextIdentifier = null)
    {
        if (empty($contextIdentifier))
        {
            $contextIdentifier = new ContextIdentifier(EvaluationPublication::class_name(), $contentObjectPublication->getId());
        }

        $this->evaluationEntityServiceMock->expects($this->once())
            ->method('getEntitiesFromIds')->with($entityIds, $contextIdentifier)
            ->will($this->returnValue($entitiesFromIds));
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

    protected function mockGetOrCreateLearningPathStepContext(ContentObjectPublication $contentObjectPublication, $learningPathStepContextMock, int $stepId)
    {
        $this->learningPathStepContextServiceMock->expects($this->once())->method('getOrCreateLearningPathStepContext')
            ->with($stepId, ContentObjectPublication::class_name(), $contentObjectPublication->getId())
            ->will($this->returnValue($learningPathStepContextMock));
    }

    /**
     * @return ContentObjectPublication
     */
    protected function createContentObjectPublication(): ContentObjectPublication
    {
        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(44);
        $contentObjectPublication->set_course_id(19);
        $contentObjectPublication->set_content_object_id(12);
        $contentObjectPublication->set_tool('Evaluation');
        return $contentObjectPublication;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     *
     * @return EvaluationPublication
     */
    protected function createEvaluationPublication(ContentObjectPublication $contentObjectPublication, int $entityType): EvaluationPublication
    {
        $evaluationPublication = new EvaluationPublication();
        $evaluationPublication->setPublicationId($contentObjectPublication->getId());
        $evaluationPublication->setEntityType($entityType);
        return $evaluationPublication;
    }

    /**
     * @return TreeNode|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createTreeNodeMock()
    {
        /** @var TreeNode | \PHPUnit_Framework_MockObject_MockObject $treeNodeMock */
        return $this->getMockBuilder(TreeNode::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return EvaluationConfiguration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createEvaluationConfigurationMock()
    {
        /** @var EvaluationConfiguration | \PHPUnit_Framework_MockObject_MockObject $configurationMock */
        return $this->getMockBuilder(EvaluationConfiguration::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return LearningPathStepContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createLearningPathStepContextMock()
    {
        /** @var LearningPathStepContext | \PHPUnit_Framework_MockObject_MockObject $learningPathStepContextMock */
        return $this->getMockBuilder(LearningPathStepContext::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return array
     */
    protected function getScores(ContentObjectPublication $contentObjectPublication): array
    {
        $userScores = $this->evaluationScoreService->getScores($contentObjectPublication);
        return $this->getSimplifiedScores($userScores);
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param TreeNode|\PHPUnit_Framework_MockObject_MockObject $treeNodeMock
     *
     * @return array
     */
    public function getScoresFromTreeNode(ContentObjectPublication $contentObjectPublication, $treeNodeMock): array
    {
        $userScores = $this->evaluationScoreService->getScoresFromTreeNode($contentObjectPublication, $treeNodeMock);
        return $this->getSimplifiedScores($userScores);
    }

    /**
     * @param array $userScores
     *
     * @return array
     */
    protected function getSimplifiedScores(array $userScores): array
    {
        $scores = array();
        foreach ($userScores as $userId => $gradeScore)
        {
            if ($gradeScore->isAuthAbsent())
            {
                $scores[$userId] = EvaluationScoreService::AUTH_ABSENT;
            }
            else if ($gradeScore->hasValue())
            {
                $scores[$userId] = $gradeScore->getValue();
            }
            else
            {
                $scores[$userId] = null;
            }
        }
        return $scores;
    }
}