<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\ExternalCalendar;
use Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Calendar\Service\CalendarRendererProvider;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarEventDataProvider extends ExternalCalendar
{
    use DependencyInjectionContainerTrait;

    public function __construct()
    {
        $this->initializeContainer();
    }

    /**
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    protected function getAvailabilityService()
    {
        return $this->getService(AvailabilityService::class);
    }

    /**
     * @param CalendarService $calendarService
     * @param string $calendarId
     * @param int $fromDate
     * @param int $toDate
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    private function getCalendarEvents(CalendarService $calendarService, $calendarId, $fromDate, $toDate)
    {
        $eventIterator = $calendarService->getEventsForCalendarIdentifierAndBetweenDates(
            $calendarId, $fromDate, $toDate
        );

        $events = [];

        foreach ($eventIterator as $googleCalenderEvent)
        {
            $eventParser = new EventParser(
                $eventIterator->getCalendarProperties(), $googleCalenderEvent, $fromDate, $toDate
            );
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider $calendarRendererProvider
     *
     * @return string[]
     */
    private function getCalendarIdentifiers(
        CalendarRendererProvider $calendarRendererProvider
    )
    {
        $package = ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 5);

        $availabilities = $this->getAvailabilityService()->getAvailabilitiesForUserAndCalendarType(
            $calendarRendererProvider->getDataUser(), $package
        );

        $calendarIdentifiers = [];

        if ($availabilities->count() == 0)
        {
            $availableCalendars = $this->getCalendars();

            foreach ($availableCalendars as $availableCalendar)
            {
                $calendarIdentifiers[] = $availableCalendar->getIdentifier();
            }
        }
        else
        {
            foreach ($availabilities as $availability)
            {
                if ($availability->isActive())
                {
                    $calendarIdentifiers[] = $availability->getCalendarId();
                }
            }
        }

        return $calendarIdentifiers;
    }

    /**
     * @see \Chamilo\Application\Calendar\Architecture\CalendarInterface::getCalendars()
     */
    public function getCalendars(User $user = null): array
    {
        $calendarService = new CalendarService(CalendarRepository::getInstance());
        $calendars = [];

        if ($calendarService->isAuthenticated())
        {
            $calendars = $calendarService->getOwnedCalendars();
        }

        return $calendars;
    }

    /**
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(CalendarRendererProvider $calendarRendererProvider, $fromDate, $toDate): array
    {
        $calendarService = new CalendarService(CalendarRepository::getInstance());
        $events = [];

        if ($calendarService->isAuthenticated())
        {
            $calendarIdentifiers = $this->getCalendarIdentifiers($calendarRendererProvider);

            foreach ($calendarIdentifiers as $calendarIdentifier)
            {
                $events = array_merge(
                    $events, $this->getCalendarEvents($calendarService, $calendarIdentifier, $fromDate, $toDate)
                );
            }
        }

        return $events;
    }
}