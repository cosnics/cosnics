<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Service;

use Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository;
use Chamilo\Application\Calendar\Extension\Office365\CalendarProperties;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Office365CalendarService
{
    const PARAM_AUTHORIZATION_CODE = 'code';

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository
     */
    private $office365CalendarRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository $office365CalendarRepository
     */
    public function __construct(Office365CalendarRepository $office365CalendarRepository)
    {
        $this->office365CalendarRepository = $office365CalendarRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository
     */
    public function getOffice365CalendarRepository()
    {
        return $this->office365CalendarRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository $office365CalendarRepository
     */
    public function setOffice365CalendarRepository(Office365CalendarRepository $office365CalendarRepository)
    {
        $this->office365CalendarRepository = $office365CalendarRepository;
    }

    /**
     *
     * @return Office365_Service_Calendar_CalendarListEntry[]
     */
    public function getOwnedCalendars()
    {
        return $this->getOffice365CalendarRepository()->findOwnedCalendars();
    }

    /**
     *
     * @return boolean
     */
    public function login($authenticationCode = null)
    {
        return $this->getOffice365CalendarRepository()->login($authenticationCode);
    }

    /**
     *
     * @return boolean
     */
    public function logout()
    {
        return $this->getOffice365CalendarRepository()->logout();
    }

    /**
     *
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Application\Calendar\Extension\Office365\EventResultSet
     */
    public function getEventsBetweenDates($fromDate, $toDate)
    {
        return $this->getOffice365CalendarRepository()->findEventsBetweenDates($fromDate, $toDate);
    }

    /**
     *
     * @param string $summary
     * @param string $description
     * @param string $timeZone
     * @return \Chamilo\Application\Calendar\Extension\Office365\CalendarProperties
     */
    private function getCalendarProperties($summary, $description, $timeZone)
    {
        return new CalendarProperties($summary, $description, $timeZone);
    }
}