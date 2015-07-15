<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class GoogleCalendarService
{
    const PARAM_AUTHORIZATION_CODE = 'code';

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository
     */
    private $googleCalendarRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository $googleCalendarRepository
     */
    public function __construct(GoogleCalendarRepository $googleCalendarRepository)
    {
        $this->googleCalendarRepository = $googleCalendarRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository
     */
    public function getGoogleCalendarRepository()
    {
        return $this->googleCalendarRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository $googleCalendarRepository
     */
    public function setGoogleCalendarRepository(GoogleCalendarRepository $googleCalendarRepository)
    {
        $this->googleCalendarRepository = $googleCalendarRepository;
    }

    /**
     *
     * @return Google_Service_Calendar_CalendarListEntry[]
     */
    public function getOwnedCalendars()
    {
        return $this->getGoogleCalendarRepository()->findOwnedCalendars();
    }

    /**
     *
     * @return boolean
     */
    public function login($authenticationCode = null)
    {
        return $this->getGoogleCalendarRepository()->login($authenticationCode);
    }

    /**
     *
     * @return boolean
     */
    public function logout()
    {
        return $this->getGoogleCalendarRepository()->logout();
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Google_Service_Calendar_Event[]
     */
    public function getEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $fromDate, $toDate)
    {
        return $this->getGoogleCalendarRepository()->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier,
            $fromDate,
            $toDate)->getItems();
    }
}