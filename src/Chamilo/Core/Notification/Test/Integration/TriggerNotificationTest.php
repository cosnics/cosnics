<?php

namespace Chamilo\Core\Notification\Test\Source;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain\NotificationTriggerData;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntryNotificationProcessor;
use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;

/**
 * @package Chamilo\Core\Notification\Test\Source
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TriggerNotificationTest extends DependencyInjectionBasedTestCase
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testProcessNotificationTrigger()
    {
        $notificationTriggerData = new NotificationTriggerData(EntryNotificationProcessor::class, new \DateTime(), 200);
        $this->getEntityNotificationProcessor()->processNotificationTrigger($notificationTriggerData);
    }

    /**
     * @return EntryNotificationProcessor
     */
    protected function getEntityNotificationProcessor()
    {
        return $this->getService(EntryNotificationProcessor::class);
    }
}