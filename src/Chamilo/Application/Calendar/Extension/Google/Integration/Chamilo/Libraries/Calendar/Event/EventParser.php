<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Application\Calendar\Extension\Google\CalendarProperties;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRulesIcalParser;
use Chamilo\Libraries\Translation\Translation;
use DateTime;
use DateTimeZone;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\CalendarProperties
     */
    private $calendarProperties;

    /**
     *
     * @var int
     */
    private $fromDate;

    /**
     *
     * @var \Google_Service_Calendar_Event
     */
    private $googleCalendarEvent;

    /**
     *
     * @var int
     */
    private $toDate;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Application\Calendar\Extension\Google\CalendarProperties $calendarProperties
     * @param \Google_Service_Calendar_Event $googleCalendarEvent
     * @param int $fromDate
     * @param int $toDate
     */
    public function __construct(
        CalendarProperties $calendarProperties, Google_Service_Calendar_Event $googleCalendarEvent, $fromDate, $toDate
    )
    {
        $this->calendarProperties = $calendarProperties;
        $this->googleCalendarEvent = $googleCalendarEvent;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     *
     * @param \Google_Service_Calendar_EventDateTime $eventDateTime
     *
     * @return string
     */
    private function determineTime(Google_Service_Calendar_EventDateTime $eventDateTime)
    {
        if ($eventDateTime->getDateTime())
        {
            return $eventDateTime->getDateTime();
        }
        else
        {
            return $eventDateTime->getDate();
        }
    }

    /**
     *
     * @param string $eventTimeZone
     * @param string $calendarTimeZone
     *
     * @return \DateTimeZone|NULL
     */
    private function determineTimeZone($eventTimeZone, $calendarTimeZone)
    {
        if ($eventTimeZone || $calendarTimeZone)
        {
            return new DateTimeZone($eventTimeZone ?: $calendarTimeZone);
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\CalendarProperties
     */
    public function getCalendarProperties()
    {
        return $this->calendarProperties;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\CalendarProperties $calendarProperties
     */
    public function setCalendarProperties(CalendarProperties $calendarProperties)
    {
        $this->calendarProperties = $calendarProperties;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents()
    {
        $googleCalendarEvent = $this->getGoogleCalendarEvent();

        if (is_null($googleCalendarEvent) || is_null($googleCalendarEvent->getStart()) ||
            is_null($googleCalendarEvent->getEnd()))
        {
            return [];
        }

        $url = null;

        $event = new Event(
            $googleCalendarEvent->getId(),
            $this->getTimestamp($googleCalendarEvent->getStart(), $this->getCalendarProperties()->getTimeZone()),
            $this->getTimestamp($googleCalendarEvent->getEnd(), $this->getCalendarProperties()->getTimeZone()),
            $this->getRecurrence($googleCalendarEvent->getRecurrence()), $url, $googleCalendarEvent->getSummary(),
            $googleCalendarEvent->getDescription(), $googleCalendarEvent->getLocation(),
            $this->getSource($this->getCalendarProperties()), Manager::context()
        );

        $event->setCalendarProperties($this->getCalendarProperties());
        $event->setGoogleCalendarEvent($googleCalendarEvent);

        return array($event);
    }

    /**
     *
     * @return int
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     *
     * @param int $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     *
     * @return \Google_Service_Calendar_Event
     */
    public function getGoogleCalendarEvent()
    {
        return $this->googleCalendarEvent;
    }

    /**
     *
     * @param \Google_Service_Calendar_Event $googleCalendarEvent
     */
    public function setGoogleCalendarEvent(Google_Service_Calendar_Event $googleCalendarEvent)
    {
        $this->googleCalendarEvent = $googleCalendarEvent;
    }

    /**
     *
     * @param string[] $recurrenceRules
     *
     * @return \Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules
     */
    private function getRecurrence($recurrenceRules)
    {
        $recurrenceRulesIcalParser = new RecurrenceRulesIcalParser();

        return $recurrenceRulesIcalParser->getRules($recurrenceRules[0]);
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\CalendarProperties $calendarProperties
     *
     * @return string
     */
    private function getSource(CalendarProperties $calendarProperties)
    {
        return Translation::get(
            'SourceName', array('CALENDAR' => $calendarProperties->getSummary()), Manager::context()
        );
    }

    /**
     *
     * @param \Google_Service_Calendar_EventDateTime $eventDateTime
     * @param string $calendarTimeZone
     */
    private function getTimestamp(Google_Service_Calendar_EventDateTime $eventDateTime, $calendarTimeZone)
    {
        $dateTime = new DateTime(
            $this->determineTime($eventDateTime),
            $this->determineTimeZone($eventDateTime->getTimeZone(), $calendarTimeZone)
        );

        return $dateTime->getTimestamp();
    }

    /**
     *
     * @return int
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     *
     * @param int $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }
}
