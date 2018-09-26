<?php

namespace Chamilo\Core\Queue\Service;

use Interop\Queue\PsrContext;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Worker
{
    /**
     * @var PsrContext
     */
    protected $psrContext;

    /**
     * @var JobProcessorFactory
     */
    protected $jobProcessorFactory;

    /**
     * @var \Chamilo\Core\Queue\Service\JobEntityManager
     */
    protected $jobEntityManager;

    /**
     * Worker constructor.
     *
     * @param PsrContext $psrContext
     * @param JobProcessorFactory $jobProcessorFactory
     * @param \Chamilo\Core\Queue\Service\JobEntityManager $jobEntityManager
     */
    public function __construct(
        PsrContext $psrContext, JobProcessorFactory $jobProcessorFactory, JobEntityManager $jobEntityManager
    )
    {
        $this->psrContext = $psrContext;
        $this->jobProcessorFactory = $jobProcessorFactory;
        $this->jobEntityManager = $jobEntityManager;
    }

    /**
     * @param string $queueName
     */
    public function waitForJobAndExecute($queueName)
    {
        $destination = $this->psrContext->createQueue($queueName);
        $consumer = $this->psrContext->createConsumer($destination);
        $message = $consumer->receive();

        try
        {
            $jobEntityId = $message->getBody();
            $job = $this->jobEntityManager->findJob($jobEntityId);

            $processor = $this->jobProcessorFactory->createJobProcessor($job);
            $processor->processJob($job);

            $consumer->acknowledge($message);
        }
        catch(\Throwable $ex)
        {
            $consumer->reject($message);
            echo $ex->getMessage();
            echo $ex->getTraceAsString();
        }
    }
}