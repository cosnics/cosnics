<?php

namespace Chamilo\Core\Notification\Storage\Repository\QueueNotificationTrigger;

use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\Notification\Repository\QueueNotificationTrigger
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationTriggerRepository extends EntityRepository implements QueueNotificationTriggerRepositoryInterface
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