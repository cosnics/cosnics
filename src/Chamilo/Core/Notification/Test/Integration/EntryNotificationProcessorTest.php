<?php

namespace Chamilo\Core\Notification\Test\Source;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain\EntryNotificationJobParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntryNotificationJobProcessor;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;

/**
 * @package Chamilo\Core\Notification\Test\Source
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryNotificationProcessorTest extends DependencyInjectionBasedTestCase
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testProcessEntryNotificationJob()
    {
        $jobParameters = new EntryNotificationJobParameters(200);

        $job = new Job();
        $job->setProcessorClass(EntryNotificationJobProcessor::class);
        $job->setJobParameters($jobParameters);

        $this->getEntityNotificationProcessor()->processJob($job);
    }

    /**
     * @return EntryNotificationJobProcessor
     */
    protected function getEntityNotificationProcessor()
    {
        return $this->getService(EntryNotificationJobProcessor::class);
    }
}