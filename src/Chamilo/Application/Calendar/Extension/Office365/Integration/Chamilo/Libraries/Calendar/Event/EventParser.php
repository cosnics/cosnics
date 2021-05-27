<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Application\Calendar\Extension\Office365\Manager;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules;
use Chamilo\Libraries\Translation\Translation;
use DateTime;
use DateTimeZone;
use Exception;
use Microsoft\Graph\Model\PatternedRecurrence;

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
     * @var \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar
     */
    private $availableCalendar;

    /**
     *
     * @var \Microsoft\Graph\Model\Event
     */
    private $office365CalendarEvent;

    /**
     *
     * @var integer
     */
    private $fromDate;

    /**
     *
     * @var integer
     */
    private $toDate;

    /**
     *
     * @param \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar $availableCalendar
     * @param \Microsoft\Graph\Model\Event $office365CalendarEvent
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function __construct(
        AvailableCalendar $availableCalendar, \Microsoft\Graph\Model\Event $office365CalendarEvent, $fromDate, $toDate
    )
    {
        $this->availableCalendar = $availableCalendar;
        $this->office365CalendarEvent = $office365CalendarEvent;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     *
     * @param string $eventTimeZone
     * @param boolean $isAllDay
     *
     * @return \DateTimeZone|NULL
     */
    private function determineTimeZone($eventTimeZone)
    {
        if ($eventTimeZone)
        {
            try
            {
                return new DateTimeZone($eventTimeZone);
            }
            catch (Exception $exception)
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar
     */
    public function getAvailableCalendar()
    {
        return $this->availableCalendar;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar $availableCalendar
     */
    public function setAvailableCalendar(
        AvailableCalendar $availableCalendar
    )
    {
        $this->availableCalendar = $availableCalendar;
    }

    /**
     *
     * @param string[] $daysOfWeek
     *
     * @return string[]
     */
    private function getByDay($patternType, $patternIndex, $patternDaysOfWeek)
    {
        $byDay = [];

        $prefix = $this->getNumericIndex($patternType, $patternIndex);

        foreach ($patternDaysOfWeek as $dayOfWeek)
        {
            $byDay[] = $prefix . substr(strtoupper($dayOfWeek), 0, 2);
        }

        return $byDay;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents()
    {
        $office365CalendarEvent = $this->getOffice365CalendarEvent();

        $url = null;

        $event = new Event(
            $office365CalendarEvent->getId(), $this->getTimestamp(
            $office365CalendarEvent->getStart()->getDateTime(), $office365CalendarEvent->getStart()->getTimeZone(),
            $office365CalendarEvent->getIsAllDay()
        ), $this->getTimestamp(
            $office365CalendarEvent->getEnd()->getDateTime(), $office365CalendarEvent->getEnd()->getTimeZone(),
            $office365CalendarEvent->getIsAllDay()
        ), $this->getRecurrence($office365CalendarEvent->getRecurrence()), $url, $office365CalendarEvent->getSubject(),
            $office365CalendarEvent->getBody()->getContent(), $office365CalendarEvent->getLocation()->getDisplayName(),
            $this->getSource($this->getAvailableCalendar()->getName()), Manager::context()
        );

        $event->setOffice365CalendarEvent($office365CalendarEvent);

        return array($event);
    }

    /**
     *
     * @param string $frequencyType
     *
     * @return integer
     */
    private function getFrequency($frequencyType)
    {
        switch ($frequencyType)
        {
            case 'Daily' :
                return RecurrenceRules::FREQUENCY_DAILY;
                break;
            case 'Weekly' :
                return RecurrenceRules::FREQUENCY_WEEKLY;
                break;
            case 'AbsoluteMonthly' :
            case 'RelativeMonthly' :
                return RecurrenceRules::FREQUENCY_MONTHLY;
                break;
            case 'AbsoluteYearly' :
            case 'RelativeYearly' :
                return RecurrenceRules::FREQUENCY_YEARLY;
                break;
            default :
                return RecurrenceRules::FREQUENCY_NONE;
        }
    }

    /**
     *
     * @return integer
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     *
     * @param integer $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     *
     * @param string $patternType
     * @param $patternIndex
     *
     * @return string
     */
    private function getNumericIndex($patternType, $patternIndex)
    {
        if (!in_array($patternType, array('RelativeMonthly', 'RelativeYearly')))
        {
            return '';
        }

        switch ($patternIndex)
        {
            case 'First' :
                return '1';
                break;
            case 'Second' :
                return '2';
                break;
            case 'Third' :
                return '3';
                break;
            case 'Fourth' :
                return '4';
                break;
            case 'Last' :
                return '-1';
                break;
            default :
                return '';
        }
    }

    /**
     *
     * @return \Microsoft\Graph\Model\Event
     */
    public function getOffice365CalendarEvent()
    {
        return $this->office365CalendarEvent;
    }

    /**
     *
     * @param \Microsoft\Graph\Model\Event $office365CalendarEvent
     */
    public function setOffice365CalendarEvent(\Microsoft\Graph\Model\Event $office365CalendarEvent)
    {
        $this->office365CalendarEvent = $office365CalendarEvent;
    }

    /**
     *
     * @param \Microsoft\Graph\Model\PatternedRecurrence $recurrence
     *
     * @return \Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules
     */
    private function getRecurrence(PatternedRecurrence $recurrence = null)
    {
        $recurrenceRules = new RecurrenceRules();

        if ($recurrence instanceof PatternedRecurrence)
        {
            // $recurrenceRules->setFrequency($this->getFrequency($recurrence->getPattern()->getType()));

            // if ($recurrence->getPattern()->getInterval() > 0)
            // {
            // $recurrenceRules->setInterval((string) $recurrence->getPattern()->getInterval());
            // }

            // if ($recurrence->getRange()->getType() == 'Numbered')
            // {
            // $recurrenceRules->setCount((string) $recurrence->getRange()->getNumberOfOccurrences());
            // }

            // if ($recurrence->getRange()->getType() == 'EndDate')
            // {
            // $recurrenceRules->setUntil($this->getTimestamp($recurrence->getRange()->getEndDate()));
            // }

            // if ($recurrence->getPattern()->getDayOfMonth() != 0)
            // {
            // $recurrenceRules->setByMonthDay(array($recurrence->getPattern()->getDayOfMonth()));
            // }

            // if ($recurrence->getPattern()->getMonth() != 0)
            // {
            // $recurrenceRules->setByMonth(array($recurrence->getPattern()->getMonth()));
            // }

            // if (count($recurrence->getPattern()->getDaysOfWeek()) > 0)
            // {
            // $recurrenceRules->setByDay(
            // $this->getByDay(
            // $recurrence->getPattern()->getType(),
            // $recurrence->getPattern()->getIndex(),
            // $recurrence->getPattern()->getDaysOfWeek()));
            // }
        }

        return $recurrenceRules;
    }

    /**
     *
     * @param string $calendarName
     *
     * @return string
     */
    private function getSource($calendarName)
    {
        return Translation::get(
            'SourceName', array('CALENDAR' => $calendarName), Manager::context()
        );
    }

    /**
     *
     * @param string $eventDateTime
     * @param string $eventTimeZone
     */
    private function getTimestamp($eventDateTime, $eventTimeZone, $isAllDay)
    {
        $dateTime = new DateTime($eventDateTime, $this->determineTimeZone($eventTimeZone, $isAllDay));

        if ($isAllDay)
        {
            return mktime(0, 0, 0, $dateTime->format('n'), $dateTime->format('j'), $dateTime->format('Y'));
        }

        return $dateTime->getTimestamp();
    }

    /**
     *
     * @return integer
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     *
     * @param integer $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }
}
