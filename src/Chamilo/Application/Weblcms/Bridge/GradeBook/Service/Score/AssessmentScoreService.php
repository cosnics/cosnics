<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssessmentScoreService implements ScoreServiceInterface
{
    /**
     * @param ContentObjectPublication $publication
     *
     * @return array
     */
    public function getScores(ContentObjectPublication $publication): array
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($publication->getId())
        );

        $attempts = WeblcmsTrackingDataManager::retrieve_assessment_attempts_with_user($condition);

        $scores = array();
        while ($attempt = $attempts->next_result())
        {
            $totalScore = (float) $attempt['total_score'];
            $userId = $attempt['user_id'];
            $hasKey = array_key_exists($userId, $scores);
            if (!$hasKey || ($totalScore > $scores[$userId]))
            {
                $scores[$userId] = $totalScore;
            }
        }
        return $scores;
    }
}