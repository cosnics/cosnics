<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Domain\Job;
use JMS\Serializer\Serializer;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobSerializer
{
    const PROPERTY_JOB_DATA = 'jobData';
    const PROPERTY_JOB_CLASS = 'jobClass';

    /**
     * @var \JMS\Serializer\Serializer
     */
    protected $serializer;

    /**
     * JobSerializer constructor.
     *
     * @param \JMS\Serializer\Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param \Chamilo\Core\Queue\Domain\Job $job
     *
     * @return string
     */
    public function serializeJob(Job $job)
    {
        $serializedJob = $this->serializer->serialize($job, 'json');

        $data = [];
        $data[self::PROPERTY_JOB_DATA] = $serializedJob;
        $data[self::PROPERTY_JOB_CLASS] = get_class($job);

        return $this->serializer->serialize($data, 'json');
    }

    /**
     * @param string $serializedData
     *
     * @return \Chamilo\Core\Queue\Domain\Job
     */
    public function deserializeJob($serializedData = '')
    {
        $data = \json_decode($serializedData, true);
        $jobData = $data[self::PROPERTY_JOB_DATA];
        $jobClass = $data[self::PROPERTY_JOB_CLASS];

        return $this->serializer->deserialize($jobData, $jobClass, 'json');
    }
}
