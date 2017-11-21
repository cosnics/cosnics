<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @param string $azureUserIdentifier
     * @return \Microsoft\Graph\Model\Calendar[]
     */
    public function listOwnedCalendars($azureUserIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/users/' . $azureUserIdentifier . '/calendars',
            \Microsoft\Graph\Model\Calendar::class);
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param string $azureUserIdentifier
     * @return \Microsoft\Graph\Model\Calendar
     */
    public function getCalendarByIdentifier($calendarIdentifier, $azureUserIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/users/' . $azureUserIdentifier . '/calendars/' . $calendarIdentifier,
            \Microsoft\Graph\Model\Calendar::class);
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param string $azureUserIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Microsoft\Graph\Model\Event[]
     */
    public function findEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $azureUserIdentifier, $fromDate,
        $toDate)
    {
        $queryParameters = http_build_query(
            ['$top' => 200, 'startDateTime' => date('c', $fromDate), 'endDateTime' => date('c', $toDate)]);

        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/users/' . $azureUserIdentifier . '/calendars/' . $calendarIdentifier . '/calendarview?' . $queryParameters,
            \Microsoft\Graph\Model\Event::class);
    }
}