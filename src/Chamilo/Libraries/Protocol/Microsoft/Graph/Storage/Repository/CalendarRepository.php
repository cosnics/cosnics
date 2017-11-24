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
        $this->setGraphRepository($graphRepository);
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
            '/users/' . $azureUserIdentifier . '/calendars',
            \Microsoft\Graph\Model\Calendar::class, true
        );
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
            '/users/' . $azureUserIdentifier . '/calendars/' . $calendarIdentifier,
            \Microsoft\Graph\Model\Calendar::class, true
        );
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param string $azureUserIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return \Microsoft\Graph\Model\Event[] | \Microsoft\Graph\Model\Entity[]
     */
    public function findEventsForCalendarIdentifierAndBetweenDates(
        $calendarIdentifier, $azureUserIdentifier, $fromDate,
        $toDate
    )
    {
        $queryParameters = http_build_query(
            ['$top' => 200, 'startDateTime' => date('c', $fromDate), 'endDateTime' => date('c', $toDate)]
        );

        return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
            '/users/' . $azureUserIdentifier . '/calendars/' . $calendarIdentifier . '/calendarview?' .
            $queryParameters,
            \Microsoft\Graph\Model\Event::class, true
        );
    }
}