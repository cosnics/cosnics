<?php

namespace Chamilo\Core\Queue\Test\Integration\Service;

use Chamilo\Core\Queue\Domain\Job;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Service\JobSerializer;
use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;

/**
 * @package Chamilo\Core\Queue\Test\Unit\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobSerializerTest extends DependencyInjectionBasedTestCase
{
    public function testSerializeJob()
    {
        $processorMock = $this->getMockBuilder(JobProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $jobData = '{"jobData":"{\"created\":\"2015-03-14T00:00:00+0100\",\"processor_class\":\"%s\"}","jobClass":"Chamilo\\\\Core\\\\Queue\\\\Domain\\\\Job"}';
        $jobData = sprintf($jobData, get_class($processorMock));

        $date = new \DateTime();
        $date->setTimestamp('1426287600');

        $job = new Job(get_class($processorMock), $date);
        $this->assertEquals($jobData, $this->getJobSerializer()->serializeJob($job));
    }

    public function testDeserializeJob()
    {
        $processorMock = $this->getMockBuilder(JobProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $date = new \DateTime();
        $date->setTimestamp('1426287600');

        $job = new Job(get_class($processorMock), $date);
        $jobData = $this->getJobSerializer()->serializeJob($job);

        $newJob = $this->getJobSerializer()->deserializeJob($jobData);
        $this->assertInstanceOf(Job::class, $newJob);
        $this->assertEquals($date->getTimestamp(), $job->getCreated()->getTimestamp());
        $this->assertEquals(get_class($processorMock), $job->getProcessorClass());

    }

    /**
     * @return JobSerializer
     */
    protected function getJobSerializer()
    {
        return $this->getService(JobSerializer::class);
    }
}