<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

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
     * @return RecordIterator
     */
    public function findOpenCoursesByRoles(
        $roles = array(), Condition $condition = null, $offset = null, $count = null, $orderBy = array()
    );

    /**
     * Retrieves the open courses
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return RecordIterator
     */
    public function findAllOpenCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = array());

    /**
     * Retrieves the courses that are not open
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return RecordIterator
     */
    public function findClosedCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = array());

    /**
     * Counts the open courses by the given user roles
     *
     * @param Role[]   $roles
     * @param Condition $condition
     *
     * @return int
     */
    public function countOpenCoursesByRoles($roles = array(), Condition $condition = null);

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
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
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