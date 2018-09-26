<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Storage\Entity\JobEntity;
use Chamilo\Core\Queue\Storage\Repository\JobEntityRepository;

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
     * @param string $jobJSON
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\JobEntity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addJobToDatabase($jobJSON = '')
    {
        $jobEntity = new JobEntity();

        $jobEntity->setDate(new \DateTime())
            ->setMessage($jobJSON)
            ->setStatus(JobEntity::STATUS_CREATED);

        $this->jobEntityRepository->createJobEntity($jobEntity);

        return $jobEntity;
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\JobEntity $jobEntity
     * @param int $newStatus
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeJobStatus(JobEntity $jobEntity, $newStatus = JobEntity::STATUS_SUCCESS)
    {
        $jobEntity->setStatus($newStatus);
        $this->jobEntityRepository->updateJobEntity($jobEntity);
    }

    /**
     * @param int $jobId
     *
     * @return JobEntity
     */
    public function findJob($jobId)
    {
        $job = $this->jobEntityRepository->find($jobId);
        if(!$job instanceof JobEntity)
        {
            throw new \RuntimeException(
                sprintf('Could not find the job entity with id %s in the database', $jobId)
            );
        }

        return $job;
    }



}