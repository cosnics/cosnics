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
     * @var JobSerializer
     */
    protected $jobSerializer;

    /**
     * @var JobProcessorFactory
     */
    protected $jobProcessorFactory;

    /**
     * Worker constructor.
     *
     * @param PsrContext $psrContext
     * @param JobSerializer $jobSerializer
     * @param JobProcessorFactory $jobProcessorFactory
     */
    public function __construct(
        PsrContext $psrContext, JobSerializer $jobSerializer, JobProcessorFactory $jobProcessorFactory
    )
    {
        $this->psrContext = $psrContext;
        $this->jobSerializer = $jobSerializer;
        $this->jobProcessorFactory = $jobProcessorFactory;
    }

    /**
     * @param string $topic
     */
    public function waitForJobAndExecute($topic)
    {
        $destination = $this->psrContext->createQueue($topic);
        $consumer = $this->psrContext->createConsumer($destination);
        $message = $consumer->receive();

        try
        {
            $job = $this->jobSerializer->deserializeJob($message);
            $processor = $this->jobProcessorFactory->createJobProcessor($job);
            $processor->processJob($job);

            $consumer->acknowledge($message);
        }
        catch(\Throwable $ex)
        {
            $consumer->reject($message);
        }
    }
}