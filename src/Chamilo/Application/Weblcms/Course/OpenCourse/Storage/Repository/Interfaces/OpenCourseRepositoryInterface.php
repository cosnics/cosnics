<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Repository to manage the data open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface OpenCourseRepositoryInterface extends DataManagerRepositoryInterface
{

    /**
     * Retrieves the open courses for the given user roles
     *
     * @param Role[]   $roles
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return DataClassIterator
     */
    public function findOpenCoursesByRoles($roles = [], Condition $condition = null, $offset = null, $count = null, $orderBy = []);

    /**
     * Retrieves the open courses
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return DataClassIterator
     */
    public function findAllOpenCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = []);

    /**
     * Retrieves the courses that are not open
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return DataClassIterator
     */
    public function findClosedCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = []);

    /**
     * Counts the open courses by the given user roles
     *
     * @param Role[]   $roles
     * @param Condition $condition
     *
     * @return int
     */
    public function countOpenCoursesByRoles($roles = [], Condition $condition = null);

    /**
     * Counts all the open courses
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countAllOpenCourses(Condition $condition = null);

    /**
     * Counts the courses that are not open
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
     * Removes a course as an open course
     *
     * @param int[] $courseIds
     *
     * @return bool
     */
    public function removeCoursesAsOpenCourse($courseIds);
}