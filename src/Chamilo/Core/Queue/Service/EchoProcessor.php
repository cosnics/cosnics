<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Storage\Entity\Job;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EchoProcessor implements JobProcessorInterface
{
    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     */
    public function processJob(Job $job)
    {
        var_dump($job->getProcessorClass());
        var_dump($job->getDate());
        var_dump($job->getJobParameters());
    }
}