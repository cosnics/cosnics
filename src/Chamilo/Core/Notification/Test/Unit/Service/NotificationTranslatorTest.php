<?php

namespace Chamilo\Core\Notification\Test\Unit\Service;

use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Domain\ViewingContext;
use Chamilo\Core\Notification\Service\NotificationTranslator;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Symfony\Component\Translation\Translator;

/**
 * Tests the NotificationTranslator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationTranslatorTest extends ChamiloTestCase
{
    /**
     * @var NotificationTranslator
     */
    protected $notificationTranslator;

    /**
     * @var \Symfony\Component\Translation\Translator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $translatorMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationTranslator = new NotificationTranslator($this->translatorMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->translatorMock);
        unset($this->notificationTranslator);
    }

    public function testCreateNotificationDescriptionContext()
    {
        $viewingContext = new ViewingContext(
            'Chamilo', new TranslationContext(
                'Chamilo\Application\Weblcms', 'NotificationNewAssignment',
                ['OBJECT' => new TranslationContext('Chamilo\Application\Weblcms', 'COURSE')]
            )
        );

        $this->translatorMock->expects($this->once())
            ->method('getFallbackLocales')
            ->will($this->returnValue(['en']));

        $this->translatorMock->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                ['COURSE', [], 'Chamilo\Application\Weblcms', 'en'],
                ['NotificationNewAssignment', ['OBJECT' => 'cOURSE'], 'Chamilo\Application\Weblcms', 'en']
            )
            ->will($this->returnArgument(0));

        $this->assertEquals(
            '[{"path":"Chamilo","en":"NotificationNewAssignment"}]',
            $this->notificationTranslator->createNotificationDescriptionContext([$viewingContext])
        );
    }

    public function testTranslateToAllLanguagesAndEncode()
    {
        $translationContext = new TranslationContext(
            'Chamilo\Application\Weblcms', 'NotificationNewAssignment',
            ['OBJECT' => new TranslationContext('Chamilo\Application\Weblcms', 'COURSE')]
        );

        $this->translatorMock->expects($this->once())
            ->method('getFallbackLocales')
            ->will($this->returnValue(['en']));

        $this->translatorMock->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                ['COURSE', [], 'Chamilo\Application\Weblcms', 'en'],
                ['NotificationNewAssignment', ['OBJECT' => 'cOURSE'], 'Chamilo\Application\Weblcms', 'en']
            )
            ->will($this->returnArgument(0));

        $this->assertEquals(
            '{"en":"NotificationNewAssignment"}',
            $this->notificationTranslator->translateToAllLanguagesAndEncode($translationContext)
        );
    }

    public function testGetTranslationFromNotification()
    {
        $this->translatorMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $viewingContextPath = 'Chamilo';

        $notification = new Notification();
        $notification->setDescriptionContext('[{"path":"Chamilo","en":"NotificationNewAssignment"}]');

        $this->assertEquals(
            'NotificationNewAssignment',
            $this->notificationTranslator->getTranslationFromNotification($notification, $viewingContextPath)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTranslationFromNotificationViewingContextNotFound()
    {
        $this->translatorMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $viewingContextPath = 'Chamilo\Application\Weblcms';

        $notification = new Notification();
        $notification->setDescriptionContext('[{"path":"Chamilo","en":"NotificationNewAssignment"}]');

        $this->notificationTranslator->getTranslationFromNotification($notification, $viewingContextPath);
    }

    public function testGetTranslationFromNotificationLocaleNotFound()
    {
        $this->translatorMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('nl'));

        $viewingContextPath = 'Chamilo';

        $notification = new Notification();
        $notification->setDescriptionContext('[{"path":"Chamilo","en":"NotificationNewAssignment"}]');

        $this->assertEquals(
            'NotificationNewAssignment',
            $this->notificationTranslator->getTranslationFromNotification($notification, $viewingContextPath)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTranslationFromNotificationTranslationInDefaultLocaleNotFound()
    {
        $this->translatorMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $viewingContextPath = 'Chamilo';

        $notification = new Notification();
        $notification->setDescriptionContext('[{"path":"Chamilo","nl":"NotificationNewAssignment"}]');

        $this->notificationTranslator->getTranslationFromNotification($notification, $viewingContextPath);
    }

    public function testGetTranslationFromFilter()
    {
        $filter = new Filter();
        $filter->setDescriptionContext('{"en":"NotificationNewAssignment"}');

        $this->translatorMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->assertEquals(
            'NotificationNewAssignment',
            $this->notificationTranslator->getTranslationFromFilter($filter)
        );
    }

    public function testGetTranslationFromFilterLocaleNotFound()
    {
        $filter = new Filter();
        $filter->setDescriptionContext('{"en":"NotificationNewAssignment"}');

        $this->translatorMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('nl'));

        $this->assertEquals(
            'NotificationNewAssignment',
            $this->notificationTranslator->getTranslationFromFilter($filter)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTranslationFromFilterDefaultLocaleNotFound()
    {
        $filter = new Filter();
        $filter->setDescriptionContext('{"nl":"NotificationNewAssignment"}');

        $this->translatorMock->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->notificationTranslator->getTranslationFromFilter($filter);
    }
}

