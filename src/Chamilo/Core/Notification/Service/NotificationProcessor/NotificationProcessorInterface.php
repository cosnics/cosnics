<?php

namespace Chamilo\Core\Notification\Service\NotificationProcessor;

use Chamilo\Core\Notification\Domain\NotificationTriggerData;

/**
 * Interface NotificationProcessorInterface
 *
 * @package Chamilo\Core\Notification\Service
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
interface NotificationProcessorInterface
{
    /**
     * @param \Chamilo\Core\Notification\Domain\NotificationTriggerData $notificationTriggerData
     */
    public function processNotificationTrigger(NotificationTriggerData $notificationTriggerData);
}

