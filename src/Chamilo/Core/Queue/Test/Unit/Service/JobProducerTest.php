<?php
namespace Chamilo\Core\Queue\Test\Unit\Service;

use Chamilo\Core\Queue\Service\JobEntityManager;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Service\Producer\ProducerInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Exception;

/**
 * Tests the JobProducer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobProducerTest extends ChamiloTestCase
{
    /**
     * @var JobProducer
     */
    protected $jobProducer;

    /**
     * @var \Chamilo\Core\Queue\Service\JobEntityManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobEntityManagerMock;

    /**
     * @var \Chamilo\Core\Queue\Service\Producer\ProducerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $producerMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->jobEntityManagerMock = $this->getMockBuilder(JobEntityManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->producerMock = $this->getMockBuilder(ProducerInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->jobProducer = new JobProducer($this->producerMock, $this->jobEntityManagerMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->producerMock);
        unset($this->jobEntityManagerMock);
        unset($this->jobProducer);
    }

    public function testProduceJob()
    {
        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->jobEntityManagerMock->expects($this->once())
            ->method('createJob')
            ->with($job);

        $this->producerMock->expects($this->once())
            ->method('produceMessage')
            ->with($job->getId(), 'notifications', 500);

        $this->jobEntityManagerMock->expects($this->once())
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_SENT_TO_QUEUE);

        $this->jobProducer->produceJob($job, 'notifications', 500);
    }

    public function testProduceJobWithException()
    {
        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->jobEntityManagerMock->expects($this->once())
            ->method('createJob')
            ->with($job);

        $this->producerMock->expects($this->once())
            ->method('produceMessage')
            ->with($job->getId(), 'notifications', 500)
            ->will($this->throwException(new Exception()));

        $this->jobEntityManagerMock->expects($this->once())
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_FAILED_RETRY);

        $this->jobProducer->produceJob($job, 'notifications', 500);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @expectedException \Exception
     */
    public function testProduceWithJobCreationException()
    {
        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->jobEntityManagerMock->expects($this->once())
            ->method('createJob')
            ->with($job)
            ->will($this->throwException(new Exception()));

        $this->jobProducer->produceJob($job, 'notifications', 500);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @expectedException \Exception
     */
    public function testProduceJobWithExceptionAndChangeJobStatusException()
    {
        $job = new Job();
        $this->set_property_value($job, 'id', 5);

        $this->jobEntityManagerMock->expects($this->once())
            ->method('createJob')
            ->with($job);

        $this->producerMock->expects($this->once())
            ->method('produceMessage')
            ->with($job->getId(), 'notifications', 500)
            ->will($this->throwException(new Exception()));

        $this->jobEntityManagerMock->expects($this->once())
            ->method('changeJobStatus')
            ->with($job, Job::STATUS_FAILED_RETRY)
            ->will($this->throwException(new Exception()));

        $this->jobProducer->produceJob($job, 'notifications', 500);
    }
}

