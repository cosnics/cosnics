<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Component;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Service\AssignmentNotificationsService;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\CourseUserCategoryService;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Core\Notification\Service\NotificationManager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetAssignmentNotificationsComponent extends Manager
{
    const PARAM_OFFSET = 'offset';
    const PARAM_NOTIFICATIONS_PER_PAGE = 'notificationsPerPage';

    /**
     *
     * @return string
     */
    function run()
    {
        $contextPaths = $this->buildContextPaths();

        $notifications = $this->getNotificationManager()->getNotificationsByContextPathsForUser(
            $contextPaths, $this->getUser(), $this->getRequest()->getFromPost(self::PARAM_OFFSET),
            $this->getRequest()->getFromPost(self::PARAM_NOTIFICATIONS_PER_PAGE)
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
        $courseTypeId = $this->getRequest()->getFromUrl(self::PARAM_COURSE_TYPE_ID);
        $courseCategoryId = $this->getRequest()->getFromUrl(self::PARAM_USER_COURSE_CATEGORY_ID);

        return $this->getAssignmentNotificationsService()->buildContextPaths(
            $this->getUser(), $courseTypeId, $courseCategoryId
        );
    }

    /**
     * @return \Chamilo\Core\Notification\Service\NotificationManager
     */
    protected function getNotificationManager()
    {
        return $this->getService(NotificationManager::class);
    }

    /**
     * @return CourseUserCategoryService
     */
    protected function getCourseUserCategoryService()
    {
        return $this->getService(CourseUserCategoryService::class);
    }

    /**
     * @return CourseService
     */
    protected function getCourseService()
    {
        return $this->getService(CourseService::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Service\AssignmentNotificationsService
     */
    protected function getAssignmentNotificationsService()
    {
        return new AssignmentNotificationsService($this->getCourseUserCategoryService(), $this->getCourseService());
    }
}
