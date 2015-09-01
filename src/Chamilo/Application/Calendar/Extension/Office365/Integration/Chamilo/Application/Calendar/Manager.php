<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Architecture\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Office365\Service\Office365CalendarService;
use Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Architecture\ExternalCalendarInterface;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager implements CalendarInterface, ExternalCalendarInterface
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate)
    {
        $office365CalendarService = new Office365CalendarService(Office365CalendarRepository :: getInstance());
        $events = array();

        if ($office365CalendarService->isAuthenticated())
        {
            $availabilityService = new AvailabilityService(new AvailabilityRepository());
            $package = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4);

            $activeAvailabilities = $availabilityService->getActiveAvailabilitiesForUserAndCalendarType(
                $renderer->getDataProvider()->getDataUser(),
                $package);

            while ($activeAvailability = $activeAvailabilities->next_result())
            {
                $eventResultSet = $office365CalendarService->getEventsForCalendarIdentifierAndBetweenDates(
                    $activeAvailability->getCalendarId(),
                    $fromDate,
                    $toDate);

                $availableCalendar = $office365CalendarService->getCalendarByIdentifier(
                    $activeAvailability->getCalendarId());

                while ($office365CalenderEvent = $eventResultSet->next_result())
                {
                    $eventParser = new EventParser(
                        $renderer,
                        $availableCalendar,
                        $office365CalenderEvent,
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
        $office365CalendarService = new Office365CalendarService(Office365CalendarRepository :: getInstance());
        $calendars = array();

        if ($office365CalendarService->isAuthenticated())
        {
            $calendars = $office365CalendarService->getOwnedCalendars();
        }

        return $calendars;
    }
}