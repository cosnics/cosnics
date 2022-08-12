<?php

namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Test\Unit\Service\Score;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssessmentScoreService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\ScoreDataService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 * Tests the AssessmentScoreService
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssessmentScoreServiceTest extends ChamiloTestCase
{
    /**
     * @var AssessmentScoreService
     */
    protected $assessmentScoreService;

    /**
     * @var ScoreDataService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $scoreDataServiceMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->scoreDataServiceMock = $this->getMockBuilder(ScoreDataService::class)
            ->disableOriginalConstructor()->getMock();
        $this->assessmentScoreService = new AssessmentScoreService($this->scoreDataServiceMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->scoreDataServiceMock);
        unset($this->assessmentScoreService);
    }

    public function testAssessmentScoresEmpty()
    {
        $this->mockGetAssessmentAttempts([], []);
    }

    public function testAssessmentScoresUser()
    {
        $this->mockGetAssessmentAttempts(
            [['user_id' => 25, 'total_score' => 47], ['user_id' => 30, 'total_score' => 60], ['user_id' => 102, 'total_score' => 90]],
            [25 => 47, 30 => 60, 102 => 90]
        );
    }

    public function testAssessmentScoresUserMultipleAttempts()
    {
        $this->mockGetAssessmentAttempts(
            [['user_id' => 25, 'total_score' => 47], ['user_id' => 30, 'total_score' => 60], ['user_id' => 102, 'total_score' => 90], ['user_id' => 30, 'total_score' => 65]],
            [25 => 47, 30 => 65, 102 => 90]
        );
    }

    public function testAssessmentScoresUserMultipleAttemptsDifferentOrder()
    {
        $this->mockGetAssessmentAttempts(
            [['user_id' => 25, 'total_score' => 47], ['user_id' => 30, 'total_score' => 65], ['user_id' => 102, 'total_score' => 90], ['user_id' => 30, 'total_score' => 60]],
            [25 => 47, 30 => 65, 102 => 90]
        );
    }

    protected function mockGetAssessmentAttempts(array $retrievedScores, array $finalScores)
    {
        $contentObjectPublication = $this->createContentObjectPublication();
        $this->scoreDataServiceMock->expects($this->once())
            ->method('getAssessmentAttempts')->with($contentObjectPublication)
            ->will($this->returnValue(new ArrayResultSet($retrievedScores)));
        $this->assertEquals($finalScores, $this->assessmentScoreService->getScores($contentObjectPublication));
    }

    /**
     * @return ContentObjectPublication
     */
    protected function createContentObjectPublication(): ContentObjectPublication
    {
        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(13);
        $contentObjectPublication->set_course_id(5);
        $contentObjectPublication->set_content_object_id(77);
        $contentObjectPublication->set_tool('Assessment');
        return $contentObjectPublication;
    }
}