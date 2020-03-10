<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Queue\Storage\Repository\JobEntityRepository;
use DateTime;
use JMS\Serializer\Serializer;
use RuntimeException;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobEntityManager
{
    /**
     * @var \Chamilo\Core\Queue\Storage\Repository\JobEntityRepository
     */
    protected $jobEntityRepository;

    /**
     * JobEntityManager constructor.
     *
     * @param \Chamilo\Core\Queue\Storage\Repository\JobEntityRepository $jobEntityRepository
     */
    public function __construct(JobEntityRepository $jobEntityRepository)
    {
        $this->jobEntityRepository = $jobEntityRepository;
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createJob(Job $job)
    {
        $job->setDate(new DateTime())
            ->setStatus(Job::STATUS_CREATED);

        $this->jobEntityRepository->createJobEntity($job);
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $jobEntity
     * @param int $newStatus
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeJobStatus(Job $jobEntity, $newStatus = Job::STATUS_SUCCESS)
    {
        $jobEntity->setStatus($newStatus);
        $this->jobEntityRepository->updateJobEntity($jobEntity);
    }

    /**
     * @param int $jobId
     *
     * @return Job
     */
    public function findJob($jobId)
    {
        $job = $this->jobEntityRepository->find($jobId);
        if(!$job instanceof Job)
        {
            throw new RuntimeException(
                sprintf('Could not find the job entity with id %s in the database', $jobId)
            );
        }

        return $job;
    }



}