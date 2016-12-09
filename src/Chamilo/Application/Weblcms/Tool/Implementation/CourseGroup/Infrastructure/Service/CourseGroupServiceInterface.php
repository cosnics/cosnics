<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface for a course group service
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseGroupServiceInterface
{

    /**
     * Creates a document category for a given course group
     * 
     * @param CourseGroup $courseGroup
     *
     * @throws \Exception
     */
    public function createDocumentCategoryForCourseGroup(CourseGroup $courseGroup);

    /**
     * Creates a forum for a given course group
     * 
     * @param CourseGroup $courseGroup
     * @param User $user
     *
     * @throws \Exception
     */
    public function createForumCategoryAndPublicationForCourseGroup(CourseGroup $courseGroup, User $user);

    /**
     * Counts the course groups in a given course
     * 
     * @param int $courseId
     *
     * @return int
     */
    public function countCourseGroupsInCourse($courseId);
}