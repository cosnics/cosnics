<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\ExternalCalendar;
use Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarEventDataProvider extends ExternalCalendar
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate)
    {
        $calendarService = new CalendarService(CalendarRepository::getInstance());
        $events = array();

        if ($calendarService->isAuthenticated())
        {
            $calendarIdentifiers = $this->getCalendarIdentifiers($calendarRendererProvider);

            foreach ($calendarIdentifiers as $calendarIdentifier)
            {
                $events = array_merge(
                    $events,
                    $this->getCalendarEvents($calendarService, $calendarIdentifier, $fromDate, $toDate));
            }
        }

        return $events;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    protected function getAvailabilityService()
    {
        return $this->getService('chamilo.application.calendar.service.availability_service');
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @return string[]
     */
    private function getCalendarIdentifiers(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider)
    {
        $package = ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 5);

        $availabilities = $this->getAvailabilityService()->getAvailabilitiesForUserAndCalendarType(
            $calendarRendererProvider->getDataUser(),
            $package);

        $calendarIdentifiers = array();

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
     *
     * @param CalendarService $calendarService
     * @param string $calendarId
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    private function getCalendarEvents(CalendarService $calendarService, $calendarId, $fromDate, $toDate)
    {
        $eventResultSet = $calendarService->getEventsForCalendarIdentifierAndBetweenDates(
            $calendarId,
            $fromDate,
            $toDate);

        $events = array();

        while ($googleCalenderEvent = $eventResultSet->next_result())
        {
            $eventParser = new EventParser(
                $eventResultSet->getCalendarProperties(),
                $googleCalenderEvent,
                $fromDate,
                $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\Architecture\CalendarInterface::getCalendars()
     */
    public function getCalendars(User $user)
    {
        $calendarService = new CalendarService(CalendarRepository::getInstance());
        $calendars = array();

        if ($calendarService->isAuthenticated())
        {
            $calendars = $calendarService->getOwnedCalendars();
        }

        return $calendars;
    }
}