<?php

namespace Chamilo\Core\Notification\Service\NotificationTriggerHandler;

use Chamilo\Core\Notification\Domain\NotificationTriggerData;

/**
 * Interface NotificationTriggerHandlerInterface
 *
 * @package Chamilo\Core\Notification\Service\NotificationTriggerHandler
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface NotificationTriggerHandlerInterface
{
    /**
     * @param \Chamilo\Core\Notification\Domain\NotificationTriggerData $notificationTriggerData
     */
    public function handle(NotificationTriggerData $notificationTriggerData);
}