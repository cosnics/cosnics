<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RecurrenceRulesParser
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent
     */
    private $calendarEvent;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent $calendarEvent
     */
    public function __construct(CalendarEvent $calendarEvent)
    {
        $this->calendarEvent = $calendarEvent;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent
     */
    public function getCalendarEvent()
    {
        return $this->calendarEvent;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent $calendarEvent
     */
    public function setCalendarEvent($calendarEvent)
    {
        $this->calendarEvent = $calendarEvent;
    }

    public function getRules()
    {
        $calendarEvent = $this->getCalendarEvent();
        
        $byDay = $calendarEvent->get_byday() ? explode(',', $calendarEvent->get_byday()) : array();
        $byMonthDay = $calendarEvent->get_bymonthday() ? explode(',', $calendarEvent->get_bymonthday()) : array();
        $byMonth = $calendarEvent->get_bymonth() ? explode(',', $calendarEvent->get_bymonth()) : array();
        
        return new RecurrenceRules(
            $calendarEvent->get_frequency(), 
            $calendarEvent->get_until(), 
            $calendarEvent->get_frequency_count(), 
            $calendarEvent->get_frequency_interval(), 
            $byDay, 
            $byMonthDay, 
            $byMonth);
    }
}