<?php

namespace Chamilo\Application\Weblcms\Course\OpenCourse\Service;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository\OpenCourseRepository;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Service to manage open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseService
{
    /**
     * @var OpenCourseRepository
     */
    protected $openCourseRepository;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserRoleServiceInterface
     */
    protected $userRoleService;

    /**
     * OpenCourseService constructor.
     *
     * @param OpenCourseRepository $openCourseRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserRoleServiceInterface $userRoleService
     */
    public function __construct(
        OpenCourseRepository $openCourseRepository, AuthorizationCheckerInterface $authorizationChecker,
        UserRoleServiceInterface $userRoleService
    )
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
    public function retrieveOpenCourses(
        User $user, Condition $condition = null, $offset = null, $count = null, $orderBy = array()
    )
    {
        if($this->authorizationChecker->isAuthorized($user, Manager::context(), 'manage_open_courses'))
        {
            return $this->openCourseRepository->findAllOpenCourses($condition, $offset, $count, $orderBy);
        }

        $roles = $this->userRoleService->getRolesForUser($user);
        return $this->openCourseRepository->findOpenCoursesByRoles($roles, $condition, $offset, $count, $orderBy);
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
        if($this->authorizationChecker->isAuthorized($user, Manager::context(), 'manage_open_courses'))
        {
            $this->openCourseRepository->countAllOpenCourses($condition);
        }

        $roles = $this->userRoleService->getRolesForUser($user);
        return $this->openCourseRepository->countOpenCoursesByRoles($roles, $condition);
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

}