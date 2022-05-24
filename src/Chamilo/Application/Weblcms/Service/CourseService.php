<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseSettingsServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\RightsServiceInterface;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\CourseRepositoryInterface;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Service class to manage weblcms courses.
 *
 * @package application\weblcms
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseService implements CourseServiceInterface
{

    /**
     * The course repository
     *
     * @var CourseRepositoryInterface
     */
    private $courseRepository;

    /**
     * The course settings service
     *
     * @var CourseSettingsServiceInterface
     */
    private $courseSettingsService;

    /**
     * The rights service
     *
     * @var RightsServiceInterface
     */
    private $rightsService;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * Constructor
     *
     * @param CourseRepositoryInterface $courseRepository
     * @param CourseSettingsServiceInterface $courseSettingsService
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        CourseRepositoryInterface $courseRepository,
        CourseSettingsServiceInterface $courseSettingsService, UserService $userService
    )
    {
        $this->courseRepository = $courseRepository;
        $this->courseSettingsService = $courseSettingsService;
        $this->userService = $userService;
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Sets the rights service
     *
     * @param RightsServiceInterface $rightsService
     */
    public function setRightsService(RightsServiceInterface $rightsService)
    {
        $this->rightsService = $rightsService;
    }

    /**
     * **************************************************************************************************************
     * Course Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns a course by a given id
     *
     * @param int $courseId
     *
     * @return Course
     */
    public function getCourseById($courseId)
    {
        return $this->courseRepository->findCourse($courseId);
    }

    /**
     * Returns a course for a given user by a given visual code.
     * Checks if the course exists and the user has
     * the correct rights for the course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $visualCode
     *
     * @return Course
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getCourseByVisualCodeForUser(User $user, $visualCode)
    {
        $course = $this->courseRepository->findCourseByVisualCode($visualCode);
        if (!$course)
        {
            throw new ObjectNotExistException('Course', $visualCode);
        }

        if (!$this->rightsService->canUserViewCourse($user, $course))
        {
            throw new NotAllowedException();
        }

        return $course;
    }

    /**
     * Returns the courses by id
     *
     * @param array $courseIds
     *
     * @return Course[]
     */
    public function getCoursesByIds(array $courseIds)
    {
        return $this->courseRepository->findCourses($courseIds);
    }

    /**
     *
     * @param int $courseTypeId
     *
     * @return Course[]
     */
    public function getCoursesByCourseTypeId(int $courseTypeId): array
    {
        return $this->courseRepository->findCoursesByCourseTypeId($courseTypeId);
    }

    /**
     * Returns all the courses for the given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function getAllCoursesForUser(User $user)
    {
        return $this->courseRepository->findCoursesForUser($user);
    }

    /**
     * Returns every course in which a user is subscribed and that is visible
     *
     * @param User $user
     *
     * @return Course[]
     */
    public function getVisibleCoursesForUser(User $user)
    {
        $coursesWhereUserIsTeacher = $this->getCoursesWhereUserIsTeacher($user);
        $coursesWhereUserIsStudent = $this->getCoursesWhereUserIsStudent($user);

        $visibleCoursesWhereUserIsStudent = [];

        foreach ($coursesWhereUserIsStudent as $courseWhereUserIsStudent)
        {
            if ($this->courseSettingsService->isCourseVisible($courseWhereUserIsStudent))
            {
                $visibleCoursesWhereUserIsStudent[] = $courseWhereUserIsStudent;
            }
        }

        return array_merge($coursesWhereUserIsTeacher, $visibleCoursesWhereUserIsStudent);
    }

    /**
     * Returns the courses for the given user where the user is a teacher
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function getCoursesWhereUserIsTeacher(User $user)
    {
        return $this->courseRepository->findCoursesWhereUserIsTeacher($user);
    }

    /**
     * Returns the courses for the given user where the user is a student
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function getCoursesWhereUserIsStudent(User $user)
    {
        return $this->courseRepository->findCoursesWhereUserIsStudent($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType $courseType
     *
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course[]
     */
    public function getCoursesInCourseTypeForUser(User $user, CourseType $courseType)
    {
        $subscribedCourseIds = $this->getSubscribedCourseIdsForUser($user);

        return $this->courseRepository->findCoursesByCourseTypeAndSubscribedCourseIds(
            $courseType, $subscribedCourseIds
        );
    }

    /**
     * @param array $groupIdentifiers
     *
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course[]
     */
    public function getCoursesWhereAtLeastOneGroupIsDirectlySubscribed(array $groupIdentifiers = [])
    {
        return $this->courseRepository->findCoursesWhereAtLeastOneGroupIsDirectlySubscribed($groupIdentifiers);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $groupIdentifiers
     *
     * @return bool
     */
    public function isAtLeastOneGroupDirectlySubscribed(Course $course, array $groupIdentifiers = [])
    {
        return $this->courseRepository->countCourseEntityRelationsByCourseAndEntityTypeAndIdentifiers(
                $course, CourseEntityRelation::ENTITY_TYPE_GROUP, $groupIdentifiers
            ) > 0;
    }

    /**
     * **************************************************************************************************************
     * Course Subscription Functionality *
     * **************************************************************************************************************
     */

    /**
     * Checks if the user is subscribed to a course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isUserSubscribedToCourse(User $user, Course $course)
    {
        $courseUserSubscription = $this->courseRepository->findCourseUserSubscriptionByCourseAndUser(
            $course->getId(),
            $user->getId()
        );

        if ($courseUserSubscription)
        {
            return true;
        }

        $courseGroupSubscriptions = $this->courseRepository->findCourseGroupSubscriptionsByCourseAndGroups(
            $course->getId(),
            $user->get_groups(true)
        );

        if (count($courseGroupSubscriptions) > 0)
        {
            return true;
        }

        return false;
    }

    /**
     * Checks if the user is subscribed as a teacher in the course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isUserTeacherInCourse(User $user, Course $course)
    {
        return $this->isUserSubscribedToCourseWithStatus($user, $course);
    }

    /**
     * Checks if the user is subscribed as a student in the course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isUserStudentInCourse(User $user, Course $course)
    {
        return $this->isUserSubscribedToCourseWithStatus($user, $course, CourseEntityRelation::STATUS_STUDENT);
    }

    /**
     * Returns an array of users who are subscribed (directly or through groups) as a teacher in a given course
     *
     * @param Course $course
     *
     * @return User[]
     */
    public function getTeachersFromCourse(Course $course)
    {
        return $this->getUsersFromCourseByStatus($course, CourseEntityRelation::STATUS_TEACHER);
    }

    /**
     * Returns an array of users who are subscribed (directly or through groups) as a student in a given course
     *
     * @param Course $course
     *
     * @return User[]
     */
    public function getStudentsFromCourse(Course $course)
    {
        return $this->getUsersFromCourseByStatus($course);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return int[]
     */
    public function getAllUserIdsFromCourse(Course $course)
    {
        $userIds = [];

        $userRecords = $this->courseRepository->findAllUsersFromCourse($course);
        foreach ($userRecords as $userRecord)
        {
            $userIds[] = $userRecord[User::PROPERTY_ID];
        }

        return $userIds;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function getAllUsersFromCourse(Course $course)
    {
        return $this->courseRepository->findAllUsersFromCourse($course);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int[]
     */
    public function getSubscribedCourseIdsForUser(User $user)
    {
        return $this->courseRepository->findSubscribedCourseIdsForUser($user);
    }



    /**
     * **************************************************************************************************************
     * Tool Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the tool registration for a given tool
     *
     * @param string $toolName
     *
     * @return CourseTool
     */
    public function getToolRegistration($toolName)
    {
        return $this->courseRepository->findCourseToolByName($toolName);
    }

    /**
     * Returns the tools that a given user has access to in the course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return string[]
     */
    public function getToolsFromCourseForUser(User $user, Course $course)
    {
        $userTools = [];

        $toolRegistrations = $this->courseRepository->findToolRegistrations();
        foreach ($toolRegistrations as $toolRegistration)
        {
            if ($this->rightsService->canUserViewTool($user, $toolRegistration->get_name(), $course))
            {
                $userTools[] = $toolRegistration->get_name();
            }
        }

        return $userTools;
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Helper function to check if a user is subscribed to the course with a given status
     *
     * @param User $user
     * @param Course $course
     * @param int $status
     *
     * @return bool
     */
    protected function isUserSubscribedToCourseWithStatus(
        User $user, Course $course,
        $status = CourseEntityRelation::STATUS_TEACHER
    )
    {
        $courseUserSubscription = $this->courseRepository->findCourseUserSubscriptionByCourseAndUser(
            $course->getId(),
            $user->getId()
        );

        if ($courseUserSubscription && $courseUserSubscription->get_status() == $status)
        {
            return true;
        }

        $courseGroupSubscriptions = $this->courseRepository->findCourseGroupSubscriptionsByCourseAndGroups(
            $course->getId(),
            $user->get_groups(true)
        );

        foreach ($courseGroupSubscriptions as $courseGroupSubscription)
        {
            if ($courseGroupSubscription->get_status() == $status)
            {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param Course $course
     * @param int $status
     *
     * @return User[]
     */
    protected function getUsersFromCourseByStatus(Course $course, $status = CourseEntityRelation::STATUS_STUDENT)
    {
        $userIds = [];

        $directlySubscribedUsers = $this->courseRepository->findUsersByStatus($course->getId(), $status);
        foreach($directlySubscribedUsers as $directlySubscribedUser)
        {
            $userIds[] = $directlySubscribedUser[User::PROPERTY_ID];
        }

        $groups = $this->courseRepository->findDirectSubscribedGroupsByStatus($course->getId(), $status);

        foreach($groups as $group)
        {
            if (!$group instanceof Group)
            {
                $group = new Group($group);
            }

            $userIds = array_merge($userIds, $group->get_users(true, true));
        }

        if (count($userIds) == 0)
        {
            return [];
        }

        return $this->getUserService()->findUsers(
            new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $userIds)
        );
    }
}