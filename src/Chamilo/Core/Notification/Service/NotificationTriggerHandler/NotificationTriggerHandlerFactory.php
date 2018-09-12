<?php

namespace Chamilo\Core\Notification\Service\NotificationTriggerHandler;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Notification\Repository\QueueNotificationTrigger\NotificationTriggerRepository;
use Chamilo\Core\Notification\Repository\QueueNotificationTrigger\RedisRepository;

/**
 * @package Chamilo\Core\Notification\Service\NotificationTriggerHandler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationTriggerHandlerFactory
{
    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Core\Notification\Service\NotificationTriggerHandler\DirectNotificationTriggerHandler
     */
    protected $directNotificationTriggerHandler;

    /**
     * @var NotificationTriggerRepository
     */
    protected $notificationTriggerRepository;

    /**
     * NotificationTriggerHandlerFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Core\Notification\Service\NotificationTriggerHandler\DirectNotificationTriggerHandler $directNotificationTriggerHandler
     * @param NotificationTriggerRepository $notificationTriggerRepository
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter,
        DirectNotificationTriggerHandler $directNotificationTriggerHandler,
        NotificationTriggerRepository $notificationTriggerRepository
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->directNotificationTriggerHandler = $directNotificationTriggerHandler;
        $this->notificationTriggerRepository = $notificationTriggerRepository;
    }

    /**
     * @return NotificationTriggerHandlerInterface
     */
    public function createNotificationTriggerHandler()
    {
        $triggerHandler = $this->configurationConsulter->getSetting(
            ['Chamilo\Core\Notification', 'notification_trigger_handler']
        );

        switch($triggerHandler)
        {
            case 'direct':
                return $this->directNotificationTriggerHandler;
            case 'database_queue':
                return new QueueNotificationTriggerHandler($this->notificationTriggerRepository);
            case 'redis_queue':
                $redisRepository = new RedisRepository();
                return new QueueNotificationTriggerHandler($redisRepository);
        }

        return $this->directNotificationTriggerHandler;
    }

}