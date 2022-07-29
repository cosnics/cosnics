<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\GradeBookItemScoreServiceInterface;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssignmentScoreCourseGroupEntityService implements GradeBookItemScoreServiceInterface
{
    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @param AssignmentService $assignmentService
     */
    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param array $userIds
     *
     * @return array
     */
    public function getScores(ContentObjectPublication $publication, array $userIds): array
    {
        $scores = array();
        foreach ($userIds as $userId)
        {
            $data_set = CourseGroupDataManager::retrieve_course_groups_from_user($userId, $publication->get_course_id());

            if ($data_set->is_empty())
            {
                $scores[] = ['user_id' => (int) $userId, 'score' => null];
                continue;
            }

            $score = null;
            while ($course_group = $data_set->next_result())
            {
                $courseGroupId = $course_group->get_id();
                $groupScore = $this->assignmentService->getLastScoreForContentObjectPublicationEntityTypeAndId($publication, 1, $courseGroupId);
                if (!is_null($groupScore))
                {
                    $groupScore = (float) $groupScore;

                    if (is_null($score) || $groupScore > $score)
                    {
                        $score = $groupScore;
                    }
                }
            }

            $scores[] = ['user_id' => (int) $userId, 'score' => $score];
        }
        return $scores;
    }
}