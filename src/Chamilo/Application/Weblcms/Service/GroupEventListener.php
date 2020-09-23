<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Core\Group\Service\GroupEventListenerInterface;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Service\GroupEventListenerInterface
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupEventListener implements GroupEventListenerInterface
{
    /**
     * @var \Chamilo\Core\Group\Service\GroupsTreeTraverser
     */
    protected $groupsTreeTraverser;

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    protected $courseService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\RightsService
     */
    protected $rightsService;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService
     */
    protected $courseGroupService;

    /**
     * GroupEventListener constructor.
     *
     * @param \Chamilo\Core\Group\Service\GroupsTreeTraverser $groupsTreeTraverser
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Chamilo\Application\Weblcms\Service\RightsService $rightsService
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService $courseGroupService
     */
    public function __construct(
        GroupsTreeTraverser $groupsTreeTraverser,
        CourseService $courseService,
        RightsService $rightsService,
        CourseGroupService $courseGroupService
    )
    {
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->courseService = $courseService;
        $this->rightsService = $rightsService;
        $this->courseGroupService = $courseGroupService;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterCreate(Group $group)
    {
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int[] $subGroupIds
     * @param int[] $impactedUserIds
     *
     * @throws \Exception
     */
    public function afterDelete(Group $group, array $subGroupIds = [], array $impactedUserIds = [])
    {
        $parentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($group, true);
        $allGroupIds = array_merge($parentGroupIds, $subGroupIds);

        $deleteGroupIds = $subGroupIds;
        $deleteGroupIds[] = $group->getId();

        $this->handleCoursesForRemovalOfUsers($allGroupIds, $impactedUserIds, $deleteGroupIds);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $oldParentGroup
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $newParentGroup
     *
     * @throws \Exception
     */
    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup)
    {
        $oldParentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($oldParentGroup, true);
        $subGroupIds = $this->groupsTreeTraverser->findSubGroupIdentifiersForGroup($group, true);

        $deleteGroupIds = $subGroupIds;
        $deleteGroupIds[] = $group->getId();

        $impactedUserIds = $this->groupsTreeTraverser->findUserIdentifiersForGroup($group, true, true);
        $newParentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($newParentGroup, true);

        $courses = $this->courseService->getCoursesWhereAtLeastOneGroupIsDirectlySubscribed($oldParentGroupIds);
        foreach ($courses as $course)
        {
            if($this->courseService->isAtLeastOneGroupDirectlySubscribed($course, $newParentGroupIds))
            {
                continue;
            }

            $courseUsersIds = $this->courseService->getAllUserIdsFromCourse($course);
            $actualDeletedCourseUserIds = array_diff($impactedUserIds, $courseUsersIds);

            $this->deleteUsersAndGroupsFromCourse($course, $actualDeletedCourseUserIds, $deleteGroupIds);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function afterSubscribe(Group $group, User $user)
    {
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Exception
     */
    public function afterUnsubscribe(Group $group, User $user)
    {
        $parentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($group, true);
        $this->handleCoursesForRemovalOfUsers($parentGroupIds, [$user->getId()]);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int[] $impactedUserIds
     *
     * @throws \Exception
     */
    public function afterEmptyGroup(Group $group, array $impactedUserIds = [])
    {
        $parentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($group, true);

        $this->handleCoursesForRemovalOfUsers($parentGroupIds, $impactedUserIds);
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
    )
    {
        $courses = $this->courseService->getCoursesWhereAtLeastOneGroupIsDirectlySubscribed($allGroupIds);
        foreach ($courses as $course)
        {
            $courseUsersIds = $this->courseService->getAllUserIdsFromCourse($course);
            $actualDeletedCourseUserIds = array_diff($impactedUserIds, $courseUsersIds);

            $this->deleteUsersAndGroupsFromCourse($course, $actualDeletedCourseUserIds, $deletedGroupIds);
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $removedUserIds
     * @param array $removedGroupIds
     *
     * @throws \Exception
     */
    protected function deleteUsersAndGroupsFromCourse(
        Course $course, array $removedUserIds = [], array $removedGroupIds = []
    )
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
    }
}

