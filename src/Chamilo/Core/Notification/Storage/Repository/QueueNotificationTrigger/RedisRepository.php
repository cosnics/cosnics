<?php

namespace Chamilo\Core\Notification\Repository\QueueNotificationTrigger;

use Chamilo\Core\Notification\QueueNotificationTrigger\QueueNotificationTriggerRepositoryInterface;

/**
 * @package Chamilo\Core\Notification\Repository\QueueNotificationTrigger
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RedisRepository implements QueueNotificationTriggerRepositoryInterface
{
    /**
     * @param string $notificationTriggerData
     * @param \DateTime $createdDate
     */
    public function addNotificationTriggerToQueue($notificationTriggerData, \DateTime $createdDate)
    {
        // TODO: Implement addNotificationTriggerToQueue() method.
    }
}