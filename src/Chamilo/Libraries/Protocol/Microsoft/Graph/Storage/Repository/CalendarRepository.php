<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Microsoft\Graph\Model\Calendar;
use Microsoft\Graph\Model\Event;

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
        $this->setGraphRepository($graphRepository);
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param string $azureUserIdentifier
     * @param int $fromDate
     * @param int $toDate
     *
     * @return \Microsoft\Graph\Model\Event[] | \Microsoft\Graph\Model\Entity[]
     */
    public function findEventsForCalendarIdentifierAndBetweenDates(
        $calendarIdentifier, $azureUserIdentifier, $fromDate, $toDate
    )
    {
        $queryParameters = http_build_query(
            ['$top' => 200, 'startDateTime' => date('c', $fromDate), 'endDateTime' => date('c', $toDate)]
        );

        $result = $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/users/' . $azureUserIdentifier . '/calendars/' . $calendarIdentifier . '/calendarview?' .
            $queryParameters, Event::class, true
        );

        return $result;
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Calendar | \Microsoft\Graph\Model\Entity[]
     */
    public function getCalendarByIdentifier($calendarIdentifier, $azureUserIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/users/' . $azureUserIdentifier . '/calendars/' . $calendarIdentifier, Calendar::class
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    protected function getGraphRepository()
    {
        return $this->graphRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     */
    protected function setGraphRepository(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     *
     * @param string $azureUserIdentifier
     *
     * @return \Microsoft\Graph\Model\Calendar[] | \Microsoft\Graph\Model\Entity[]
     */
    public function listOwnedCalendars($azureUserIdentifier)
    {
        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/users/' . $azureUserIdentifier . '/calendars', Calendar::class, true
        );
    }
}