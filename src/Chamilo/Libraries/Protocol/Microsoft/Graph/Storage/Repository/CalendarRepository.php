<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CalendarRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    private $graphRepository;

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     */
    public function __construct(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    public function getGraphRepository()
    {
        return $this->graphRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     */
    public function setGraphRepository(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     *
     * @return \Microsoft\Graph\Model\Calendar[]
     */
    public function listOwnedCalendars()
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            'me/calendars',
            \Microsoft\Graph\Model\Calendar::class);
    }

    /**
     *
     * @param string $calendarIdentifier
     * @return \Microsoft\Graph\Model\Calendar
     */
    public function getCalendarByIdentifier($calendarIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            'me/calendars/' . $calendarIdentifier,
            \Microsoft\Graph\Model\Calendar::class);
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Libraries\Storage\ResultSet\ArrayResultSet
     */
    public function findEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $fromDate, $toDate)
    {
        $queryParameters = http_build_query(
            ['$top' => 200, 'startDateTime' => date('c', $fromDate), 'endDateTime' => date('c', $toDate)]);

        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            'me/calendars/' . $calendarIdentifier . '/calendarview?' . $queryParameters,
            \Microsoft\Graph\Model\Calendar::class);
    }
}