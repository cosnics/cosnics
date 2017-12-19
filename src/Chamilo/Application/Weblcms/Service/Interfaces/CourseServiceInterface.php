<?php
namespace Chamilo\Application\Weblcms\Service\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface class for CourseService
 * 
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseServiceInterface
{

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
    public function getCourseById($courseId);

    /**
     * @param int $courseTypeId
     * @return Course[]
     */
    public function getCoursesByCourseTypeId(int $courseTypeId): array;

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
    public function getCourseByVisualCodeForUser(User $user, $visualCode);

    /**
     * Returns the courses by id
     * 
     * @param array $courseIds
     *
     * @return Course[]
     */
    public function getCoursesByIds(array $courseIds);

    /**
     * Returns all the courses for the given user
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function getAllCoursesForUser(User $user);

    /**
     * Returns every course in which a user is subscribed and that is visible
     * 
     * @param User $user
     *
     * @return Course[]
     */
    public function getVisibleCoursesForUser(User $user);

    /**
     * Returns the courses for the given user where the user is a teacher
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function getCoursesWhereUserIsTeacher(User $user);

    /**
     * Returns the courses for the given user where the user is a student
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function getCoursesWhereUserIsStudent(User $user);

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
    public function isUserSubscribedToCourse(User $user, Course $course);

    /**
     * Checks if the user is subscribed as a teacher in the course
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isUserTeacherInCourse(User $user, Course $course);

    /**
     * Checks if the user is subscribed as a student in the course
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function isUserStudentInCourse(User $user, Course $course);

    /**
     * Returns an array of users who are subscribed (directly or through groups) as a teacher in a given course
     * 
     * @param Course $course
     *
     * @return User[]
     */
    public function getTeachersFromCourse(Course $course);

    /**
     * Returns an array of users who are subscribed (directly or through groups) as a student in a given course
     * 
     * @param Course $course
     *
     * @return User[]
     */
    public function getStudentsFromCourse(Course $course);

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
    public function getToolRegistration($toolName);

    /**
     * Returns the tools that a given user has access to in the course
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return string[]
     */
    public function getToolsFromCourseForUser(User $user, Course $course);
}