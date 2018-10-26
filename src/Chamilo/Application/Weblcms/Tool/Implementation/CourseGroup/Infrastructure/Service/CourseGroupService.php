<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepositoryInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;

/**
 * Course group service to help with the management of course groups
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupService implements CourseGroupServiceInterface
{

    /**
     *
     * @var CourseGroupRepositoryInterface
     */
    protected $courseGroupRepository;

    /**
     * CourseGroupService constructor.
     *
     * @param CourseGroupRepositoryInterface $courseGroupRepository
     */
    public function __construct(CourseGroupRepositoryInterface $courseGroupRepository)
    {
        $this->courseGroupRepository = $courseGroupRepository;
    }

    /**
     * Counts the course groups in a given course
     *
     * @param int $courseId
     *
     * @return int
     */
    public function countCourseGroupsInCourse($courseId)
    {
        return $this->courseGroupRepository->countCourseGroupsInCourse($courseId);
    }
}