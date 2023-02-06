<?php

namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Test\Unit\Service;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\GradeBookItemScoreService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\ScoreServiceManager;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathStepContextRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the GradeBookItemScoreService
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookItemScoreServiceTest extends ChamiloTestCase
{
    /**
     * @var GradeBookItemScoreService
     */
    protected $gradeBookItemScoreService;

    /**
     * @var PublicationService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $publicationServiceMock;

    /**
     * @var ScoreServiceManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $scoreServiceManagerMock;

    /**
     * @var LearningPathService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $learningPathServiceMock;

    /**
     * @var LearningPathStepContextRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $learningPathStepContextRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->publicationServiceMock = $this->getMockBuilder(PublicationService::class)
            ->disableOriginalConstructor()->getMock();
        $this->scoreServiceManagerMock = $this->getMockBuilder(ScoreServiceManager::class)
            ->disableOriginalConstructor()->getMock();
        $this->learningPathServiceMock = $this->getMockBuilder(LearningPathService::class)
            ->disableOriginalConstructor()->getMock();
        $this->learningPathStepContextRepositoryMock = $this->getMockBuilder(LearningPathStepContextRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->gradeBookItemScoreService = new GradeBookItemScoreService($this->publicationServiceMock, $this->scoreServiceManagerMock, $this->learningPathServiceMock, $this->learningPathStepContextRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->publicationServiceMock);
        unset($this->scoreServiceManagerMock);
        unset($this->learningPathServiceMock);
        unset($this->learningPathStepContextRepositoryMock);
        unset($this->gradeBookItemScoreService);
    }

    public function testGetScores()
    {
        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(15);
        $contentObjectPublication->set_tool('Assignment');
        $gradeBookItem = new GradeBookItem();
        $gradeBookItem->setId(4);
        $contextIdentifier = new ContextIdentifier(ContentObjectPublication::class, 21);
        $gradeBookItem->setContextIdentifier($contextIdentifier);
        $scoreService = $this->getMockForAbstractClass('\Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\ScoreServiceInterface');
        $gs1 = new GradeScore(59);
        $gs2 =  new GradeScore(55);
        $gs3 = new GradeScore(72);
        $gs4 = new GradeScore(57);
        $userScores = [7 => $gs1, 5 => $gs1, 24 => $gs2, 28 => $gs3, 29 => $gs3, 30 => $gs4];

        $this->mockGetPublication(21, $contentObjectPublication);
        $this->mockGetScoreServiceByType('Assignment', $scoreService);
        $this->mockGetScores($scoreService, $contentObjectPublication, $userScores);
        $this->assertEquals([7 => $gs1, 5 => $gs1, 24 => $gs2, 28 => $gs3, 29 => $gs3],
            $this->gradeBookItemScoreService->getScores($gradeBookItem, [7, 5, 24, 28, 29]));
    }

    public function testGetTreeNodeScores()
    {
        $learningPath = new LearningPath();
        $tree = new Tree();
        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(10);
        $treeNode = new TreeNode($tree, $learningPath, $treeNodeData);
        $treeNode->setContentObject(new Assignment());
        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(15);
        $contentObjectPublication->set_tool('Assignment');
        $contentObjectPublication->setContentObject($learningPath);
        $gradeBookItem = new GradeBookItem();
        $gradeBookItem->setId(5);
        $contextIdentifier = new ContextIdentifier(LearningPathStepContext::class, 1);
        $gradeBookItem->setContextIdentifier($contextIdentifier);
        $lpsContext = new LearningPathStepContext();
        $lpsContext->setId(3);
        $lpsContext->setContextClass(ContentObjectPublication::class);
        $lpsContext->setContextId(22);
        $lpsContext->setLearningPathStepId(10);
        $scoreService = $this->getMockForAbstractClass('\Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\LearningPathScoreServiceInterface');
        $gs1 = new GradeScore(59);
        $gs2 = new GradeScore(55);
        $gs3 = new GradeScore(72);
        $gs4 = new GradeScore(57);
        $userScores = [7 => $gs1, 5 => $gs1, 24 => $gs2, 28 => $gs3, 29 => $gs3, 30 => $gs4];

        $this->mockFindLearningPathStepContextById($lpsContext);
        $this->mockGetPublication(22, $contentObjectPublication);
        $this->mockGetTree($learningPath, $tree);
        $this->mockGetLearningPathScoreServiceByType('Assignment', $scoreService);
        $this->mockGetScoresFromTreeNode($scoreService, $contentObjectPublication, $treeNode, $userScores);
        $this->assertEquals([7 => $gs1, 5 => $gs1, 24 => $gs2, 28 => $gs3, 29 => $gs3],
            $this->gradeBookItemScoreService->getScores($gradeBookItem, [7, 5, 24, 28, 29]));
    }

    public function testGetTreeNodeScoresException()
    {
        $gradeBookItem = new GradeBookItem();
        $gradeBookItem->setId(5);
        $contextIdentifier = new ContextIdentifier(LearningPathStepContext::class, 1);
        $gradeBookItem->setContextIdentifier($contextIdentifier);
        $lpsContext = new LearningPathStepContext();
        $lpsContext->setId(3);
        $lpsContext->setContextClass(ContentObjectPublication::class);
        $lpsContext->setContextId(22);
        $lpsContext->setLearningPathStepId(10);

        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(15);
        $contentObjectPublication->setContentObject(new ContentObject());

        $this->mockFindLearningPathStepContextById($lpsContext);
        $this->mockGetPublication(22, $contentObjectPublication);
        $this->expectException(\Exception::class);

        $this->gradeBookItemScoreService->getScores($gradeBookItem, [7, 5, 24, 28, 29]);
    }

    protected function mockFindLearningPathStepContextById(LearningPathStepContext $lpsContext): void
    {
        $this->learningPathStepContextRepositoryMock->expects($this->once())
            ->method('findLearningPathStepContextById')->with(1)
            ->will($this->returnValue($lpsContext));
    }

    protected function mockGetPublication(int $contextId, ContentObjectPublication $contentObjectPublication): void
    {
        $this->publicationServiceMock->expects($this->once())
            ->method('getPublication')->with($contextId)
            ->will($this->returnValue($contentObjectPublication));
    }

    protected function mockGetTree(LearningPath $learningPath, Tree $tree): void
    {
        $this->learningPathServiceMock->expects($this->once())
            ->method('getTree')->with($learningPath)
            ->will($this->returnValue($tree));
    }

    protected function mockGetScoreServiceByType(string $type, $scoreService): void
    {
        $this->scoreServiceManagerMock->expects($this->once())
            ->method('getScoreServiceByType')->with($type)
            ->will($this->returnValue($scoreService));
    }

    protected function mockGetLearningPathScoreServiceByType(string $type, $scoreService): void
    {
        $this->scoreServiceManagerMock->expects($this->once())
            ->method('getLearningPathScoreServiceByType')->with($type)
            ->will($this->returnValue($scoreService));
    }

    protected function mockGetScores($scoreService, ContentObjectPublication $contentObjectPublication, array $userScores): void
    {
        $scoreService->expects($this->once())
            ->method('getScores')->with($contentObjectPublication)
            ->will($this->returnValue($userScores));
    }

    protected function mockGetScoresFromTreeNode($scoreService, ContentObjectPublication $contentObjectPublication, TreeNode $treeNode, array $userScores): void
    {
        $scoreService->expects($this->once())
            ->method('getScoresFromTreeNode')->with($contentObjectPublication, $treeNode)
            ->will($this->returnValue($userScores));
    }
}