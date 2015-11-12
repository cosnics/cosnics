<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Architecture\ExternalCalendar;
use Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository;
use Chamilo\Application\Calendar\Extension\Office365\Service\CalendarService;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager extends ExternalCalendar
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate)
    {
        $calendarService = new CalendarService(CalendarRepository :: getInstance());
        $events = array();

        if ($calendarService->isAuthenticated())
        {
            $availabilityService = new AvailabilityService(new AvailabilityRepository());
            $package = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4);

            $activeAvailabilities = $availabilityService->getActiveAvailabilitiesForUserAndCalendarType(
                $calendarRendererProvider->getDataUser(),
                $package);

            while ($activeAvailability = $activeAvailabilities->next_result())
            {
                $eventResultSet = $calendarService->getEventsForCalendarIdentifierAndBetweenDates(
                    $activeAvailability->getCalendarId(),
                    $fromDate,
                    $toDate);

                $availableCalendar = $calendarService->getCalendarByIdentifier(
                    $activeAvailability->getCalendarId());

                while ($office365CalenderEvent = $eventResultSet->next_result())
                {
                    $eventParser = new EventParser($availableCalendar, $office365CalenderEvent, $fromDate, $toDate);
                    $events = array_merge($events, $eventParser->getEvents());
                }
            }
        }

        return $events;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars()
    {
        $calendarService = new CalendarService(CalendarRepository :: getInstance());
        $calendars = array();

        if ($calendarService->isAuthenticated())
        {
            $calendars = $calendarService->getOwnedCalendars();
        }

        return $calendars;
    }
}