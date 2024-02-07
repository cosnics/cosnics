<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

/**
 * Interface for a course group service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseGroupServiceInterface
{

    /**
     * Counts the course groups in a given course
     *
     * @param int $courseId
     *
     * @return int
     */
    public function countCourseGroupsInCourse($courseId);
}