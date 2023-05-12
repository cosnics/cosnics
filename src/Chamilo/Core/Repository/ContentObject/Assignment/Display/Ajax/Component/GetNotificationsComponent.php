<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component;

use Chamilo\Core\Notification\Service\NotificationTranslator;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetNotificationsComponent extends Manager
{
    const PARAM_OFFSET = 'offset';
    const PARAM_NOTIFICATIONS_PER_PAGE = 'notificationsPerPage';

    /**
     *
     * @return string
     */
    function run()
    {
        $notificationServiceBridge = $this->getNotificationServiceBridge();

        $notifications = $notificationServiceBridge->getNotificationsForUser(
            $this->getUser(), $this->getRequest()->getFromRequest(self::PARAM_OFFSET),
            $this->getRequest()->getFromRequest(self::PARAM_NOTIFICATIONS_PER_PAGE)
        );

        return new JsonResponse($this->getSerializer()->serialize($notifications, 'json'), 200, [], true);
    }
}