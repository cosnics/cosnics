<?php
namespace Chamilo\Libraries\Calendar\Event\RecurrenceRules;

use Chamilo\Libraries\Calendar\Event\Event;
use DateTime;
use Sabre\VObject;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\RecurrenceRules
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecurrenceCalculator
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\Event[]
     */
    private $events;

    /**
     *
     * @var integer
     */
    private $startTime;

    /**
     *
     * @var integer
     */
    private $endTime;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event[] $events
     * @param integer $startTime
     * @param integer $endTime
     */
    public function __construct(array $events, $startTime, $endTime)
    {
        $this->events = $events;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Sabre\VObject\InvalidDataException
     */
    public function expandEvents()
    {
        $vCalendar = new VObject\Component\VCalendar();

        $events = $this->getEvents();

        $expandedEvents = array();
        $recurringEvents = array();

        foreach ($events as $key => $event)
        {
            if (!$event->getRecurrenceRules()->hasRecurrence())
            {
                $expandedEvents[] = $event;
            }
            else
            {
                $recurringEvents[$key] = $event;
            }
        }

        if (count($expandedEvents) == count($events))
        {
            return $expandedEvents;
        }

        foreach ($recurringEvents as $key => $event)
        {
            $startDateTime = new DateTime();
            $startDateTime->setTimestamp($event->getStartDate());

            $endDateTime = new DateTime();
            $endDateTime->setTimestamp($event->getEndDate());

            $vEvent = $vCalendar->add('VEVENT');

            $vEvent->add('SUMMARY', $event->getTitle());
            $vEvent->add('DTSTART', $startDateTime);
            $vEvent->add('DTEND', $endDateTime);

            $vObjectRecurrenceRules = new VObjectRecurrenceRulesFormatter();
            $recurrenceRules = $event->getRecurrenceRules();

            $vEvent->add('RRULE', $vObjectRecurrenceRules->format($recurrenceRules));
            $vEvent->add('EVENTID', $key);
            $vEvent->add('UID', uniqid());
        }

        $fromDateTime = new DateTime();
        $fromDateTime->setTimestamp($this->getStartTime());

        $toDateTime = new DateTime();
        $toDateTime->setTimestamp($this->getEndTime());

        $vCalendar = $vCalendar->expand($fromDateTime, $toDateTime);
        $calculatedEvents = $vCalendar->VEVENT;

        foreach ($calculatedEvents as $calculatedEvent)
        {
            $repeatEvent = clone $recurringEvents[$calculatedEvent->EVENTID->getValue()];

            $repeatEvent->setRecurrenceRules(new RecurrenceRules());
            $repeatEvent->setStartDate($calculatedEvent->DTSTART->getDateTime()->getTimeStamp());
            $repeatEvent->setEndDate($calculatedEvent->DTEND->getDateTime()->getTimeStamp());

            if ($this->isVisible($repeatEvent, $this->getStartTime(), $this->getEndTime()))
            {
                $expandedEvents[] = $repeatEvent;
            }
        }

        return $expandedEvents;
    }

    /**
     *
     * @return integer
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     *
     * @param integer $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     *
     * @return integer
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     *
     * @param integer $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $fromTime
     * @param integer $endTime
     *
     * @return boolean
     */
    private function isVisible(Event $event, $fromTime, $endTime)
    {
        return ($event->getStartDate() >= $fromTime && $event->getStartDate() <= $endTime) ||
            ($event->getEndDate() >= $fromTime && $event->getEndDate() <= $endTime) ||
            ($event->getStartDate() < $fromTime && $event->getEndDate() > $endTime);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event[] $events
     */
    public function setEvent($events)
    {
        $this->events = $events;
    }
}