<?php
namespace Chamilo\Application\Weblcms\Storage\Repository\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Wrapper for the weblcms datamanager
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface WeblcmsRepositoryInterface
{

    /**
     * Retrieves a user by a username
     * 
     * @param string $username
     *
     * @return User
     */
    public function retrieveUserByUsername($username);

    /**
     * Retrieves a group by a code
     * 
     * @param string $groupCode
     *
     * @return Group
     */
    public function retrieveGroupByCode($groupCode);

    /**
     * Retrieves a course by a code
     * 
     * @param string $courseCode
     *
     * @return Course
     */
    public function retrieveCourseByCode($courseCode);

    /**
     * Retrieves a course entity relation by a given entity and course.
     * The entity is defined by a type
     * and an identifier.
     * 
     * @param int $entityType
     * @param int $entityId
     * @param int $courseId
     *
     * @return CourseEntityRelation
     */
    public function retrieveCourseEntityRelationByEntityAndCourse($entityType, $entityId, $courseId);
}