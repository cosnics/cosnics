<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Core\Group\Service\GroupEventListenerInterface;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class GroupEventListener implements GroupEventListenerInterface
{
    protected CourseGroupService $courseGroupService;

    protected CourseService $courseService;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected RightsService $rightsService;

    public function __construct(
        GroupsTreeTraverser $groupsTreeTraverser, CourseService $courseService, RightsService $rightsService,
        CourseGroupService $courseGroupService
    )
    {
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->courseService = $courseService;
        $this->rightsService = $rightsService;
        $this->courseGroupService = $courseGroupService;
    }

    public function afterCreate(Group $group): bool
    {
        return true;
    }

    /**
     * @param int[] $subGroupIds
     * @param int[] $impactedUserIds
     *
     * @throws \Exception
     */
    public function afterDelete(Group $group, array $subGroupIds = [], array $impactedUserIds = []): bool
    {
        $parentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($group);
        $allGroupIds = array_merge($parentGroupIds, $subGroupIds);

        $deleteGroupIds = $subGroupIds;
        $deleteGroupIds[] = $group->getId();

        return $this->handleCoursesForRemovalOfUsers($allGroupIds, $impactedUserIds, $deleteGroupIds);
    }

    /**
     * @param int[] $impactedUserIds
     *
     * @throws \Exception
     */
    public function afterEmptyGroup(Group $group, array $impactedUserIds = []): bool
    {
        $parentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($group);

        return $this->handleCoursesForRemovalOfUsers($parentGroupIds, $impactedUserIds);
    }

    /**
     * @throws \Exception
     */
    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup): bool
    {
        $oldParentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($oldParentGroup);
        $subGroupIds = $this->groupsTreeTraverser->findSubGroupIdentifiersForGroup($group, true);

        $deleteGroupIds = $subGroupIds;
        $deleteGroupIds[] = $group->getId();

        $impactedUserIds = $this->groupsTreeTraverser->findUserIdentifiersForGroup($group, true, true);
        $newParentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($newParentGroup);

        $courses = $this->courseService->getCoursesWhereAtLeastOneGroupIsDirectlySubscribed($oldParentGroupIds);
        foreach ($courses as $course)
        {
            if ($this->courseService->isAtLeastOneGroupDirectlySubscribed($course, $newParentGroupIds))
            {
                continue;
            }

            $courseUsersIds = $this->courseService->getAllUserIdsFromCourse($course);
            $actualDeletedCourseUserIds = array_diff($impactedUserIds, $courseUsersIds);

            $this->deleteUsersAndGroupsFromCourse($course, $actualDeletedCourseUserIds, $deleteGroupIds);
        }

        return true;
    }

    public function afterSubscribe(Group $group, User $user): bool
    {
        return true;
    }

    /**
     * @throws \Exception
     */
    public function afterUnsubscribe(Group $group, User $user): bool
    {
        $parentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($group);

        return $this->handleCoursesForRemovalOfUsers($parentGroupIds, [$user->getId()]);
    }

    public function afterUpdate(Group $group): bool
    {
        return true;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $removedUserIds
     * @param array $removedGroupIds
     *
     * @return bool
     * @throws \Exception
     */
    protected function deleteUsersAndGroupsFromCourse(
        Course $course, array $removedUserIds = [], array $removedGroupIds = []
    ): bool
    {
        if (!empty($removedUserIds))
        {
            $this->rightsService->removeUsersFromRightsByIds($course, $removedUserIds);
            $this->courseGroupService->removeUsersFromAllCourseGroupsByIds($course, $removedUserIds);
        }

        if (!empty($removedGroupIds))
        {
            $this->rightsService->removeGroupsFromRightsByIds($course, $removedGroupIds);
        }

        return true;
    }

    /**
     * Handles the courses for the impacted users that where scheduled for removal
     *
     * @param int[] $allGroupIds (includes every parent and every child group)
     * @param int[] $impactedUserIds
     * @param int[] $deletedGroupIds
     *
     * @throws \Exception
     */
    protected function handleCoursesForRemovalOfUsers(
        array $allGroupIds = [], array $impactedUserIds = [], array $deletedGroupIds = []
    ): bool
    {
        $courses = $this->courseService->getCoursesWhereAtLeastOneGroupIsDirectlySubscribed($allGroupIds);

        foreach ($courses as $course)
        {
            $courseUsersIds = $this->courseService->getAllUserIdsFromCourse($course);
            $actualDeletedCourseUserIds = array_diff($impactedUserIds, $courseUsersIds);

            $this->deleteUsersAndGroupsFromCourse($course, $actualDeletedCourseUserIds, $deletedGroupIds);
        }

        return true;
    }
}

