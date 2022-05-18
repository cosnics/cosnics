<?php

namespace Chamilo\Core\Notification\Test\Unit\Service;

use Chamilo\Core\Notification\Domain\NotificationDTO;
use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Domain\ViewingContext;
use Chamilo\Core\Notification\Service\NotificationContextManager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\Notification\Service\NotificationTranslator;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Core\Notification\Storage\Entity\NotificationContext;
use Chamilo\Core\Notification\Storage\Entity\UserNotification;
use Chamilo\Core\Notification\Storage\Repository\NotificationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use DateTime;
use RuntimeException;

/**
 * Tests the NotificationManager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationManagerTest extends ChamiloTestCase
{
    /**
     * @var NotificationManager
     */
    protected $notificationManager;

    /**
     * @var \Chamilo\Core\Notification\Storage\Repository\NotificationRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationRepositoryMock;

    /**
     * @var \Chamilo\Core\Notification\Service\NotificationTranslator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationTranslatorMock;

    /**
     * @var \Chamilo\Core\Notification\Service\NotificationContextManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationContextManagerMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->notificationRepositoryMock = $this->getMockBuilder(NotificationRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationTranslatorMock = $this->getMockBuilder(NotificationTranslator::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationContextManagerMock = $this->getMockBuilder(NotificationContextManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationManager = new NotificationManager(
            $this->notificationRepositoryMock, $this->notificationContextManagerMock, $this->notificationTranslatorMock
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->notificationRepositoryMock);
        unset($this->notificationTranslatorMock);
        unset($this->notificationContextManagerMock);
        unset($this->notificationManager);
    }

    public function testCreateNotificationForUsers()
    {
        $viewingContexts = [
            new ViewingContext(
                'Chamilo', new TranslationContext('Chamilo\Application\Weblcms', 'CourseViewingContext', [])
            )
        ];

        $filters = [
            new Filter()
        ];

        $contextPaths = [
            'Chamilo',
            'Chamilo\Application\Weblcms::Course:5'
        ];

        $date = new DateTime();

        $notificationContext = new NotificationContext();
        $notificationContext->setPath('Chamilo');

        $this->notificationTranslatorMock->expects($this->once())
            ->method('createNotificationDescriptionContext')
            ->with($viewingContexts)
            ->will($this->returnValue('{"description_context"}'));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('createNotification')
            ->with(
                $this->callback(
                    function (Notification $notification) use ($filters, $date) {
                        return $notification->getFilters() == $filters &&
                            $notification->getDate() == $date &&
                            $notification->getDescriptionContext() == '{"description_context"}';
                    }
                )
            );

        $this->notificationContextManagerMock->expects($this->exactly(2))
            ->method('getOrCreateContextByPath')
            ->will($this->returnValue($notificationContext));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('createUserNotifications')
            ->with(
                $this->callback(
                    function ($userNotifications) {
                        return count($userNotifications) == 4;
                    }
                )
            );

        $this->notificationManager->createNotificationForUsers(
            'index.php', $viewingContexts, $date, [2, 5], $filters, $contextPaths
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCreateNotificationForUsersWithInvalidFilter()
    {
        $viewingContexts = [
            new ViewingContext(
                'Chamilo', new TranslationContext('Chamilo\Application\Weblcms', 'CourseViewingContext', [])
            )
        ];

        $filters = [
            new NotificationContext()
        ];

        $contextPaths = [
            'Chamilo',
            'Chamilo\Application\Weblcms::Course:5'
        ];

        $date = new DateTime();

        $notificationContext = new NotificationContext();
        $notificationContext->setPath('Chamilo');

        $this->notificationManager->createNotificationForUsers(
            'index.php', $viewingContexts, $date, [2, 5], $filters, $contextPaths
        );
    }

    public function testFormatNotifications()
    {
        $filter = new Filter();
        $this->set_property_value($filter, 'id', 5);
        $filter->setDescriptionContext('filter-description-context');

        $userNotification = new UserNotification();
        $userNotification->setRead(false);
        $userNotification->setViewed(false);

        $dateTime = new DateTime();

        $notification = new Notification();
        $this->set_property_value($notification, 'id', 10);
        $notification->setDescriptionContext('notification-description-context');
        $notification->setDate($dateTime);
        $notification->setUsers([$userNotification]);
        $notification->setFilters([$filter]);

        $this->notificationTranslatorMock->expects($this->once())
            ->method('getTranslationFromFilter')
            ->with($filter)
            ->will($this->returnValue('Filter 1'));

        $this->notificationTranslatorMock->expects($this->once())
            ->method('getTranslationFromNotification')
            ->with($notification)
            ->will($this->returnValue('Notification 1'));

        $formattedNotifications = $this->notificationManager->formatNotifications([$notification], 'Chamilo');
        $result = $formattedNotifications[0];

        $this->assertInstanceOf(NotificationDTO::class, $result);

        $this->assertEquals(10, $result->getId());
        $this->assertEquals('Notification 1', $result->getMessage());
        $this->assertEquals($dateTime->format("d/m/Y,  H:i"), $result->getTime());
        $this->assertEquals(false, $result->isRead());
        $this->assertEquals(true, $result->isNew());
        $this->assertEquals(5, $result->getFilters()[0]->getId());
        $this->assertEquals('Filter 1', $result->getFilters()[0]->getDescription());
    }

    public function testGetNotificationById()
    {
        $notification = new Notification();

        $this->notificationRepositoryMock->expects($this->once())
            ->method('find')
            ->with(5)
            ->will($this->returnValue($notification));

        $this->assertEquals($notification, $this->notificationManager->getNotificationById(5));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetNotificationByIdWithInvalidId()
    {
        $this->notificationManager->getNotificationById(null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetNotificationByIdWithNoNotificationFound()
    {
        $this->notificationRepositoryMock->expects($this->once())
            ->method('find')
            ->with(5)
            ->will($this->returnValue(null));

        $this->notificationManager->getNotificationById(5);
    }

    public function testGetNotificationsByContextPathsForUser()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $context = new NotificationContext();

        $notification = new Notification();
        $userNotification = new UserNotification();
        $userNotification->setNotification($notification);

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->returnValue($context));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('findUserNotificationsByContextsForUser')
            ->with([$context], $user, 0, 10)
            ->will($this->returnValue([$userNotification]));

        $notifications = $this->notificationManager->getNotificationsByContextPathsForUser([$contextPath], $user, 0, 10);
        $this->assertEquals($notification, $notifications[0]);
        $this->assertEquals($userNotification, $notifications[0]->getUsers()[0]);
    }

    public function testGetNotificationsByContextPathsForUserWithExceptionInContextPath()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->throwException(new RuntimeException()));

        $notifications = $this->notificationManager->getNotificationsByContextPathsForUser([$contextPath], $user, 0, 10);
        $this->assertEmpty($notifications);
    }

    public function testGetNotificationsByContextPathsForUserWithoutContextPath()
    {
        $user = new User();

        $this->notificationContextManagerMock->expects($this->never())
            ->method('getContextByPath');

        $notifications = $this->notificationManager->getNotificationsByContextPathsForUser([], $user, 0, 10);
        $this->assertEmpty($notifications);
    }

    public function testGetNotificationsByContextPathForUser()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $context = new NotificationContext();

        $notification = new Notification();
        $userNotification = new UserNotification();
        $userNotification->setNotification($notification);

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->returnValue($context));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('findUserNotificationsByContextsForUser')
            ->with([$context], $user, 0, 10)
            ->will($this->returnValue([$userNotification]));

        $notifications = $this->notificationManager->getNotificationsByContextPathForUser($contextPath, $user, 0, 10);
        $this->assertEquals($notification, $notifications[0]);
        $this->assertEquals($userNotification, $notifications[0]->getUsers()[0]);
    }

    public function testCountUnseenNotificationsByContextPathsForUser()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $context = new NotificationContext();

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->returnValue($context));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('countUnseenNotificationsByContextsForUser')
            ->with([$context], $user)
            ->will($this->returnValue(5));

        $count = $this->notificationManager->countUnseenNotificationsByContextPathsForUser([$contextPath], $user);
        $this->assertEquals(5, $count);
    }

    public function testCountUnseenNotificationsByContextPathsForUserWithExceptionInContextPath()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->throwException(new RuntimeException()));

        $count = $this->notificationManager->countUnseenNotificationsByContextPathsForUser([$contextPath], $user);
        $this->assertEquals(0, $count);
    }

    public function testCountUnseenNotificationsByContextPathsForUserWithoutContextPath()
    {
        $user = new User();

        $this->notificationContextManagerMock->expects($this->never())
            ->method('getContextByPath');

        $count = $this->notificationManager->countUnseenNotificationsByContextPathsForUser([], $user);
        $this->assertEquals(0, $count);
    }

    public function testCountUnseenNotificationsByContextPathForUser()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $context = new NotificationContext();

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->returnValue($context));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('countUnseenNotificationsByContextsForUser')
            ->with([$context], $user)
            ->will($this->returnValue(5));

        $count = $this->notificationManager->countUnseenNotificationsByContextPathForUser($contextPath, $user);
        $this->assertEquals(5, $count);
    }

    public function testSetNotificationsViewedForUserAndContextPaths()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $context = new NotificationContext();

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->returnValue($context));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('setNotificationsViewedForUserAndContexts')
            ->with([$context], $user);

        $this->notificationManager->setNotificationsViewedForUserAndContextPaths([$contextPath], $user);
    }

    public function testSetNotificationsViewedForUserAndContextPathWithExceptionInContextPaths()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->throwException(new RuntimeException()));

        $this->notificationManager->setNotificationsViewedForUserAndContextPaths([$contextPath], $user);
    }

    public function testSetNotificationsViewedForUserAndContextPathWithoutContextPaths()
    {
        $user = new User();

        $this->notificationContextManagerMock->expects($this->never())
            ->method('getContextByPath');

        $this->notificationManager->setNotificationsViewedForUserAndContextPaths([], $user);
    }

    public function testSetNotificationsViewedForUserAndContextPath()
    {
        $contextPath = 'Chamilo\Application\Weblcms::Course:5';
        $user = new User();

        $context = new NotificationContext();

        $this->notificationContextManagerMock->expects($this->once())
            ->method('getContextByPath')
            ->with($contextPath)
            ->will($this->returnValue($context));

        $this->notificationRepositoryMock->expects($this->once())
            ->method('setNotificationsViewedForUserAndContexts')
            ->with([$context], $user);

        $this->notificationManager->setNotificationsViewedForUserAndContextPath($contextPath, $user);
    }

    public function testSetNotificationsViewedForUser()
    {
        $notification = new Notification();
        $user = new User();

        $this->notificationRepositoryMock->expects($this->once())
            ->method('setNotificationsViewedForUser')
            ->with([$notification], $user);

        $this->notificationManager->setNotificationsViewedForUser([$notification], $user);
    }

    public function testSetNotificationReadForUser()
    {
        $notification = new Notification();
        $user = new User();

        $this->notificationRepositoryMock->expects($this->once())
            ->method('setNotificationReadForUser')
            ->with($notification, $user);

        $this->notificationManager->setNotificationReadForUser($notification, $user);
    }

    public function testCanUserViewNotification()
    {
        $notification = new Notification();
        $user = new User();

        $this->notificationRepositoryMock->expects($this->once())
            ->method('countUserNotificationsByNotificationAndUser')
            ->with($notification, $user)
            ->will($this->returnValue(5));

        $this->assertTrue($this->notificationManager->canUserViewNotification($notification, $user));
    }

    public function testCanUserViewNotificationWhenNot()
    {
        $notification = new Notification();
        $user = new User();

        $this->notificationRepositoryMock->expects($this->once())
            ->method('countUserNotificationsByNotificationAndUser')
            ->with($notification, $user)
            ->will($this->returnValue(0));

        $this->assertFalse($this->notificationManager->canUserViewNotification($notification, $user));
    }
}


