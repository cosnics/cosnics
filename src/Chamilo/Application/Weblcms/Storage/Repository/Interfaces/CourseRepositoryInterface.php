<?php
namespace Chamilo\Application\Weblcms\Storage\Repository\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * Interface to describe the necessary functions needed from the CourseEntityRepository
 * 
 * @package application\bamaflex;
 * @author Tom Goethals - Hogeschool Gent
 */
interface CourseRepositoryInterface
{

    /**
     * Returns a course by a given id
     * 
     * @param int $courseId
     *
     * @return Course
     */
    public function findCourse($courseId);

    /**
     * Returns a course by a given visual code
     * 
     * @param string $visualCode
     *
     * @return Course
     */
    public function findCourseByVisualCode($visualCode);

    /**
     * Returns courses with an array of course id's.
     * 
     * @param array $courseIds
     *
     * @return Course[]
     */
    public function findCourses(array $courseIds);

    /**
     * @param int $courseTypeId
     * @return Course[]
     */
    public function findCoursesByCourseTypeId(int $courseTypeId): array;

    /**
     * Returns Courses with a given set of parameters
     * 
     * @param DataClassRetrievesParameters $retrievesParameters
     *
     * @return Course[]
     */
    public function findCoursesByParameters(DataClassRetrievesParameters $retrievesParameters);

    /**
     * Returns courses where a user is subscribed
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function findCoursesForUser(User $user);

    /**
     * Returns courses where a user is subscribed as a teacher
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function findCoursesWhereUserIsTeacher(User $user);

    /**
     * Returns courses where a user is subscribed as a student
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function findCoursesWhereUserIsStudent(User $user);

    /**
     * Returns the course user subscriptions by a given course and user
     * 
     * @param int $courseId
     * @param int $userId
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation
     */
    public function findCourseUserSubscriptionByCourseAndUser($courseId, $userId);

    /**
     * Returns the course group subscriptions by a given course and groups
     * 
     * @param int $courseId
     * @param int[] $groupIds
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation[]
     */
    public function findCourseGroupSubscriptionsByCourseAndGroups($courseId, $groupIds);

    /**
     * Finds a course tool registration by a given tool name
     * 
     * @param string $toolName
     *
     * @return CourseTool
     */
    public function findCourseToolByName($toolName);

    /**
     * Finds the tool registrations
     * 
     * @return CourseTool[]
     */
    public function findToolRegistrations();

    /**
     * Finds courses with his titular and settings with given retrieve parameters
     * 
     * @param Condition $condition
     *
     * @return Course[]
     */
    public function findCoursesWithTitularAndCourseSettings(Condition $condition = null);

    /**
     * Returns all users subscribed to course by status
     * 
     * @param $courseId
     * @param $status
     * @return ResultSet
     */
    public function findUsersByStatus($courseId, $status);

    /**
     * Returns all groups directly subscribed to course by status
     * 
     * @param $courseId
     * @param $status
     * @return ResultSet
     */
    public function findDirectSubscribedGroupsByStatus($courseId, $status);
}