<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\CourseUserCategoryService;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Core\Notification\Service\NotificationManager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class GetAssignmentNotificationsComponent extends Manager
{
    public const PARAM_NOTIFICATIONS_PER_PAGE = 'notificationsPerPage';
    public const PARAM_OFFSET = 'offset';

    public function run()
    {
        $contextPaths = $this->buildContextPaths();

        $notifications = $this->getNotificationManager()->getNotificationsByContextPathsForUser(
            $contextPaths, $this->getUser(), $this->getRequest()->getFromRequest(self::PARAM_OFFSET),
            $this->getRequest()->getFromRequest(self::PARAM_NOTIFICATIONS_PER_PAGE)
        );

        $this->getNotificationManager()->setNotificationsViewedForUser($notifications, $this->getUser());

        $notifications = $this->getNotificationManager()->formatNotifications($notifications, 'Chamilo');

        return new JsonResponse($this->getSerializer()->serialize($notifications, 'json'), 200, [], true);
    }

    /**
     * @return string[]
     */
    protected function buildContextPaths()
    {
        $courseTypeId = $this->getRequest()->getFromQuery(self::PARAM_COURSE_TYPE_ID);
        if ($courseTypeId > - 1)
        {
            $courseType = new CourseType();
            $courseType->setId($courseTypeId);

            $courseCategoryId = $this->getRequest()->getFromQuery(self::PARAM_USER_COURSE_CATEGORY_ID);
            if ($courseCategoryId > 0)
            {
                $courseUserCategory = new CourseUserCategory();
                $courseUserCategory->setId($courseCategoryId);

                $courses = $this->getCourseUserCategoryService()->getCoursesForUserByCourseUserCategoryAndCourseType(
                    $this->getUser(), $courseUserCategory, $courseType
                );
            }
            else
            {
                $courses = $this->getCourseService()->getCoursesInCourseTypeForUser($this->getUser(), $courseType);
            }

            $contextPaths = [];

            foreach ($courses as $course)
            {
                $contextPaths[] = 'Chamilo\\Application\\Weblcms::Tool:Assignment::Course:' . $course->getId();
                // TODO: FIX THIS FOR LEARNING PATHS CORRECTLY
                $contextPaths[] = 'Chamilo\\Application\\Weblcms::Tool:LearningPath::Course:' . $course->getId();
            }

            return $contextPaths;
        }

        return ['Assignment'];
    }

    protected function getCourseService(): CourseService
    {
        return $this->getService(CourseService::class);
    }

    protected function getCourseUserCategoryService(): CourseUserCategoryService
    {
        return $this->getService(CourseUserCategoryService::class);
    }

    protected function getNotificationManager(): NotificationManager
    {
        return $this->getService(NotificationManager::class);
    }
}
