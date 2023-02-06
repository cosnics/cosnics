<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\ScoreDataService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssessmentScoreService implements ScoreServiceInterface
{
    /**
     * @var ScoreDataService
     */
    protected $scoreDataService;

    /**
     * @param ScoreDataService $scoreDataService
     */
    public function __construct(ScoreDataService $scoreDataService)
    {
        $this->scoreDataService = $scoreDataService;
    }

    /**
     * @param ContentObjectPublication $publication
     *
     * @return GradeScoreInterface[]
     */
    public function getScores(ContentObjectPublication $publication): array
    {
        $attempts = $this->scoreDataService->getAssessmentAttempts($publication);

        /** @var GradeScore[] $scores */
        $scores = array();
        while ($attempt = $attempts->next_result())
        {
            $gradeScore = new GradeScore((float) $attempt['total_score']);
            $userId = $attempt['user_id'];
            $hasKey = array_key_exists($userId, $scores);
            if (!$hasKey || $gradeScore->hasPresedenceOver($scores[$userId]))
            {
                $scores[$userId] = $gradeScore;
            }
        }
        return $scores;
    }
}