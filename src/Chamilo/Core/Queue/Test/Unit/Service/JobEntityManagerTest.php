<?php
namespace Chamilo\Core\Queue\Test\Unit\Service;

use Chamilo\Core\Queue\Service\JobEntityManager;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Queue\Storage\Repository\JobEntityRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the JobEntityManager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class JobEntityManagerTest extends ChamiloTestCase
{
    /**
     * @var JobEntityManager
     */
    protected $jobEntityManager;

    /**
     * @var \Chamilo\Core\Queue\Storage\Repository\JobEntityRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobEntityRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->jobEntityRepositoryMock = $this->getMockBuilder(JobEntityRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->jobEntityManager = new JobEntityManager($this->jobEntityRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->jobEntityRepositoryMock);
        unset($this->jobEntityManager);
    }

    public function testCreateJob()
    {
        $job = new Job();

        $this->jobEntityRepositoryMock->expects($this->once())
            ->method('createJobEntity')
            ->with($job);

        $this->jobEntityManager->createJob($job);

        $this->assertEquals(Job::STATUS_CREATED, $job->getStatus());
        $this->assertNotNull($job->getDate());
    }

    public function testChangeJobStatus()
    {
        $job = new Job();

        $this->jobEntityRepositoryMock->expects($this->once())
            ->method('updateJobEntity')
            ->with($job);

        $this->jobEntityManager->changeJobStatus($job, Job::STATUS_FAILED_NO_LONGER_VALID);
        $this->assertEquals(Job::STATUS_FAILED_NO_LONGER_VALID, $job->getStatus());
    }

    public function testFindJob()
    {
        $job = new Job();

        $this->jobEntityRepositoryMock->expects($this->once())
            ->method('find')
            ->with(5)
            ->will($this->returnValue($job));

        $this->assertEquals($job, $this->jobEntityManager->findJob(5));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFindJobWhenNotFound()
    {
        $job = new Job();

        $this->jobEntityRepositoryMock->expects($this->once())
            ->method('find')
            ->with(5)
            ->will($this->returnValue(null));

        $this->jobEntityManager->findJob(5);
    }

}


