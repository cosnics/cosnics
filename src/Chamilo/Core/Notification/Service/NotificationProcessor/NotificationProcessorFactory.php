<?php

namespace Chamilo\Core\Notification\Service\NotificationProcessor;

use Chamilo\Core\Notification\Domain\NotificationTriggerData;

/**
 * @package Chamilo\Core\Notification\Service\NotificationProcessor
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationProcessorFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $serviceContainer;

    /**
     * @param \Chamilo\Core\Notification\Domain\NotificationTriggerData $notificationTriggerData
     *
     * @return \Chamilo\Core\Notification\Service\NotificationProcessor\NotificationProcessorInterface
     */
    public function createNotificationProcessor(NotificationTriggerData $notificationTriggerData)
    {
        $processorClass = $notificationTriggerData->getProcessorClass();

        /** @var \Chamilo\Core\Notification\Service\NotificationProcessor\NotificationProcessorInterface $notificationProcessor */
        $notificationProcessor = $this->serviceContainer->get($processorClass);

        if (!$notificationProcessor instanceof NotificationProcessorInterface)
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given notification processor %s must implement the NotificationProcessorInterface',
                    $processorClass
                )
            );
        }

        return $notificationProcessor;
    }
}