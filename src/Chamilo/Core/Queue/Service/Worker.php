<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;

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
     *
     * @throws \Throwable
     */
    public function waitForJobAndExecute($queueName)
    {
        $timeout = 10 * (60 * 1000); // x minutes * 60 seconds * 1000 milliseconds

        $destination = $this->psrContext->createQueue($queueName);
        $consumer = $this->psrContext->createConsumer($destination);
        $message = $consumer->receive($timeout);
        if(!$message instanceof PsrMessage)
        {
            return;
        }

        try
        {
            $jobEntityId = $message->getBody();
            $job = $this->jobEntityManager->findJob($jobEntityId);

            try
            {
                $this->jobEntityManager->changeJobStatus($job, Job::STATUS_IN_PROGRESS);

                $processor = $this->jobProcessorFactory->createJobProcessor($job);
                $processor->processJob($job);

                $consumer->acknowledge($message);

                $this->jobEntityManager->changeJobStatus($job, Job::STATUS_SUCCESS);
            }
            catch(JobNoLongerValidException $ex)
            {
                $consumer->acknowledge($message);
                $this->jobEntityManager->changeJobStatus($job, Job::STATUS_FAILED_NO_LONGER_VALID);
            }
            catch (\Throwable $ex)
            {
                $this->jobEntityManager->changeJobStatus($job, Job::STATUS_FAILED_RETRY);
                throw $ex;
            }

        }
        catch(\Throwable $ex)
        {
            $consumer->reject($message);
            throw $ex;
        }
    }
}