<?php

namespace Chamilo\Core\Notification\Service\NotificationTriggerHandler;

use Chamilo\Core\Notification\Domain\NotificationTriggerData;
use Chamilo\Core\Notification\QueueNotificationTrigger\QueueNotificationTriggerRepositoryInterface;

/**
 * @package Chamilo\Core\Notification\Service\NotificationTriggerHandler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class QueueNotificationTriggerHandler implements NotificationTriggerHandlerInterface
{
    /**
     * @var \Chamilo\Core\Notification\QueueNotificationTrigger\QueueNotificationTriggerRepositoryInterface
     */
    protected $queueNotificationTriggerRepository;

    /**
     * QueueNotificationTriggerHandler constructor.
     *
     * @param \Chamilo\Core\Notification\QueueNotificationTrigger\QueueNotificationTriggerRepositoryInterface $queueNotificationTriggerRepository
     */
    public function __construct(QueueNotificationTriggerRepositoryInterface $queueNotificationTriggerRepository)
    {
        $this->queueNotificationTriggerRepository = $queueNotificationTriggerRepository;
    }

    /**
     * @param \Chamilo\Core\Notification\Domain\NotificationTriggerData $notificationTriggerData
     */
    public function handle(NotificationTriggerData $notificationTriggerData)
    {

    }
}