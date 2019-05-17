<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Service;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Service\CourseUserCategoryService;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentNotificationsService
{
    /**
     * @var CourseUserCategoryService
     */
    protected $courseUserCategoryService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    protected $courseService;

    /**
     * AssignmentNotificationsService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Service\CourseUserCategoryService $courseUserCategoryService
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Service\CourseUserCategoryService $courseUserCategoryService,
        \Chamilo\Application\Weblcms\Service\CourseService $courseService
    )
    {
        $this->courseUserCategoryService = $courseUserCategoryService;
        $this->courseService = $courseService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int|null $courseTypeId
     * @param int|null $courseCategoryId
     *
     * @return array
     */
    public function buildContextPaths(User $user, int $courseTypeId = null, int $courseCategoryId = null)
    {
        if ($courseTypeId > - 1)
        {
            $courseType = new CourseType();
            $courseType->setId($courseTypeId);

            if ($courseCategoryId > 0)
            {
                $courseUserCategory = new CourseUserCategory();
                $courseUserCategory->setId($courseCategoryId);

                $courses = $this->courseUserCategoryService->getCoursesForUserByCourseUserCategoryAndCourseType(
                    $user, $courseUserCategory, $courseType
                );
            }
            else
            {
                $courses = $this->courseService->getCoursesInCourseTypeForUser($user, $courseType);
            }

            $contextPaths = [];

            foreach($courses as $course)
            {
                $contextPaths[] = 'Chamilo\\Application\\Weblcms::Tool:Assignment::Course:' . $course->getId();
                $contextPaths[] = 'Chamilo\\Application\\Weblcms::Tool:LearningPath::Course:' . $course->getId();
            }

            return $contextPaths;
        }

        return ['Assignment'];
    }
}