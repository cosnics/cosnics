<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Service;

use Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository;

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
     * @return \stdClass[]
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
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->getOffice365CalendarRepository()->hasAccessToken();
    }
}