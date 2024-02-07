<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetNotificationsComponent extends Manager
{
    public const PARAM_NOTIFICATIONS_PER_PAGE = 'notificationsPerPage';

    public const PARAM_OFFSET = 'offset';

    /**
     *
     * @return string
     */
    public function run()
    {
        $notificationServiceBridge = $this->getNotificationServiceBridge();

        $notifications = $notificationServiceBridge->getNotificationsForUser(
            $this->getUser(), $this->getRequest()->request->get(self::PARAM_OFFSET),
            $this->getRequest()->request->get(self::PARAM_NOTIFICATIONS_PER_PAGE)
        );

        return new JsonResponse($this->getSerializer()->serialize($notifications, 'json'), 200, [], true);
    }
}