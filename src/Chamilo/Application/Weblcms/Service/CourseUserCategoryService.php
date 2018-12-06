<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\Repository\CourseUserCategoryRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Services to manage course user categories
 *
 * @package Chamilo\Application\Weblcms\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseUserCategoryService
{
    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\CourseUserCategoryRepository
     */
    protected $courseUserCategoryRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    protected $courseService;

    /**
     * CourseUserCategoryService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Storage\Repository\CourseUserCategoryRepository $courseUserCategoryRepository
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     */
    public function __construct(CourseUserCategoryRepository $courseUserCategoryRepository, CourseService $courseService)
    {
        $this->courseUserCategoryRepository = $courseUserCategoryRepository;
        $this->courseService = $courseService;
    }

    /**
     * @param int $courseUserCategoryId
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory
     */
    public function getCourseUserCategoryById($courseUserCategoryId)
    {
        return $this->courseUserCategoryRepository->findCourseUserCategoryById($courseUserCategoryId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory $courseUserCategory
     * @param \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType $courseType
     *
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getCoursesForUserByCourseUserCategoryAndCourseType(
        User $user, CourseUserCategory $courseUserCategory, CourseType $courseType
    )
    {
        $subscribedCourseIds = $this->courseService->getSubscribedCourseIdsForUser($user);

        return $this->courseUserCategoryRepository->findCoursesForUserByCourseUserCategoryAndCourseType(
            $courseUserCategory, $courseType, $subscribedCourseIds
        );
    }

}