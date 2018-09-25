<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Domain\Job;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @package Chamilo\Core\Queueu\Service\NotificationProcessor
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobProcessorFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $serviceContainer;

    /**
     * JobProcessorFactory constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @param \Chamilo\Core\Queue\Domain\Job $job
     *
     * @return \Chamilo\Core\Queue\Service\JobProcessorInterface
     */
    public function createJobProcessor(Job $job)
    {
        $processorClass = $job->getProcessorClass();

        /** @var \Chamilo\Core\Queue\Service\JobProcessorInterface $jobProcessor */
        $jobProcessor = $this->serviceContainer->get($processorClass);

        if (!$jobProcessor instanceof JobProcessorInterface)
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given job processor %s must implement the JobProcessorInterface',
                    $processorClass
                )
            );
        }

        return $jobProcessor;
    }
}