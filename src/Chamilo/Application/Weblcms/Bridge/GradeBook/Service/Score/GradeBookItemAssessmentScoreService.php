<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookItemAssessmentScoreService implements GradeBookItemScoreServiceInterface
{
    /**
     * @param ContentObjectPublication $publication
     * @param array $userIds
     *
     * @return array
     */
    public function getScores(ContentObjectPublication $publication, array $userIds): array
    {
        $scores = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($publication->getId())
        );

        $condition = new AndCondition($conditions);

        $results = WeblcmsTrackingDataManager::retrieve_assessment_attempts_with_user($condition);
        foreach ($results->as_array() as $result)
        {
            $userId = $result['user_id'];
            if (in_array($userId, $userIds))
            {
                if (!empty($scores[$userId]))
                {
                    $totalScore = (float) $result['total_score'];
                    if ($totalScore > $scores[$userId]['score'])
                    {
                        $scores[$userId]['score'] = $totalScore;
                    }
                }
                else
                {
                    $scores[] = ['user_id' => (int) $result['user_id'], 'score' => (float) $result['total_score']];
                }
            }
        }
        $scoresUserIds = array_column($scores, 'user_id');

        foreach ($userIds as $userId)
        {
            if (!in_array($userId, $scoresUserIds))
            {
                $scores[] = ['user_id' => (int) $userId, 'score' => null];
            }
        }

        return $scores;
    }
}