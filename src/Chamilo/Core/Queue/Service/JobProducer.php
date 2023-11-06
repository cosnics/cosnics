<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobProducer
{
    /**
     * @var Producer\ProducerInterface
     */
    protected $producer;

    /**
     * @var \Chamilo\Core\Queue\Service\JobEntityManager
     */
    protected $jobEntityManager;

    private ExceptionLoggerInterface $exceptionLogger;

    /**
     * Producer constructor.
     *
     * @param Producer\ProducerInterface $producer
     * @param \Chamilo\Core\Queue\Service\JobEntityManager $jobEntityManager
     */
    public function __construct(Producer\ProducerInterface $producer, JobEntityManager $jobEntityManager, ExceptionLoggerInterface $exceptionLogger)
    {
        $this->producer = $producer;
        $this->jobEntityManager = $jobEntityManager;
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     * @param string $queueName
     * @param int $delay - Delay in seconds
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function produceJob(Job $job, string $queueName, int $delay = 0)
    {
        $this->jobEntityManager->createJob($job);

        try
        {
            $this->producer->produceMessage($job->getId(), $queueName, $delay);
            $this->jobEntityManager->changeJobStatus($job, Job::STATUS_SENT_TO_QUEUE);
        }
        catch(\Exception $ex)
        {
            $this->exceptionLogger->logException(new Exception(sprintf("Job %s has failed", $job->getId())));
            $this->jobEntityManager->changeJobStatus($job, Job::STATUS_FAILED_RETRY);
        }
    }

}