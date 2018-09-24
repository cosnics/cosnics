<?php

namespace Chamilo\Core\Queue\Domain;

use Chamilo\Core\Queue\Service\JobProcessorInterface;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Queue\Job
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Job
{
    /**
     * @var \DateTime
     *
     * @Type("DateTime")
     */
    protected $created;

    /**
     * The class name of the processor
     *
     * @var string
     *
     * @Type("string")
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

        if(!is_subclass_of($processorClass, JobProcessorInterface::class))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given job processor %s must implement the JobProcessorInterface',
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