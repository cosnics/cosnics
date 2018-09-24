<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Domain\Job;

/**
 * Interface JobProcessorInterface
 *
 * @package Chamilo\Core\Queue\Service
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
interface JobProcessorInterface
{
    /**
     * @param \Chamilo\Core\Queue\Domain\Job $job
     *
     * @return mixed
     */
    public function processJob(Job $job);
}

