<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
// use Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService;
use Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository;
use Chamilo\Libraries\Calendar\Event\Event;

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
        $googleCalendarEvents = $googleCalendarService->getEventsForCalendarIdentifierAndBetweenDates(
            'roderidder@gmail.com',
            1435536000,
            1438560000);

        $events = array();

        foreach ($googleCalendarEvents as $googleCalendarEvent)
        {

            $startDate = new \DateTime(
                $googleCalendarEvent->getStart()->getDateTime() ?  : $googleCalendarEvent->getStart()->getDate(),
                $googleCalendarEvent->getStart()->getTimeZone() ? new \DateTimeZone(
                    $googleCalendarEvent->getStart()->getTimeZone()) : null);
            $endDate = new \DateTime(
                $googleCalendarEvent->getEnd()->getDateTime() ?  : $googleCalendarEvent->getEnd()->getDate(),
                $googleCalendarEvent->getEnd()->getTimeZone() ? new \DateTimeZone(
                    $googleCalendarEvent->getEnd()->getTimeZone()) : null);

            $events[] = new Event(
                $googleCalendarEvent->getICalUID(),
                $startDate->getTimestamp(),
                $endDate->getTimestamp(),
                null,
                null,
                $googleCalendarEvent->getSummary(),
                null,
                'Google Calendar',
                __NAMESPACE__);
        }

        return $events;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication[] $publications
     * @param integer $fromDate
     * @param integer $toDate
     */
    private function renderEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $publications, $fromDate,
        $toDate)
    {
        $events = array();

        while ($publication = $publications->next_result())
        {
            $eventParser = new EventParser($renderer, $publication, $fromDate, $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }
}