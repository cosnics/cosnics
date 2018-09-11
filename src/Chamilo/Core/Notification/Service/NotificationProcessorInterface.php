<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Storage\Entity\NotificationTrigger;

/**
 * Interface NotificationProcessorInterface
 *
 * @package Chamilo\Core\Notification\Service
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
interface NotificationProcessorInterface
{
    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\NotificationTrigger $notificationTrigger
     */
    public function processNotificationTrigger(NotificationTrigger $notificationTrigger);
}

