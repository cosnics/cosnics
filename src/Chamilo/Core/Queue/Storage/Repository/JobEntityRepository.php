<?php

namespace Chamilo\Core\Queue\Storage\Repository;

use Chamilo\Core\Queue\Storage\Entity\Job;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\Queue\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobEntityRepository extends EntityRepository
{
    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $jobEntity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createJobEntity(Job $jobEntity)
    {
        $this->getEntityManager()->persist($jobEntity);

        foreach($jobEntity->getParameters() as $parameter)
        {
            $this->getEntityManager()->persist($parameter);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $jobEntity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateJobEntity(Job $jobEntity)
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return Job
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFirstFailedJob()
    {
        $queryBuilder = $this->createQueryBuilder('job')
            ->where('job.status = :failedJobStatus')
            ->setParameter('failedJobStatus', Job::STATUS_FAILED_RETRY)
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Job[]
     */
    public function findFailedJobs()
    {
        $queryBuilder = $this->createQueryBuilder('job')
            ->addSelect('parameter')
            ->join('job.jobParameters', 'parameter')
            ->where('job.status = :failedJobStatus')
            ->setParameter('failedJobStatus', Job::STATUS_FAILED_RETRY);

        return $queryBuilder->getQuery()->getResult();
    }
}