<?php
namespace Chamilo\Application\Weblcms\EventDispatcher\Subscriber;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\RightsService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupDeleteEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupEmptyEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupMoveEvent;
use Chamilo\Core\Group\EventDispatcher\Event\AfterGroupUnsubscribeEvent;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Application\Weblcms\EventDispatcher\Subscriber
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupEventSubscriber implements EventSubscriberInterface
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

    /**
     * @throws \Exception
     */
    public function afterDelete(AfterGroupDeleteEvent $afterGroupDeleteEvent): bool
    {
        $group = $afterGroupDeleteEvent->getGroup();
        $subGroupIdentifiers = $afterGroupDeleteEvent->getSubGroupIdentifiers();

        $parentGroupIds = $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($group);
        $allGroupIds = array_merge($parentGroupIds, $subGroupIdentifiers);

        $deleteGroupIds = $subGroupIdentifiers;
        $deleteGroupIds[] = $group->getId();

        return $this->handleCoursesForRemovalOfUsers(
            $allGroupIds, $afterGroupDeleteEvent->getImpactUserIdentifiers(), $deleteGroupIds
        );
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
    public function afterMove(AfterGroupMoveEvent $afterGroupMoveEvent): bool
    {
        $group = $afterGroupMoveEvent->getGroup();

        $oldParentGroupIds =
            $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($afterGroupMoveEvent->getOldParentGroup());
        $subGroupIds = $this->groupsTreeTraverser->findSubGroupIdentifiersForGroup($group, true);

        $deleteGroupIds = $subGroupIds;
        $deleteGroupIds[] = $group->getId();

        $impactedUserIds = $this->groupsTreeTraverser->findUserIdentifiersForGroup($group, true, true);
        $newParentGroupIds =
            $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($afterGroupMoveEvent->getNewParentGroup());

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

    /**
     * @throws \Exception
     */
    public function afterUnsubscribe(AfterGroupUnsubscribeEvent $afterGroupUnsubscribeEvent): bool
    {
        $parentGroupIds =
            $this->groupsTreeTraverser->findParentGroupIdentifiersForGroup($afterGroupUnsubscribeEvent->getGroup());

        return $this->handleCoursesForRemovalOfUsers($parentGroupIds, [$afterGroupUnsubscribeEvent->getUser()->getId()]
        );
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

    public static function getSubscribedEvents(): array
    {
        return [
            AfterGroupDeleteEvent::class => 'afterDelete',
            AfterGroupEmptyEvent::class => 'afterEmpty',
            AfterGroupMoveEvent::class => 'afterMove',
            AfterGroupUnsubscribeEvent::class => 'afterUnsubscribe'
        ];
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

