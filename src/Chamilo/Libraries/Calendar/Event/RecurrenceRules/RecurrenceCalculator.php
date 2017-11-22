<?php
namespace Chamilo\Libraries\Calendar\Event\RecurrenceRules;

use Chamilo\Libraries\Calendar\Event\Event;
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
     * @var \Chamilo\Libraries\Calendar\Event\Event
     */
    private $event;

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
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startTime
     * @param integer $endTime
     */
    public function __construct(Event $event, $startTime, $endTime)
    {
        $this->event = $event;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
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
        $event = $this->getEvent();
        $recurrenceRules = $event->getRecurrenceRules();

        if ($recurrenceRules->hasRecurrence())
        {
            $vCalendar = new VObject\Component\VCalendar();

            $startDateTime = new \DateTime();
            $startDateTime->setTimestamp($event->getStartDate());

            $endDateTime = new \DateTime();
            $endDateTime->setTimestamp($event->getEndDate());

            $vEvent = $vCalendar->add('VEVENT');

            $vEvent->add('SUMMARY', $event->getTitle());
            $vEvent->add('DESCRIPTION', $event->getContent());
            $vEvent->add('DTSTART', $startDateTime);
            $vEvent->add('DTEND', $endDateTime);

            $vObjectRecurrenceRules = new VObjectRecurrenceRulesFormatter();

            $vEvent->add('RRULE', $vObjectRecurrenceRules->format(($event->getRecurrenceRules())));
            $vEvent->add('UID', uniqid());

            $fromDateTime = new \DateTime();
            $fromDateTime->setTimestamp($this->getStartTime());

            $toDateTime = new \DateTime();
            $toDateTime->setTimestamp($this->getEndTime());

            $vCalendar->expand($fromDateTime, $toDateTime);
            $calculatedEvents = $vCalendar->VEVENT;

            $events = array();

            foreach ($calculatedEvents as $calculatedEvent)
            {
                $repeatEvent = clone $event;

                $repeatEvent->setRecurrenceRules(new RecurrenceRules());
                $repeatEvent->setStartDate($calculatedEvent->DTSTART->getDateTime()->getTimeStamp());
                $repeatEvent->setEndDate($calculatedEvent->DTEND->getDateTime()->getTimeStamp());

                $events[] = $repeatEvent;
            }
        }
        else
        {
            if ($this->isVisible($event, $this->getStartTime(), $this->getEndTime()))
            {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $fromTime
     * @param integer $endTime
     * @return boolean
     */
    private function isVisible(Event $event, $fromTime, $endTime)
    {
        return ($event->getStartDate() >= $fromTime && $event->getStartDate() <= $endTime) ||
             ($event->getEndDate() >= $fromTime && $event->getEndDate() <= $endTime) ||
             ($event->getStartDate() < $fromTime && $event->getEndDate() > $endTime);
    }
}