<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Calendar\Event\RecurrenceRules;
use Chamilo\Libraries\Platform\Translation;

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
     * @var \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    private $renderer;

    /**
     *
     * @var string[]
     */
    private $weekLabels;

    /**
     *
     * @var \stdClass
     */
    private $calendarEvent;

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
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param string[] $weekLabels
     * @param string[] $calendarEvent
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function __construct(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $weekLabels, $calendarEvent,
        $fromDate, $toDate)
    {
        $this->renderer = $renderer;
        $this->weekLabels = $weekLabels;
        $this->calendarEvent = $calendarEvent;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     *
     * @return string[]
     */
    public function getWeekLabels()
    {
        return $this->weekLabels;
    }

    /**
     *
     * @param string[] $weekLabels
     */
    public function setWeekLabels($weekLabels)
    {
        $this->weekLabels = $weekLabels;
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
        \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar $availableCalendar)
    {
        $this->availableCalendar = $availableCalendar;
    }

    /**
     *
     * @return \stdClass
     */
    public function getCalendarEvent()
    {
        return $this->calendarEvent;
    }

    /**
     *
     * @param \stdClass $calendarEvent
     */
    public function setCalendarEvent($calendarEvent)
    {
        $this->calendarEvent = $calendarEvent;
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

    /**
     *
     * @return \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents()
    {
        $calendarEvent = $this->getCalendarEvent();
        $url = null;
        $events = array();

        if ($calendarEvent['occurences'] > 1)
        {
            $pattern = str_split($calendarEvent['pattern']);
            $weekLabels = $this->getWeekLabels();
            $enabledWeeks = array();

            foreach ($pattern as $weekNumber => $isEnabled)
            {
                if ($isEnabled)
                {
                    $startTime = strtotime($calendarEvent['start_time']);
                    $baseDate = strtotime($weekLabels[$weekNumber]);
                    $baseDate += ($calendarEvent['day'] * 24 * 60 * 60);

                    $baseDay = date('j', $baseDate);
                    $baseMonth = date('n', $baseDate);
                    $baseYear = date('Y', $baseDate);

                    $hour = date('G', $startTime);
                    $minute = date('i', $startTime);
                    $second = date('s', $startTime);

                    $startDate = mktime($hour, $minute, $second, $baseMonth, $baseDay, $baseYear);
                    $endDate = $startDate + ($calendarEvent['duration'] * 60);

                    $event = new Event(
                        $calendarEvent['id'],
                        $startDate,
                        $endDate,
                        new RecurrenceRules(),
                        $url,
                        $calendarEvent['name'],
                        $calendarEvent['name'],
                        Translation :: get(
                            'TypeName',
                            null,
                            \Chamilo\Application\Calendar\Extension\SyllabusPlus\Manager :: context()),
                        \Chamilo\Application\Calendar\Extension\SyllabusPlus\Manager :: context());

                    $event->setCalendarEvent($calendarEvent);
                    $events[] = $event;
                }
            }

//             var_dump(count($events));
        }
        else
        {
            $event = new Event(
                $calendarEvent['id'],
                $calendarEvent['start_timestamp'],
                $calendarEvent['end_timestamp'],
                new RecurrenceRules(),
                $url,
                $calendarEvent['name'],
                $calendarEvent['name'],
                Translation :: get(
                    'TypeName',
                    null,
                    \Chamilo\Application\Calendar\Extension\SyllabusPlus\Manager :: context()),
                \Chamilo\Application\Calendar\Extension\SyllabusPlus\Manager :: context());

            $event->setCalendarEvent($calendarEvent);
            $events[] = $event;
        }

        return $events;
    }

    private function getRecurrence(array $calendarEvent)
    {
        $recurrenceRules = new RecurrenceRules();

        var_dump(strtotime('2016W01'));
        exit();

        if ($calendarEvent['occurences'] > 1)
        {
            var_dump($calendarEvent);

            $recurrenceRules->setFrequency(RecurrenceRules :: FREQUENCY_YEARLY);
            $recurrenceRules->setCount($calendarEvent['occurences']);

            $pattern = str_split($calendarEvent['pattern']);
            $weekLabels = $this->getWeekLabels();
            $enabledWeeks = array();

            foreach ($pattern as $weekNumber => $isEnabled)
            {
                if ($isEnabled)
                {
                    $enabledWeeks[] = $weekLabels[$weekNumber];
                }
            }

            sort($enabledWeeks);

            $recurrenceRules->setByWeekNumber($enabledWeeks);

            var_dump($recurrenceRules);
        }

        return $recurrenceRules;
    }
}
