<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService;
use Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager implements CalendarInterface
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate)
    {
        $googleCalendarService = new GoogleCalendarService(GoogleCalendarRepository :: getInstance());
        $events = array();

        if ($googleCalendarService->isAuthenticated())
        {
            $availabilityService = new AvailabilityService(new AvailabilityRepository());
            $package = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4);

            $activeAvailabilities = $availabilityService->getActiveAvailabilitiesForUserAndCalendarType(
                $renderer->getDataProvider()->getDataUser(),
                $package);

            while ($activeAvailability = $activeAvailabilities->next_result())
            {
                $eventResultSet = $googleCalendarService->getEventsForCalendarIdentifierAndBetweenDates(
                    $activeAvailability->getCalendarId(),
                    $fromDate,
                    $toDate);

                while ($googleCalenderEvent = $eventResultSet->next_result())
                {
                    $eventParser = new EventParser(
                        $renderer,
                        $eventResultSet->getCalendarProperties(),
                        $googleCalenderEvent,
                        $fromDate,
                        $toDate);
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
        $googleCalendarService = new GoogleCalendarService(GoogleCalendarRepository :: getInstance());
        $calendars = array();

        if ($googleCalendarService->isAuthenticated())
        {
            $calendars = $googleCalendarService->getOwnedCalendars();
        }

        return $calendars;
    }
}