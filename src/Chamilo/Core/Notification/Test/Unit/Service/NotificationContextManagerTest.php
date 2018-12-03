<?php

namespace Chamilo\Core\Notification\Test\Unit\Service;

use Chamilo\Core\Notification\Service\NotificationContextManager;
use Chamilo\Core\Notification\Storage\Entity\NotificationContext;
use Chamilo\Core\Notification\Storage\Repository\NotificationContextRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the NotificationContextManager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationContextManagerTest extends ChamiloTestCase
{
    /**
     * @var NotificationContextManager
     */
    protected $notificationContextManager;

    /**
     * @var \Chamilo\Core\Notification\Storage\Repository\NotificationContextRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationContextRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->notificationContextRepositoryMock = $this->getMockBuilder(NotificationContextRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationContextManager = new NotificationContextManager($this->notificationContextRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->notificationContextRepositoryMock);
        unset($this->notificationContextManager);
    }

    public function testGetContextByPath()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';

        $notificationContext = new NotificationContext();
        $notificationContext->setPath($contextPath);

        $this->notificationContextRepositoryMock->expects($this->once())
            ->method('findByPath')
            ->with($contextPath)
            ->will($this->returnValue($notificationContext));

        $this->assertEquals($notificationContext, $this->notificationContextManager->getContextByPath($contextPath));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetContextByPathWithInvalidPath()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';

        $notificationContext = new NotificationContext();
        $notificationContext->setPath($contextPath);

        $this->notificationContextRepositoryMock->expects($this->once())
            ->method('findByPath')
            ->with($contextPath)
            ->will($this->returnValue(null));

        $this->notificationContextManager->getContextByPath($contextPath);
    }

    public function testGetContextByPathUsesCaching()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';

        $notificationContext = new NotificationContext();
        $notificationContext->setPath($contextPath);

        $this->notificationContextRepositoryMock->expects($this->once())
            ->method('findByPath')
            ->with($contextPath)
            ->will($this->returnValue($notificationContext));

        $this->assertEquals($notificationContext, $this->notificationContextManager->getContextByPath($contextPath));
        $this->assertEquals($notificationContext, $this->notificationContextManager->getContextByPath($contextPath));
    }

    public function testGetOrCreateContextByPath()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';

        $notificationContext = new NotificationContext();
        $notificationContext->setPath($contextPath);

        $this->notificationContextRepositoryMock->expects($this->once())
            ->method('findByPath')
            ->with($contextPath)
            ->will($this->returnValue($notificationContext));

        $this->assertEquals($notificationContext, $this->notificationContextManager->getOrCreateContextByPath($contextPath));
    }

    public function testGetOrCreateContextByPathWithNewContext()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';

        $this->notificationContextRepositoryMock->expects($this->once())
            ->method('findByPath')
            ->with($contextPath)
            ->will($this->returnValue(null));

        $this->notificationContextRepositoryMock->expects($this->once())
            ->method('createNotificationContext')
            ->with($this->callback(function(NotificationContext $notificationContext) use ($contextPath) {
                return $notificationContext->getPath() == $contextPath;
            }));

        $notificationContext = $this->notificationContextManager->getOrCreateContextByPath($contextPath);
        $this->assertInstanceof(NotificationContext::class, $notificationContext);
        $this->assertEquals($contextPath, $notificationContext->getPath());
    }

    public function testGetOrCreateContextByPathUsesCache()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';

        $notificationContext = new NotificationContext();
        $notificationContext->setPath($contextPath);

        $this->notificationContextRepositoryMock->expects($this->once())
            ->method('findByPath')
            ->with($contextPath)
            ->will($this->returnValue($notificationContext));

        $this->assertEquals($notificationContext, $this->notificationContextManager->getOrCreateContextByPath($contextPath));
        $this->assertEquals($notificationContext, $this->notificationContextManager->getOrCreateContextByPath($contextPath));
    }
}


