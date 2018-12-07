<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FailedJobExecutor
{
    /**
     * @var \Chamilo\Core\Queue\Service\JobProcessorFactory
     */
    protected $jobProcessorFactory;

    /**
     * @var \Chamilo\Core\Queue\Service\JobEntityManager
     */
    protected $jobEntityManager;

    /**
     * FailedJobExecutor constructor.
     *
     * @param \Chamilo\Core\Queue\Service\JobProcessorFactory $jobProcessorFactory
     * @param \Chamilo\Core\Queue\Service\JobEntityManager $jobEntityManager
     */
    public function __construct(JobProcessorFactory $jobProcessorFactory, JobEntityManager $jobEntityManager)
    {
        $this->jobProcessorFactory = $jobProcessorFactory;
        $this->jobEntityManager = $jobEntityManager;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Throwable
     */
    public function retryFirstFailedJob(OutputInterface $output)
    {
        $job = $this->jobEntityManager->findFirstFailedJob();
        if(!$job instanceof Job)
        {
            $output->writeln('No failed job found, exiting');
            return;
        }

        try
        {
            $output->writeln('Processing job ' . $job->getId());

            $this->jobEntityManager->changeJobStatus($job, Job::STATUS_IN_PROGRESS);

            $processor = $this->jobProcessorFactory->createJobProcessor($job);
            $processor->processJob($job);

            $this->jobEntityManager->changeJobStatus($job, Job::STATUS_SUCCESS);

            $output->writeln('Success');
        }
        catch(JobNoLongerValidException $ex)
        {
            $this->jobEntityManager->changeJobStatus($job, Job::STATUS_FAILED_NO_LONGER_VALID);
            $output->writeln('No longer valid');
        }
        catch (\Throwable $ex)
        {
            $this->jobEntityManager->changeJobStatus($job, Job::STATUS_FAILED_RETRY);
            $output->writeln('Failed, please retry');
            throw $ex;
        }

    }

}