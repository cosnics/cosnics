<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Service\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Service to manage open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface OpenCourseServiceInterface
{

    /**
     * Retrieves the open courses for a given user
     *
     * @param User $user
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return DataClassIterator
     */
    public function getOpenCourses(User $user, Condition $condition = null, $offset = null, $count = null, $orderBy = array());

    /**
     * Returns the closed courses
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return DataClassIterator
     */
    public function getClosedCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = array());

    /**
     * Counts the open courses for the given user
     *
     * @param User $user
     * @param Condition $condition
     *
     * @return int
     */
    public function countOpenCourses(User $user, Condition $condition = null);

    /**
     * Counts the closed courses
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countClosedCourses(Condition $condition = null);

    /**
     * Returns the roles for a given open course
     *
     * @param Course $course
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getRolesForOpenCourse(Course $course);

    /**
     * Attaches given roles to given courses by ids
     *
     * @param User $user
     * @param int[] $courseIds
     *
     * @param int[] $roleIds
     *
     * @return
     *
     */
    public function attachRolesToCoursesByIds(User $user, $courseIds = array(), $roleIds = array());

    /**
     * Updates the roles for the courses
     *
     * @param User $user
     * @param int[] $courseIds
     *
     * @param int[] $roleIds
     *
     * @return
     *
     */
    public function updateRolesForCourses(User $user, $courseIds = array(), $roleIds = array());

    /**
     * Removes a course as open course
     *
     * @param User $user
     * @param int[] $courseIds
     *
     * @return
     *
     */
    public function removeCoursesAsOpenCourse(User $user, $courseIds);

    /**
     * Returns whether or not the course is open for the current user, based on his roles
     *
     * @param Course $course
     * @param User $user
     *
     * @return bool
     */
    public function isCourseOpenForUser(Course $course, User $user);
}