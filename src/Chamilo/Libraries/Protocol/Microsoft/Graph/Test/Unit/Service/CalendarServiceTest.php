<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository;

/**
 * Tests the CalendarService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CalendarServiceTest extends ChamiloTestCase
{
    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $calendarRepositoryMock;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userServiceMock;

    /**
     * @var CalendarService
     */
    protected $calendarService;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->calendarRepositoryMock = $this->getMockBuilder(CalendarRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->userServiceMock = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()->getMock();

        $this->calendarService = new CalendarService($this->userServiceMock, $this->calendarRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->userServiceMock);
        unset($this->calendarRepositoryMock);
        unset($this->calendarService);
    }

    public function testListOwnedCalendars()
    {
        $user = new User();
        $azureUserIdentifier = 5;

        $calendars = [new \Microsoft\Graph\Model\Calendar()];

        $this->mockGetAzureUser($user);

        $this->calendarRepositoryMock->expects($this->once())
            ->method('listOwnedCalendars')
            ->with($azureUserIdentifier)
            ->will($this->returnValue($calendars));

        $this->assertEquals($calendars, $this->calendarService->listOwnedCalendars($user));
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function testListOwnedCalendarsWithoutUser()
    {
        $user = new User();
        $this->mockGetAzureUser($user, null);
        $this->calendarService->listOwnedCalendars($user);
    }

    public function testGetCalendarByIdentifier()
    {
        $user = new User();
        $azureUserIdentifier = 5;
        $calendarIdentifier = 10;

        $calendar = new \Microsoft\Graph\Model\Calendar();

        $this->mockGetAzureUser($user);

        $this->calendarRepositoryMock->expects($this->once())
            ->method('getCalendarByIdentifier')
            ->with($calendarIdentifier, $azureUserIdentifier)
            ->will($this->returnValue($calendar));

        $this->assertEquals($calendar, $this->calendarService->getCalendarByIdentifier($calendarIdentifier, $user));
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function testGetCalendarByIdentifierWithoutUser()
    {
        $user = new User();
        $calendarIdentifier = 10;

        $this->mockGetAzureUser($user, null);
        $this->calendarService->getCalendarByIdentifier($calendarIdentifier, $user);
    }

    public function testFindEventsForCalendarIdentifierAndBetweenDates()
    {
        $user = new User();
        $azureUserIdentifier = 5;
        $calendarIdentifier = 10;
        $fromDate = time() - 1000;
        $toDate = time() + 1000;

        $calendar = new \Microsoft\Graph\Model\Calendar();

        $this->mockGetAzureUser($user);

        $this->calendarRepositoryMock->expects($this->once())
            ->method('findEventsForCalendarIdentifierAndBetweenDates')
            ->with($calendarIdentifier, $azureUserIdentifier, $fromDate, $toDate)
            ->will($this->returnValue($calendar));

        $this->assertEquals(
            $calendar,
            $this->calendarService->findEventsForCalendarIdentifierAndBetweenDates(
                $calendarIdentifier, $user, $fromDate, $toDate
            )
        );
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function testFindEventsForCalendarIdentifierAndBetweenDatesWithoutUser()
    {
        $user = new User();
        $azureUserIdentifier = 5;
        $calendarIdentifier = 10;
        $fromDate = time() - 1000;
        $toDate = time() + 1000;

        $this->mockGetAzureUser($user, null);

        $this->calendarService->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier, $user, $fromDate, $toDate
        );
    }

    protected function mockGetAzureUser(User $user, $azureUserIdentifier = 5)
    {
        $this->userServiceMock->expects($this->atLeastOnce())
            ->method('getAzureUserIdentifier')
            ->with($user)
            ->will($this->returnValue($azureUserIdentifier));
    }

}

