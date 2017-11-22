<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Service;

use Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarService
{
    const PARAM_AUTHORIZATION_CODE = 'code';

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository
     */
    private $calendarRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository
     */
    public function getCalendarRepository()
    {
        return $this->calendarRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository $calendarRepository
     */
    public function setCalendarRepository(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     *
     * @return \stdClass[]
     */
    public function getOwnedCalendars()
    {
        return $this->getCalendarRepository()->findOwnedCalendars();
    }

    /**
     *
     * @param string $calendarIdentifier
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar
     */
    public function getCalendarByIdentifier($calendarIdentifier)
    {
        return $this->getCalendarRepository()->findCalendarByIdentifier($calendarIdentifier);
    }

    /**
     *
     * @return boolean
     */
    public function login($authenticationCode = null)
    {
        return $this->getCalendarRepository()->login($authenticationCode);
    }

    /**
     *
     * @return boolean
     */
    public function logout()
    {
        return $this->getCalendarRepository()->logout();
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Application\Calendar\Extension\Office365\EventResultSet
     */
    public function getEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $fromDate, $toDate)
    {
        return $this->getCalendarRepository()->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier,
            $fromDate,
            $toDate);
    }

    /**
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->getCalendarRepository()->hasAccessToken();
    }
}