<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\CalendarProperties;
use Chamilo\Application\Calendar\Extension\Google\EventResultSet;
use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarService
{
    const PARAM_AUTHORIZATION_CODE = 'code';

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository
     */
    private $calendarRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     *
     * @param string $summary
     * @param string $description
     * @param string $timeZone
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\CalendarProperties
     */
    private function getCalendarProperties($summary, $description, $timeZone)
    {
        return new CalendarProperties($summary, $description, $timeZone);
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository
     */
    public function getCalendarRepository()
    {
        return $this->calendarRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository $calendarRepository
     */
    public function setCalendarRepository(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     * @throws \Exception
     */
    protected function getConfigurablePathBuilder()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurablePathBuilder::class
        );
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\EventResultSet
     */
    public function getEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $fromDate, $toDate)
    {
        $eventsCacheService =
            new EventsCacheService($this->getCalendarRepository(), $this->getConfigurablePathBuilder());
        $googleCalendarEvents = $eventsCacheService->getEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier, $fromDate, $toDate
        );

        return new EventResultSet(
            $this->getCalendarProperties(
                $googleCalendarEvents->getSummary(), $googleCalendarEvents->getDescription(),
                $googleCalendarEvents->getTimeZone()
            ), $googleCalendarEvents->getItems()
        );
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getOwnedCalendars()
    {
        $ownedCalendarsCacheService =
            new OwnedCalendarsCacheService($this->getCalendarRepository(), $this->getConfigurablePathBuilder());

        return $ownedCalendarsCacheService->getOwnedCalendars();
    }

    /**
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->getCalendarRepository()->hasAccessToken();
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
}