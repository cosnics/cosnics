<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Architecture\Domain\CalendarProperties;
use Chamilo\Application\Calendar\Extension\Google\Architecture\Domain\Event;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use DateTime;
use DateTimeZone;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    private function determineTime(Google_Service_Calendar_EventDateTime $eventDateTime): string
    {
        if ($eventDateTime->getDateTime()) {
            return $eventDateTime->getDateTime();
        }
        else {
            return $eventDateTime->getDate();
        }
    }

    /**
     * @throws \DateInvalidTimeZoneException
     */
    private function determineTimeZone(string $eventTimeZone, string $calendarTimeZone): ?DateTimeZone
    {
        if ($eventTimeZone || $calendarTimeZone) {
            return new DateTimeZone($eventTimeZone ?: $calendarTimeZone);
        }
        else {
            return null;
        }
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Google\Architecture\Domain\Event[]
     * @throws \DateInvalidTimeZoneException
     */
    public function getEvents(CalendarProperties $calendarProperties, Google_Service_Calendar_Event $googleCalendarEvent
    ): array
    {
        if (is_null($googleCalendarEvent->getStart()) || is_null($googleCalendarEvent->getEnd())) {
            return [];
        }

        $url = null;

        $event = new Event(
            $googleCalendarEvent->getId(),
            $this->getTimestamp($googleCalendarEvent->getStart(), $calendarProperties->getTimeZone()),
            $this->getTimestamp($googleCalendarEvent->getEnd(), $calendarProperties->getTimeZone()), $url,
            $googleCalendarEvent->getSummary(), $googleCalendarEvent->getDescription(),
            $googleCalendarEvent->getLocation(), $this->getSource($calendarProperties), Manager::CONTEXT
        );

        $event->setCalendarProperties($calendarProperties);
        $event->setGoogleCalendarEvent($googleCalendarEvent);

        return [$event];
    }

    private function getSource(CalendarProperties $calendarProperties): string
    {
        return $this->getTranslator()->trans(
            'SourceName', ['%Calendar%' => $calendarProperties->getSummary()], Manager::CONTEXT
        );
    }

    /**
     * @throws \DateInvalidTimeZoneException
     * @throws \Exception
     */
    private function getTimestamp(Google_Service_Calendar_EventDateTime $eventDateTime, string $calendarTimeZone): int
    {
        $dateTime = new DateTime(
            $this->determineTime($eventDateTime),
            $this->determineTimeZone($eventDateTime->getTimeZone(), $calendarTimeZone)
        );

        return $dateTime->getTimestamp();
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
