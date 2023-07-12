<?php
namespace Chamilo\Application\Weblcms\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface for a course admin validator
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseAdminValidatorInterface
{

    /**
     * Validates whether or not a user is an admin of a course
     * 
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    public function isUserAdminOfCourse(User $user, Course $course): bool;
}