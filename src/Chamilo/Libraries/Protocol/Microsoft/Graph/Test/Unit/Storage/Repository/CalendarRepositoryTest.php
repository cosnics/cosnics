<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Storage\Repository;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;

/**
 * Tests the CalendarRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CalendarRepositoryTest extends ChamiloTestCase
{
    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    private $graphRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->graphRepositoryMock = $this->getMockBuilder(GraphRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->calendarRepository = new CalendarRepository($this->graphRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->graphRepositoryMock);
        unset($this->calendarRepository);
    }

    public function testListOwnedCalendars()
    {
        $azureUserIdentifier = 5;

        $calendar = new \Microsoft\Graph\Model\Calendar();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with('/users/' . $azureUserIdentifier . '/calendars', \Microsoft\Graph\Model\Calendar::class, true)
            ->will($this->returnValue([$calendar]));

        $this->assertEquals([$calendar], $this->calendarRepository->listOwnedCalendars($azureUserIdentifier));
    }

    public function testGetCalendarByIdentifier()
    {
        $azureUserIdentifier = 5;
        $calendarIdentifier = 10;

        $calendar = new \Microsoft\Graph\Model\Calendar();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/users/' . $azureUserIdentifier . '/calendars/' . $calendarIdentifier,
                \Microsoft\Graph\Model\Calendar::class, null
            )
            ->will($this->returnValue($calendar));

        $this->assertEquals(
            $calendar, $this->calendarRepository->getCalendarByIdentifier($calendarIdentifier, $azureUserIdentifier)
        );
    }

    public function testFindEventsForCalendarIdentifierAndBetweenDates()
    {
        $azureUserIdentifier = 5;
        $calendarIdentifier = 10;

        $fromDate = date('c', time() - 1000);
        $toDate = date('c', time() + 1000);

        $queryParameters = http_build_query(['$top' => 200, 'startDateTime' => $fromDate, 'endDateTime' => $toDate]);
        $endpoint = '/users/5/calendars/10/calendarview?' . $queryParameters;

        $calendar = new \Microsoft\Graph\Model\Calendar();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with($endpoint, \Microsoft\Graph\Model\Event::class, true)
            ->will($this->returnValue($calendar));

        $this->assertEquals(
            $calendar,
            $this->calendarRepository->findEventsForCalendarIdentifierAndBetweenDates(
                $calendarIdentifier, $azureUserIdentifier, time() - 1000, time() + 1000
            )
        );
    }

}

