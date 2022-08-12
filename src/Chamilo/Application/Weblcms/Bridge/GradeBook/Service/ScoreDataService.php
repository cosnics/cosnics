<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as PlatformGroupDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ScoreDataService
{
    /**
     * @param ContentObjectPublication $publication
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\RecordResultSet
     */
    public function getAssessmentAttempts(ContentObjectPublication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($publication->getId())
        );
        return WeblcmsTrackingDataManager::retrieve_assessment_attempts_with_user($condition);
    }

    /**
     * @param int[] $courseGroupIds
     * @param array $courseGroupIdsRecursive
     *
     * @return array
     */
    public function getUserEntitiesFromCourseGroupsRecursive(array $courseGroupIds, array $courseGroupIdsRecursive)
    {
        $courseGroupUserRelations = CourseGroupDataManager::retrieve_course_group_users_from_course_groups($courseGroupIds);
        return $this->getUserEntitiesFromCourseGroupUserRelations($courseGroupUserRelations, $courseGroupIdsRecursive);
    }

    /**
     * @param int $platformGroupId
     *
     * @return array
     */
    public function getUserEntitiesFromPlatformGroup(int $platformGroupId)
    {
        /** @var Group $group */
        $group = PlatformGroupDataManager::retrieve_by_id(Group::class_name(), $platformGroupId);
        return $group->get_users(true, true);
    }

    /**
     * @param ResultSet $courseGroupUserRelations
     * @param array $courseGroupIdsRecursive
     *
     * @return array
     */
    protected function getUserEntitiesFromCourseGroupUserRelations(ResultSet $courseGroupUserRelations, array $courseGroupIdsRecursive): array
    {
        $userEntities = array();

        while ($relation = $courseGroupUserRelations->next_result())
        {
            $userId = $relation->get_user();
            $relationCourseGroupId = $relation->get_course_group();
            $courseGroupIds = $courseGroupIdsRecursive[$relationCourseGroupId];

            foreach ($courseGroupIds as $courseGroupId)
            {
                if (!array_key_exists($courseGroupId, $userEntities))
                {
                    $userEntities[$courseGroupId] = [];
                }
                $userEntities[$courseGroupId][] = $userId;
            }
        }

        return array_map(array_unique, $userEntities);
    }
}