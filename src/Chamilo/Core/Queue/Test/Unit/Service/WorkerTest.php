<?php

namespace Chamilo\Core\Queue\Test\Unit\Service;

use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Service\JobEntityManager;
use Chamilo\Core\Queue\Service\JobProcessorFactory;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Service\Worker;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Exception;
use Interop\Queue\PsrConsumer;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrQueue;

/**
 * Tests the Worker
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkerTest extends ChamiloTestCase
{
    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var \Chamilo\Core\Queue\Service\JobProcessorFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobProcessorFactoryMock;

    /**
     * @var \Interop\Queue\PsrContext|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $psrContextMock;

    /**
     * @var \Chamilo\Core\Queue\Service\JobEntityManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobEntityManagerMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->jobProcessorFactoryMock = $this->getMockBuilder(JobProcessorFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->psrContextMock = $this->getMockBuilder(PsrContext::class)
            ->disableOriginalConstructor()->getMock();

        $this->jobEntityManagerMock = $this->getMockBuilder(JobEntityManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->worker = new Worker($this->psrContextMock, $this->jobProcessorFactoryMock, $this->jobEntityManagerMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->jobProcessorFactoryMock);
        unset($this->psrContextMock);
        unset($this->jobEntityManagerMock);
        unset($this->worker);
    }

    public function testWaitForJobAndExecute()
    {
        $psrQueue = $this->getMockBuilder(PsrQueue::class)
            ->disableOriginalConstructor()->getMock();

        $psrConsumer = $this->getMockBuilder(PsrConsumer::class)
            ->disableOriginalConstructor()->getMock();

        /** @var PsrMessage|\PHPUnit\Framework\MockObject\MockObject $psrMessage */
        $psrMessage = $this->getMockBuilder(PsrMessage::class)
            ->disableOriginalConstructor()->getMock();

        $psrMessage->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(5));

        $jobProcessorMock = $this->getMockBuilder(JobProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->psrContextMock->expects($this->once())
            ->method('createQueue')
            ->with('notifications')
            ->will($this->returnValue($psrQueue));

        $this->psrContextMock->expects($this->once())
            ->method('createConsumer')
            ->with($psrQueue)
            ->will($this->returnValue($psrConsumer));

        $psrConsumer->expects($this->once())
            ->method('receive')
            ->will($this->returnValue($psrMessage));

        $this->jobEntityManagerMock->expects($this->once())
            ->method('findJob')
            ->with(5)
            ->will($this->returnValue($job));

        $this->jobEntityManagerMock->expects($this->at(1))
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_IN_PROGRESS);

        $this->jobProcessorFactoryMock->expects($this->once())
            ->method('createJobProcessor')
            ->with($job)
            ->will($this->returnValue($jobProcessorMock));

        $jobProcessorMock->expects($this->once())
            ->method('processJob')
            ->with($job);

        $psrConsumer->expects($this->once())
            ->method('acknowledge')
            ->with($psrMessage);

        $this->jobEntityManagerMock->expects($this->at(2))
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_SUCCESS);

        $this->worker->waitForJobAndExecute('notifications');
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Throwable
     */
    public function testWaitForJobAndExecuteExceptionBeforeJob()
    {
        $psrQueue = $this->getMockBuilder(PsrQueue::class)
            ->disableOriginalConstructor()->getMock();

        $psrConsumer = $this->getMockBuilder(PsrConsumer::class)
            ->disableOriginalConstructor()->getMock();

        /** @var PsrMessage|\PHPUnit\Framework\MockObject\MockObject $psrMessage */
        $psrMessage = $this->getMockBuilder(PsrMessage::class)
            ->disableOriginalConstructor()->getMock();

        $psrMessage->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(5));

        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->psrContextMock->expects($this->once())
            ->method('createQueue')
            ->with('notifications')
            ->will($this->returnValue($psrQueue));

        $this->psrContextMock->expects($this->once())
            ->method('createConsumer')
            ->with($psrQueue)
            ->will($this->returnValue($psrConsumer));

        $psrConsumer->expects($this->once())
            ->method('receive')
            ->will($this->returnValue($psrMessage));

        $this->jobEntityManagerMock->expects($this->once())
            ->method('findJob')
            ->with(5)
            ->will($this->throwException(new Exception()));

        $psrConsumer->expects($this->once())
            ->method('reject')
            ->with($psrMessage);

        $this->worker->waitForJobAndExecute('notifications');
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Throwable
     */
    public function testWaitForJobAndExecuteExceptionWithinJob()
    {
        $psrQueue = $this->getMockBuilder(PsrQueue::class)
            ->disableOriginalConstructor()->getMock();

        $psrConsumer = $this->getMockBuilder(PsrConsumer::class)
            ->disableOriginalConstructor()->getMock();

        /** @var PsrMessage|\PHPUnit\Framework\MockObject\MockObject $psrMessage */
        $psrMessage = $this->getMockBuilder(PsrMessage::class)
            ->disableOriginalConstructor()->getMock();

        $psrMessage->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(5));

        $jobProcessorMock = $this->getMockBuilder(JobProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->psrContextMock->expects($this->once())
            ->method('createQueue')
            ->with('notifications')
            ->will($this->returnValue($psrQueue));

        $this->psrContextMock->expects($this->once())
            ->method('createConsumer')
            ->with($psrQueue)
            ->will($this->returnValue($psrConsumer));

        $psrConsumer->expects($this->once())
            ->method('receive')
            ->will($this->returnValue($psrMessage));

        $this->jobEntityManagerMock->expects($this->once())
            ->method('findJob')
            ->with(5)
            ->will($this->returnValue($job));

        $this->jobEntityManagerMock->expects($this->at(1))
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_IN_PROGRESS);

        $this->jobProcessorFactoryMock->expects($this->once())
            ->method('createJobProcessor')
            ->with($job)
            ->will($this->returnValue($jobProcessorMock));

        $jobProcessorMock->expects($this->once())
            ->method('processJob')
            ->with($job)
            ->will($this->throwException(new Exception()));

        $this->jobEntityManagerMock->expects($this->at(2))
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_FAILED_RETRY);

        $psrConsumer->expects($this->once())
            ->method('reject')
            ->with($psrMessage);

        $this->worker->waitForJobAndExecute('notifications');
    }

    public function testWaitForJobAndExecuteNoLongerValidExceptionWithinJob()
    {
        $psrQueue = $this->getMockBuilder(PsrQueue::class)
            ->disableOriginalConstructor()->getMock();

        $psrConsumer = $this->getMockBuilder(PsrConsumer::class)
            ->disableOriginalConstructor()->getMock();

        /** @var PsrMessage|\PHPUnit\Framework\MockObject\MockObject $psrMessage */
        $psrMessage = $this->getMockBuilder(PsrMessage::class)
            ->disableOriginalConstructor()->getMock();

        $psrMessage->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(5));

        $jobProcessorMock = $this->getMockBuilder(JobProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->psrContextMock->expects($this->once())
            ->method('createQueue')
            ->with('notifications')
            ->will($this->returnValue($psrQueue));

        $this->psrContextMock->expects($this->once())
            ->method('createConsumer')
            ->with($psrQueue)
            ->will($this->returnValue($psrConsumer));

        $psrConsumer->expects($this->once())
            ->method('receive')
            ->will($this->returnValue($psrMessage));

        $this->jobEntityManagerMock->expects($this->once())
            ->method('findJob')
            ->with(5)
            ->will($this->returnValue($job));

        $this->jobEntityManagerMock->expects($this->at(1))
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_IN_PROGRESS);

        $this->jobProcessorFactoryMock->expects($this->once())
            ->method('createJobProcessor')
            ->with($job)
            ->will($this->returnValue($jobProcessorMock));

        $jobProcessorMock->expects($this->once())
            ->method('processJob')
            ->with($job)
            ->will($this->throwException(new JobNoLongerValidException()));

        $this->jobEntityManagerMock->expects($this->at(2))
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_FAILED_NO_LONGER_VALID);

        $psrConsumer->expects($this->once())
            ->method('acknowledge')
            ->with($psrMessage);

        $this->worker->waitForJobAndExecute('notifications');
    }
}

