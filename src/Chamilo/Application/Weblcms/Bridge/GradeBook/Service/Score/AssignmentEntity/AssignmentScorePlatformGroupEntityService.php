<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\GradeBookItemScoreServiceInterface;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as PlatformGroupDataManager;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssignmentScorePlatformGroupEntityService implements GradeBookItemScoreServiceInterface
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
        /** @var ResultSet $platformGroups */
        $platformGroups = PlatformGroupDataManager::retrieve_publication_target_platform_groups(
            $publication->getId(), $publication->get_course_id()
        );
        $userMap = array();
        $groups = array();
        while ($platformGroup = $platformGroups->next_result())
        {
            $groupScore = $this->assignmentService->getLastScoreForContentObjectPublicationEntityTypeAndId($publication, 2, $platformGroup->getId());
            $platformGroupMemberIds = $platformGroup->get_users(true, true);
            foreach ($platformGroupMemberIds as $memberId)
            {
                if (!array_key_exists($memberId, $userMap))
                {
                    $userMap[$memberId] = array();
                }
                $userMap[$memberId][] = $platformGroup;
            }
            $groups[$platformGroup->getId()] = $groupScore;
        }
        $scores = array();

        foreach ($userIds as $userId)
        {
            $score = null;

            $userGroups = $userMap[$userId];
            foreach ($userGroups as $group)
            {
                $groupScore = $groups[$group->getId()];
                if (!is_null($groupScore))
                {
                    if (is_null($score) || $groupScore > $score)
                    {
                        $score = (float) $groupScore;
                    }
                }
            }
            $scores[] = ['user_id' => (int) $userId, 'score' => $score];
        }

        return $scores;
    }
}