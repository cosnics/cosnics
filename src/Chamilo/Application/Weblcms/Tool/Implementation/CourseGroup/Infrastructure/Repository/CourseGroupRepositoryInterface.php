<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository;

/**
 * Interface for a repository to manage course groups data
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseGroupRepositoryInterface
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