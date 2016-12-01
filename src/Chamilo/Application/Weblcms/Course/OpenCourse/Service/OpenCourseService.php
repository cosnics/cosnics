<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Service;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\OpenCourse\Service\Interfaces\OpenCourseServiceInterface;
use Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository\Interfaces\OpenCourseRepositoryInterface;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Service to manage open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseService implements OpenCourseServiceInterface
{

    /**
     *
     * @var OpenCourseRepositoryInterface
     */
    protected $openCourseRepository;

    /**
     *
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     *
     * @var UserRoleServiceInterface
     */
    protected $userRoleService;

    /**
     * OpenCourseService constructor.
     *
     * @param OpenCourseRepositoryInterface $openCourseRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserRoleServiceInterface $userRoleService
     */
    public function __construct(OpenCourseRepositoryInterface $openCourseRepository,
        AuthorizationCheckerInterface $authorizationChecker, UserRoleServiceInterface $userRoleService)
    {
        $this->openCourseRepository = $openCourseRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->userRoleService = $userRoleService;
    }

    /**
     * Retrieves the open courses for a given user
     *
     * @param User $user
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return RecordIterator
     */
    public function getOpenCourses(User $user, Condition $condition = null, $offset = null, $count = null, $orderBy = array())
    {
        if ($this->authorizationChecker->isAuthorized($user, Manager::context(), 'ManageOpenCourses'))
        {
            return $this->openCourseRepository->findAllOpenCourses($condition, $offset, $count, $orderBy);
        }

        $roles = $this->userRoleService->getRolesForUser($user);
        return $this->openCourseRepository->findOpenCoursesByRoles($roles, $condition, $offset, $count, $orderBy);
    }

    /**
     * Returns the closed courses
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return RecordIterator
     */
    public function getClosedCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = array())
    {
        return $this->openCourseRepository->findClosedCourses($condition, $offset, $count, $orderBy);
    }

    /**
     * Counts the open courses for the given user
     *
     * @param User $user
     * @param Condition $condition
     *
     * @return int
     */
    public function countOpenCourses(User $user, Condition $condition = null)
    {
        if ($this->authorizationChecker->isAuthorized($user, Manager::context(), 'ManageOpenCourses'))
        {
            return $this->openCourseRepository->countAllOpenCourses($condition);
        }

        $roles = $this->userRoleService->getRolesForUser($user);
        return $this->openCourseRepository->countOpenCoursesByRoles($roles, $condition);
    }

    /**
     * Counts the closed courses
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countClosedCourses(Condition $condition = null)
    {
        return $this->openCourseRepository->countClosedCourses($condition);
    }

    /**
     * Returns the roles for a given open course
     *
     * @param Course $course
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getRolesForOpenCourse(Course $course)
    {
        return $this->openCourseRepository->getRolesForOpenCourse($course);
    }

    /**
     * Attaches given roles to given courses by ids
     *
     * @param User $user
     * @param int[] $courseIds
     *
     * @param int[] $roleIds
     *
     * @throws \Exception
     */
    public function attachRolesToCoursesByIds(User $user, $courseIds = array(), $roleIds = array())
    {
        $this->authorizationChecker->checkAuthorization($user, Manager::context(), 'ManageOpenCourses');

        if (empty($roleIds) || empty($courseIds))
        {
            return;
        }

        foreach ($courseIds as $courseId)
        {
            foreach ($roleIds as $roleId)
            {
                $courseEntityRelation = new CourseEntityRelation();
                $courseEntityRelation->set_course_id($courseId);
                $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_ROLE);
                $courseEntityRelation->setEntityId($roleId);

                if (! $this->openCourseRepository->create($courseEntityRelation))
                {
                    throw new \Exception(
                        sprintf('Could not attach the role with id %s to the course with id %s', $roleId, $courseId));
                }
            }
        }
    }

    /**
     * Updates the roles for the courses
     *
     * @param User $user
     * @param int[] $courseIds
     *
     * @param int[] $roleIds
     *
     * @throws \Exception
     */
    public function updateRolesForCourses(User $user, $courseIds = array(), $roleIds = array())
    {
        $this->removeCoursesAsOpenCourse($user, $courseIds);
        $this->attachRolesToCoursesByIds($user, $courseIds, $roleIds);
    }

    /**
     * Removes a course as open course
     *
     * @param User $user
     * @param int[] $courseIds
     *
     * @throws \Exception
     */
    public function removeCoursesAsOpenCourse(User $user, $courseIds)
    {
        $this->authorizationChecker->checkAuthorization($user, Manager::context(), 'ManageOpenCourses');

        if (! $this->openCourseRepository->removeCoursesAsOpenCourse($courseIds))
        {
            throw new \Exception('Could not remove the courses as open course with ids ' . implode(', ', $courseIds));
        }
    }

    /**
     * Returns whether or not the course is open for the current user, based on his roles
     *
     * @param Course $course
     * @param User $user
     *
     * @return bool
     */
    public function isCourseOpenForUser(Course $course, User $user)
    {
        $courseRoles = $this->getRolesForOpenCourse($course);
        return $this->userRoleService->doesUserHasAtLeastOneRole($user, $courseRoles->as_array());
    }
}