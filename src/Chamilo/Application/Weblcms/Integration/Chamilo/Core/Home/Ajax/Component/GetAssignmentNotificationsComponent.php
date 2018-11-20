<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager;
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
        $notifications = $this->getNotificationManager()->getNotificationsByContextPathForUser(
            'Assignment', $this->getUser(), $this->getRequest()->getFromPost(self::PARAM_OFFSET),
            $this->getRequest()->getFromPost(self::PARAM_NOTIFICATIONS_PER_PAGE)
        );

        $this->getNotificationManager()->setNotificationsViewedForUser($notifications, $this->getUser());

        $notifications = $this->getNotificationManager()->formatNotifications($notifications, 'Chamilo');

        return new JsonResponse($this->getSerializer()->serialize($notifications, 'json'), 200, [], true);
    }

    /**
     * @return \Chamilo\Core\Notification\Service\NotificationManager
     */
    protected function getNotificationManager()
    {
        return $this->getService(NotificationManager::class);
    }
}
