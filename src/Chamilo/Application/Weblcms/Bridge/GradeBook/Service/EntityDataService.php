<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service;

use Chamilo\Application\Weblcms\Storage\DataManager as PlatformGroupDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EntityDataService
{
    /**
     * @var CourseGroupRepository
     */
    protected $courseGroupRepository;

    /**
     * @param CourseGroupRepository $courseGroupRepository
     */
    public function __construct(CourseGroupRepository $courseGroupRepository)
    {
        $this->courseGroupRepository = $courseGroupRepository;
    }

    /**
     * @param int $courseId
     *
     * @return array
     */
    public function getCourseGroupUserEntitiesRecursiveFromCourse(int $courseId): array
    {
        $courseGroups = $this->toAssociativeArray(
            $this->courseGroupRepository->getCourseGroupsInCourse($courseId)
        );

        $courseGroupIdsRecursive = [];
        foreach ($courseGroups as $courseGroup)
        {
            $courseGroupId = $courseGroup->getId();
            $groupIdAndAncestorIds = [$courseGroupId];
            $curCourseGroup = $courseGroup;
            while ($curCourseGroup->get_parent_id() != 0)
            {
                $parentId = $curCourseGroup->get_parent_id();
                $groupIdAndAncestorIds[] = $parentId;
                $curCourseGroup = $courseGroups[$parentId];
            }
            $courseGroupIdsRecursive[$courseGroupId] = $groupIdAndAncestorIds;
        }

        return $this->getUserEntitiesFromCourseGroupsRecursive(array_keys($courseGroups), $courseGroupIdsRecursive);
    }

    /**
     * @param int[] $courseGroupIds
     * @param array $courseGroupIdsRecursive
     *
     * @return array
     */
    protected function getUserEntitiesFromCourseGroupsRecursive(array $courseGroupIds, array $courseGroupIdsRecursive)
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

    /**
     * @param DataClassIterator $courseGroups
     *
     * @return array
     */
    protected function toAssociativeArray(DataClassIterator $courseGroups): array
    {
        $groups = [];
        foreach ($courseGroups as $courseGroup)
        {
            $courseGroupId = $courseGroup->getId();
            $groups[$courseGroupId] = $courseGroup;

        }
        return $groups;
    }
}