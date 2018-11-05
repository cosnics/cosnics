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
        $jsonResponse = '[{"message":"<b>Kevin VAN EENOO</b> heeft een verbeterde versie opgeladen bij de inzending van <b>Sonia VANDERMEERSCH</b>","time":"4 uur geleden","url":"","isRead":false,"isNew":true,"filters":[]},' .
            '{"message":"<b>Tom DEMETS</b> heeft nieuwe feedback geplaatst op de inzending van <b>Sonia VANDERMEERSCH</b>","time":"20 uur geleden","url":"","isRead":false,"isNew":true,"filters":[]},' .
            '{"message":"<b>Sonia VANDERMEERSCH</b> heeft een nieuwe inzending geplaatst","time":"gisteren - 20u20","url":"","isRead":true,"isNew":false,"filters":[]}]';

        $notificationServiceBridge = $this->getNotificationServiceBridge();
        $notifications = $notificationServiceBridge->getNotificationsForUser($this->getUser());

        return new JsonResponse($notifications, 200, [], true);
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