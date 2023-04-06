<?php

namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Test\Unit\Service\Score;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\LearningPathTrackingScoreService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\TrackingServiceBuilderService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;

/**
 * Tests the LearningPathTrackingScoreService
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathTrackingScoreServiceTest extends ChamiloTestCase
{
    /**
     * @var LearningPathTrackingScoreService
     */
    protected $learningPathTrackingScoreService;

    /**
     * @var TrackingServiceBuilderService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackingServiceBuilderServiceMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->trackingServiceBuilderServiceMock = $this->getMockBuilder(TrackingServiceBuilderService::class)
            ->disableOriginalConstructor()->getMock();
        $this->learningPathTrackingScoreService = new LearningPathTrackingScoreService($this->trackingServiceBuilderServiceMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->trackingServiceBuilderServiceMock);
        unset($this->learningPathTrackingScoreService);
    }

    public function testGetScoresFromTreeNodeException()
    {
        $contentObject = new ContentObject();
        $contentObject->setId(16);
        $contentObjectPublication = $this->createContentObjectPublication($contentObject);
        $tree = new Tree();
        $treeNode = new TreeNode($tree);
        $this->expectException(\Exception::class);
        $this->learningPathTrackingScoreService->getScoresFromTreeNode($contentObjectPublication, $treeNode);
    }

    public function testGetScoresFromTreeNodeEmpty()
    {
        $this->_testGetScoresFromTreeNode([], []);
    }

    public function testGetScoresFromTreeNode()
    {
        $this->_testGetScoresFromTreeNode([10 => 40, 12 => 78], [['user_id' => 10, 'max_score' => 40], ['user_id' => 12, 'max_score' => 78]]);
    }

    public function _testGetScoresFromTreeNode(array $expected, array $actual)
    {
        $learningPath = new LearningPath();
        $learningPath->setId(15);
        $contentObjectPublication = $this->createContentObjectPublication($learningPath);
        $tree = new Tree();
        $treeNode = new TreeNode($tree, $learningPath);

        $trackingServiceMock = $this->getMockBuilder(TrackingService::class)
            ->disableOriginalConstructor()->getMock();
        $this->trackingServiceBuilderServiceMock->expects($this->once())
            ->method('buildTrackingServiceForPublication')->with($contentObjectPublication)
            ->will($this->returnValue($trackingServiceMock));
        $trackingServiceMock->expects($this->once())
            ->method('getLearningPathAttemptsWithUser')->with($learningPath, $treeNode)
            ->will($this->returnValue($actual));

        $this->assertEquals($expected, $this->getScores($contentObjectPublication, $treeNode));
    }

    /**
     * @param ContentObject $contentObject
     *
     * @return ContentObjectPublication
     */
    protected function createContentObjectPublication(ContentObject $contentObject): ContentObjectPublication
    {
        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(46);
        $contentObjectPublication->set_course_id(42);
        $contentObjectPublication->setContentObject($contentObject);
        $contentObjectPublication->set_content_object_id($contentObject->getId());
        $contentObjectPublication->set_tool('LearningPath');
        return $contentObjectPublication;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param TreeNode $treeNode
     *
     * @return array
     * @throws \Exception
     */
    protected function getScores(ContentObjectPublication $contentObjectPublication, TreeNode $treeNode): array
    {
        $userScores = $this->learningPathTrackingScoreService->getScoresFromTreeNode($contentObjectPublication, $treeNode);

        $scores = array();
        foreach ($userScores as $userId => $gradeScore)
        {
            $scores[$userId] = $gradeScore->getValue();
        }
        return $scores;
    }
}