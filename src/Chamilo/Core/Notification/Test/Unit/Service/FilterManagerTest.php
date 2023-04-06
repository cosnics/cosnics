<?php

namespace Chamilo\Core\Notification\Test\Unit\Service;

use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Service\FilterManager;
use Chamilo\Core\Notification\Service\NotificationContextManager;
use Chamilo\Core\Notification\Service\NotificationTranslator;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\NotificationContext;
use Chamilo\Core\Notification\Storage\Repository\FilterRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the FilterManager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterManagerTest extends ChamiloTestCase
{
    /**
     * @var \Chamilo\Core\Notification\Storage\Repository\FilterRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filterRepositoryMock;

    /**
     * @var \Chamilo\Core\Notification\Service\NotificationTranslator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationTranslatorMock;

    /**
     * @var \Chamilo\Core\Notification\Service\NotificationContextManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationContextManagerMock;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->filterRepositoryMock = $this->getMockBuilder(FilterRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationTranslatorMock = $this->getMockBuilder(NotificationTranslator::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationContextManagerMock = $this->getMockBuilder(NotificationContextManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->filterManager = new FilterManager(
            $this->filterRepositoryMock, $this->notificationContextManagerMock, $this->notificationTranslatorMock
        );
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->filterRepositoryMock);
        unset($this->notificationContextManagerMock);
        unset($this->notificationTranslatorMock);
        unset($this->filterManager);
    }

    public function testGetOrCreateFilterByContextPath()
    {
        $filter = new Filter();
        $notificationContext = new NotificationContext();
        $notificationContext->setPath('Chamilo\Application\Weblcms::Course:5');

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getOrCreateContextByPath')
            ->with('Chamilo\Application\Weblcms::Course:5')
            ->will($this->returnValue($notificationContext));

        $this->filterRepositoryMock->expects($this->once())
            ->method('findFilterByNotificationContext')
            ->with($notificationContext)
            ->will($this->returnValue($filter));

        $retrievedFilter = $this->filterManager->getOrCreateFilterByContextPath(
            'Chamilo\Application\Weblcms::Course:5',
            new TranslationContext('Chamilo\Application\Weblcms', 'FilterCourse')
        );

        $this->assertEquals($filter, $retrievedFilter);
    }

    public function testGetOrCreateFilterByContextPathForNewFilter()
    {
        $notificationContext = new NotificationContext();
        $notificationContext->setPath('Chamilo\Application\Weblcms::Course:5');

        $translationContext = new TranslationContext('Chamilo\Application\Weblcms', 'FilterCourse');

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getOrCreateContextByPath')
            ->with('Chamilo\Application\Weblcms::Course:5')
            ->will($this->returnValue($notificationContext));

        $this->filterRepositoryMock->expects($this->once())
            ->method('findFilterByNotificationContext')
            ->with($notificationContext)
            ->will($this->returnValue(null));

        $this->notificationTranslatorMock->expects($this->once())
            ->method('translateToAllLanguagesAndEncode')
            ->with($translationContext)
            ->will($this->returnValue('{}'));

        $this->filterRepositoryMock->expects($this->once())
            ->method('createFilter');

        $filter = $this->filterManager->getOrCreateFilterByContextPath(
            'Chamilo\Application\Weblcms::Course:5', $translationContext
        );

        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertEquals($notificationContext, $filter->getNotificationContext());
        $this->assertEquals('{}', $filter->getDescriptionContext());
    }
}

