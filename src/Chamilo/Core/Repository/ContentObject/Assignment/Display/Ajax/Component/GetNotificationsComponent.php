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
    /**
     *
     * @return string
     */
    function run()
    {
        $notificationServiceBridge = $this->getNotificationServiceBridge();
        $notifications = $notificationServiceBridge->getNotificationsForUser($this->getUser());

        return new JsonResponse($this->getSerializer()->serialize($notifications, 'json'), 200, [], true);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface
     */
    protected function getNotificationServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(NotificationServiceBridgeInterface::class);
    }

    /**
     * @return NotificationTranslator
     */
    protected function getNotificationTranslator()
    {
        return $this->getService(NotificationTranslator::class);
    }
}