<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Course group service to help with the management of course groups
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupService
{
    /**
     * @var CourseGroupRepository
     */
    protected $courseGroupRepository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var CourseGroupDecoratorsManager
     */
    protected $courseGroupDecoratorsManager;

    /**
     * CourseGroupService constructor.
     *
     * @param CourseGroupRepository $courseGroupRepository
     * @param CourseGroupDecoratorsManager $courseGroupDecoratorsManager
     * @param UserService $userService
     */
    public function __construct(
        CourseGroupRepository $courseGroupRepository, CourseGroupDecoratorsManager $courseGroupDecoratorsManager,
        UserService $userService
    )
    {
        $this->courseGroupRepository = $courseGroupRepository;
        $this->courseGroupDecoratorsManager = $courseGroupDecoratorsManager;
        $this->userService = $userService;
    }

    /**
     * Counts the course groups in a given course
     *
     * @param int $courseId
     *
     * @return int
     */
    public function countCourseGroupsInCourse($courseId)
    {
        return $this->courseGroupRepository->countCourseGroupsInCourse($courseId);
    }

    public function getCourseGroupsInCourse(int $courseId)
    {
        return $this->courseGroupRepository->getCourseGroupsInCourse($courseId);
    }

    /**
     * @param CourseGroup $courseGroup
     * @param array $users
     * @param bool $recalculateMembers
     */
    public function subscribeUsersWithoutMaxCapacityCheck(
        CourseGroup $courseGroup, array $users, bool $recalculateMembers = true
    )
    {
        $currentMemberUserIds = $this->courseGroupRepository->getUserIdsDirectlySubscribedInGroup($courseGroup);

        foreach ($users as $user)
        {
            if (in_array($user->getId(), $currentMemberUserIds))
            {
                continue;
            }

            $this->createCourseGroupUserRelation($courseGroup, $user);
        }

        if ($recalculateMembers)
        {
            $this->recalculateMaxMembers($courseGroup);
        }
    }

    /**
     * @param CourseGroup $courseGroup
     * @param array $userIds
     * @param bool $recalculateMembers
     */
    public function subscribeUsersWithoutMaxCapacityCheckById(
        CourseGroup $courseGroup, array $userIds, bool $recalculateMembers = true
    )
    {
        $currentMemberUserIds = $this->courseGroupRepository->getUserIdsDirectlySubscribedInGroup($courseGroup);
        $userIdsToSubscribe = array_diff($userIds, $currentMemberUserIds);
        $usersToSubscribe = $this->userService->findUsersByIdentifiers($userIdsToSubscribe);

        foreach ($usersToSubscribe as $userToSubscribe)
        {
            $this->createCourseGroupUserRelation($courseGroup, $userToSubscribe);
        }

        if ($recalculateMembers)
        {
            $this->recalculateMaxMembers($courseGroup);
        }
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     */
    public function subscribeUserToCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $this->createCourseGroupUserRelation($courseGroup, $user);
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     */
    public function unsubscribeUserFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        if (!$this->courseGroupRepository->removeUserFromCourseGroup($courseGroup, $user))
        {
            throw new \RuntimeException(
                sprintf('The user %s could not be unsubscribed from group %s', $user->getId(), $courseGroup->getId())
            );
        }

        $this->courseGroupDecoratorsManager->unsubscribeUser($courseGroup, $user);
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return User[]
     */
    public function getMembersDirectlySubscribedInGroup(CourseGroup $courseGroup)
    {
        $userIds = $this->courseGroupRepository->getUserIdsDirectlySubscribedInGroup($courseGroup);

        return $this->userService->findUsersByIdentifiers($userIds);
    }

    /**
     * @return int[]
     */
    public function getUserIdsDirectlySubscribedInGroup(CourseGroup $courseGroup)
    {
        return $this->courseGroupRepository->getUserIdsDirectlySubscribedInGroup($courseGroup);
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return int
     */
    public function countMembersDirectlySubscribedInGroup(CourseGroup $courseGroup)
    {
        return $this->courseGroupRepository->countMembersDirectlySubscribedInGroup($courseGroup);
    }

    /**
     * @param int $courseGroupId
     *
     * @return bool|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|CourseGroup
     */
    public function getCourseGroupById(int $courseGroupId)
    {
        return $this->courseGroupRepository->getCourseGroupById($courseGroupId);
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     */
    protected function createCourseGroupUserRelation(Coursegroup $courseGroup, User $user)
    {
        $courseGroupRelation = new CourseGroupUserRelation();
        $courseGroupRelation->set_course_group($courseGroup->getId());
        $courseGroupRelation->set_user($user->getId());
        $courseGroupRelation->set_subscription_time(time());

        if (!$this->courseGroupRepository->createCourseGroupUserRelation($courseGroupRelation))
        {
            throw new \RuntimeException(
                sprintf('The user %s could not be subscribed in group %s', $user->getId(), $courseGroup->getId())
            );
        }

        $this->courseGroupDecoratorsManager->subscribeUser($courseGroup, $user);
    }

    /**
     * @param CourseGroup $courseGroup
     */
    public function recalculateMaxMembers(CourseGroup $courseGroup): void
    {
        $numberOfGroupMembers = $this->countMembersDirectlySubscribedInGroup($courseGroup);
        if ($courseGroup->get_max_number_of_members() < $numberOfGroupMembers)
        {
            $courseGroup->set_max_number_of_members($numberOfGroupMembers);

            if (!$this->courseGroupRepository->updateCourseGroup($courseGroup))
            {
                throw new \RuntimeException('Could not update course group %s', $courseGroup->getId());
            }
        }
    }

    /**
     * @param User $user
     * @param CourseGroup $courseGroup
     *
     * @return bool
     *
     * TODO: refactor the method more_subscriptions_allowed_for_user_in_group
     */
    public function canUserSelfSubscribeToGroup(User $user, CourseGroup $courseGroup)
    {
        if (!$courseGroup->is_self_registration_allowed())
        {
            return false;
        }

        if ($this->isUserSubscribedToCourseGroup($user, $courseGroup))
        {
            return false;
        }

        if ($this->isMaxNumberOfUsersReached($courseGroup))
        {
            return false;
        }

        return DataManager::more_subscriptions_allowed_for_user_in_group(
            $courseGroup->get_parent_id(),
            $user->getId()
        );
    }

    public function isUserSubscribedToCourseGroup(User $user, CourseGroup $courseGroup)
    {
        return $this->courseGroupRepository->retrieveUserSubscriptionInCourseGroup($user, $courseGroup) instanceof
            CourseGroupUserRelation;
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return bool
     */
    protected function isMaxNumberOfUsersReached(CourseGroup $courseGroup)
    {
        // Unlimited amount of users allowed
        if ($courseGroup->get_max_number_of_members() == 0)
        {
            return false;
        }

        return $this->countMembersDirectlySubscribedInGroup($courseGroup) >= $courseGroup->get_max_number_of_members();
    }

    /**
     * @param string $courseGroupName
     * @param int $courseId
     * @param int $parentGroupId
     *
     * @return CourseGroup
     */
    public function createCourseGroup(
        string $courseGroupName, int $courseId, int $parentGroupId = 0,
        string $description = '', int $maxMembers = 0, bool $selfRegistrationAllowed = false,
        bool $selfUnregistrationAllowed = false
    )
    {
        $courseGroup = new CourseGroup();
        $courseGroup->set_course_code($courseId);

        if (empty($parentGroupId))
        {
            $parentGroupId = $this->courseGroupRepository->getRootCourseGroup($courseId)->getId();
        }

        $courseGroup->set_parent_id($parentGroupId);
        $courseGroup->set_name($courseGroupName);
        $courseGroup->set_description($description);
        $courseGroup->set_max_number_of_members($maxMembers);
        $courseGroup->set_self_registration_allowed($selfRegistrationAllowed);
        $courseGroup->set_self_unregistration_allowed($selfUnregistrationAllowed);

        if (!$this->courseGroupRepository->createCourseGroup($courseGroup))
        {
            throw new \RuntimeException(
                sprintf('Could not create course group with name %s in course %s', $courseGroupName, $courseId)
            );
        }

        return $courseGroup;
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return CourseGroup
     */
    public function updateCourseGroup(CourseGroup $courseGroup)
    {
        if (!$this->courseGroupRepository->updateCourseGroup($courseGroup))
        {
            throw new \RuntimeException('Could not update course group with id ' . $courseGroup->getId());
        }

        return $courseGroup;
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return CourseGroup[]
     */
    public function getDirectChildrenFromGroup(CourseGroup $courseGroup)
    {
        return $courseGroup->get_children(false)->as_array();
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return CourseGroup[]
     */
    public function getNestedChildrenFromGroup(CourseGroup $courseGroup)
    {
        return $courseGroup->get_children(true)->as_array();
    }
}
