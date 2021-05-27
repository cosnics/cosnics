<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Infrastructure\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface for a course copier service
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseCopierInterface
{

    /**
     * Copies the course by the given parameters
     * 
     * @param User $user
     * @param Course $currentCourse
     * @param int[] $targetCourseIds
     * @param int[] $selectedContentObjectPublicationIds
     * @param int[] $selectedPublicationCategoryIds
     * @param bool $ignoreCategories
     * @param bool $copyCourseGroups
     *
     * @return
     *
     */
    public function copyCourse(User $user, Course $currentCourse, $targetCourseIds = [],
        $selectedContentObjectPublicationIds = [], $selectedPublicationCategoryIds = [], $ignoreCategories = false,
        $copyCourseGroups = true);
}