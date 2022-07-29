<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\GradeBookItemScoreServiceInterface;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssignmentScoreUserEntityService implements GradeBookItemScoreServiceInterface
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
            $score = $this->assignmentService->getLastScoreForContentObjectPublicationEntityTypeAndId($publication, 0, $userId);
            if (!is_null($score))
            {
                $score = (float) $score;
            }
            $scores[] = ['user_id' => (int) $userId, 'score' => $score];
        }
        return $scores;
    }
}