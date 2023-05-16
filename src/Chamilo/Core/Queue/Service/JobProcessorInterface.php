<?php
namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Storage\Entity\Job;

/**
 * Interface JobProcessorInterface
 *
 * @package Chamilo\Core\Queue\Service
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
interface JobProcessorInterface
{
    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     */
    public function processJob(Job $job);
}

