<?php
namespace Chamilo\Libraries\Calendar\Service\Recurrence;

use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Architecture\Domain\RecurrenceRules;
use DateTime;
use Sabre\VObject;

/**
 * @package Chamilo\Libraries\Calendar\Service\Recurrence
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecurrenceCalculator
{
    private int $endTime;

    /**
     * @var \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    private array $events;

    private int $startTime;

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     */
    public function __construct(array $events, int $startTime, int $endTime)
    {
        $this->events = $events;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     * @throws \Sabre\VObject\InvalidDataException
     * @throws \Exception
     */
    public function expandEvents(): array
    {
        $vCalendar = new VObject\Component\VCalendar();

        $events = $this->getEvents();

        $expandedEvents = [];
        $recurringEvents = [];

        foreach ($events as $key => $event) {
            if (!$event->getRecurrenceRules()->hasRecurrence()) {
                $expandedEvents[] = $event;
            }
            else {
                $recurringEvents[$key] = $event;
            }
        }

        if (count($expandedEvents) == count($events)) {
            return $expandedEvents;
        }

        foreach ($recurringEvents as $key => $event) {
            $startDateTime = new DateTime('@' . $event->getStartDate());
            $endDateTime = new DateTime('@' . $event->getEndDate());

            /**
             * @var \Sabre\VObject\Component\VEvent $vEvent
             */
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

        $fromDateTime = new DateTime('@' . $this->getStartTime());
        $toDateTime = new DateTime('@' . $this->getEndTime());

        $vCalendar = $vCalendar->expand($fromDateTime, $toDateTime);
        $calculatedEvents = $vCalendar->select('VEVENT');

        foreach ($calculatedEvents as $calculatedEvent) {
            $repeatEvent = clone $recurringEvents[$calculatedEvent->EVENTID->getValue()];

            $repeatEvent->setRecurrenceRules(new RecurrenceRules());
            $repeatEvent->setStartDate($calculatedEvent->DTSTART->getDateTime()->getTimeStamp());
            $repeatEvent->setEndDate($calculatedEvent->DTEND->getDateTime()->getTimeStamp());

            if ($this->isVisible($repeatEvent, $this->getStartTime(), $this->getEndTime())) {
                $expandedEvents[] = $repeatEvent;
            }
        }

        return $expandedEvents;
    }

    public function getEndTime(): int
    {
        return $this->endTime;
    }

    public function setEndTime(int $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function setStartTime(int $startTime): void
    {
        $this->startTime = $startTime;
    }

    private function isVisible(Event $event, int $fromTime, int $endTime): bool
    {
        return ($event->getStartDate() >= $fromTime && $event->getStartDate() <= $endTime) ||
            ($event->getEndDate() >= $fromTime && $event->getEndDate() <= $endTime) ||
            ($event->getStartDate() < $fromTime && $event->getEndDate() > $endTime);
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     */
    public function setEvent(array $events): void
    {
        $this->events = $events;
    }
}