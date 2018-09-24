<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Domain\Job;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EchoProcessor implements JobProcessorInterface
{

    /**
     * @param \Chamilo\Core\Queue\Domain\Job $job
     *
     * @return mixed
     */
    public function processJob(Job $job)
    {
        var_dump($job->getProcessorClass());
        var_dump($job->getCreated());
    }
}