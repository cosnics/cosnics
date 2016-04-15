<?php

namespace Chamilo\Application\Weblcms\Admin\Extension\Platform;

use Chamilo\Application\Weblcms\Admin\CourseAdminValidatorInterface;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * The Course Admin Validator for the Admin Platform package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseAdminValidator implements CourseAdminValidatorInterface
{

    /**
     * Validates whether or not a user is an admin of a course
     *
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    public function isUserAdminOfCourse(User $user, Course $course)
    {
        return \Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager:: entity_is_admin_for_target(
            \Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\UserEntity :: ENTITY_TYPE,
            $user->getId(),
            \Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity :: ENTITY_TYPE,
            $course->getId()
        );
    }
}