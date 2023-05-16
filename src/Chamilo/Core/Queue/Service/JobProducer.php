<?php
namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Storage\Entity\Job;
use Exception;

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

    /**
     * Producer constructor.
     *
     * @param Producer\ProducerInterface $producer
     * @param \Chamilo\Core\Queue\Service\JobEntityManager $jobEntityManager
     */
    public function __construct(Producer\ProducerInterface $producer, JobEntityManager $jobEntityManager)
    {
        $this->producer = $producer;
        $this->jobEntityManager = $jobEntityManager;
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
        catch(Exception $ex)
        {
            $this->jobEntityManager->changeJobStatus($job, Job::STATUS_FAILED_RETRY);
        }
    }

}