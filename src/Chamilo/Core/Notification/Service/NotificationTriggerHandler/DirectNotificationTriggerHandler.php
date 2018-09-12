<?php

namespace Chamilo\Core\Notification\Service\NotificationTriggerHandler;

use Chamilo\Core\Notification\Domain\NotificationTriggerData;
use Chamilo\Core\Notification\Service\NotificationProcessor\NotificationProcessorFactory;

/**
 * @package Chamilo\Core\Notification\Service\NotificationTriggerHandler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DirectNotificationTriggerHandler implements NotificationTriggerHandlerInterface
{
    /**
     * @var NotificationProcessorFactory
     */
    protected $notificationProcessorFactory;

    /**
     * DirectNotificationTriggerHandler constructor.
     *
     * @param NotificationProcessorFactory $notificationProcessorFactory
     */
    public function __construct(NotificationProcessorFactory $notificationProcessorFactory)
    {
        $this->notificationProcessorFactory = $notificationProcessorFactory;
    }

    /**
     * @param \Chamilo\Core\Notification\Domain\NotificationTriggerData $notificationTriggerData
     */
    public function handle(NotificationTriggerData $notificationTriggerData)
    {
        $notificationProcessor =
            $this->notificationProcessorFactory->createNotificationProcessor($notificationTriggerData);

        $notificationProcessor->processNotificationTrigger($notificationTriggerData);
    }
}