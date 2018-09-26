<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Domain\Job;
use Chamilo\Core\Queue\Storage\Entity\JobEntity;
use Interop\Queue\PsrContext;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Dispatcher
{
    /**
     * @var Producer\ProducerInterface
     */
    protected $producer;

    /**
     * @var JobSerializer
     */
    protected $jobSerializer;

    /**
     * @var \Chamilo\Core\Queue\Service\JobEntityManager
     */
    protected $jobEntityManager;

    /**
     * Producer constructor.
     *
     * @param Producer\ProducerInterface $producer
     * @param JobSerializer $jobSerializer
     */
    public function __construct(Producer\ProducerInterface $producer, JobSerializer $jobSerializer)
    {
        $this->producer = $producer;
        $this->jobSerializer = $jobSerializer;
    }

    /**
     * @param \Chamilo\Core\Queue\Domain\Job $job
     * @param string $queueName
     * @param int $delay - Delay in seconds
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function dispatchJob(Job $job, string $queueName, int $delay = 0)
    {
        $messageContent = $this->jobSerializer->serializeJob($job);
        $jobEntity = $this->jobEntityManager->addJobToDatabase($messageContent);
        $this->producer->produceMessage($jobEntity->getId(), $queueName, $delay);
        $this->jobEntityManager->changeJobStatus($jobEntity, JobEntity::STATUS_SENT_TO_QUEUE);
    }

}