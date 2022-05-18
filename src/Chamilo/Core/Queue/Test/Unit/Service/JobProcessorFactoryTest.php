<?php

namespace Chamilo\Core\Queue\Test\Unit\Service;

use Chamilo\Core\Queue\Service\JobProcessorFactory;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tests the JobProcessorFactory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobProcessorFactoryTest extends ChamiloTestCase
{
    /**
     * @var JobProcessorFactory
     */
    protected $jobProcessorFactory;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->jobProcessorFactory = new JobProcessorFactory($this->containerMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->containerMock);
        unset($this->jobProcessorFactory);
    }

    public function testCreateJobProcessor()
    {
        $job = new Job();
        $job->setProcessorClass('DummyProcessorClass');

        $jobProcessor = $this->getMockBuilder(JobProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('DummyProcessorClass')
            ->will($this->returnValue($jobProcessor));

        $this->assertEquals($jobProcessor, $this->jobProcessorFactory->createJobProcessor($job));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateJobProcessorInvalidProcessor()
    {
        $job = new Job();
        $job->setProcessorClass('DummyProcessorClass');

        $jobProcessor = $this->getMockBuilder(JobProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('DummyProcessorClass')
            ->will($this->returnValue(null));

        $this->jobProcessorFactory->createJobProcessor($job);
    }
}


