<?php

namespace Chamilo\Core\Notification\Domain;

use Chamilo\Core\Notification\Service\NotificationProcessor\NotificationProcessorInterface;

/**
 * @package Chamilo\Core\Notification\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class NotificationTriggerData
{
    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * The class name of the processor
     *
     * @var string
     */
    protected $processorClass;

    /**
     * NotificationTriggerData constructor.
     *
     * @param \DateTime $created
     * @param string $processorClass
     */
    public function __construct(string $processorClass, \DateTime $created)
    {
        $this->created = $created;
        $this->processorClass = $processorClass;

        if(!is_subclass_of($processorClass, NotificationProcessorInterface::class))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given notification processor %s must implement the NotificationProcessorInterface',
                    $processorClass
                )
            );
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getProcessorClass(): string
    {
        return $this->processorClass;
    }
}