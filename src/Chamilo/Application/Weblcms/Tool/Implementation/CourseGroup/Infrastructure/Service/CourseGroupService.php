<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

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

    /**
     * @param CourseGroup $courseGroup
     * @param array $userIds
     */
    public function subscribeUsersById(CourseGroup $courseGroup, array $userIds)
    {
        $currentMemberUserIds = $this->courseGroupRepository->getUserIdsDirectlySubscribedInGroup($courseGroup);
        $userIdsToSubscribe = array_diff($userIds, $currentMemberUserIds);
        $usersToSubscribe = $this->userService->findUsersByIdentifiers($userIdsToSubscribe);

        foreach ($usersToSubscribe as $userToSubscribe)
        {
            $this->createCourseGroupUserRelation($courseGroup, $userToSubscribe);
        }

        $numberOfGroupMembers = $this->countMembersDirectlySubscribedInGroup($courseGroup);
        if($courseGroup->get_max_number_of_members() < $numberOfGroupMembers)
        {
            $courseGroup->set_max_number_of_members($numberOfGroupMembers);

            if(!$this->courseGroupRepository->updateCourseGroup($courseGroup))
            {
                throw new \RuntimeException('Could not update course group %s', $courseGroup->getId());
            }
        }
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     */
    public function subscribeUserToCourseGroup(CourseGroup $courseGroup, User $user)
    {

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
     *
     * @return User[]
     */
    public function getMembersDirectlySubscribedInGroup(CourseGroup $courseGroup)
    {
        $userIds = $this->courseGroupRepository->getUserIdsDirectlySubscribedInGroup($courseGroup);

        return $this->userService->findUsersByIdentifiers($userIds);
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
}
