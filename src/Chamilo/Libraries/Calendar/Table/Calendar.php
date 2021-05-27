<?php
namespace Chamilo\Libraries\Calendar\Table;

use HTML_Table;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Calendar extends HTML_Table
{
    const TIME_PLACEHOLDER = '__TIME__';

    /**
     *
     * @var integer
     */
    private $displayTime;

    /**
     *
     * @var string[]
     */
    private $eventsToShow;

    /**
     *
     * @param integer $displayTime
     * @param string[] $classes
     */
    public function __construct($displayTime, $classes = [])
    {
        if (is_null($displayTime))
        {
            $this->displayTime = time();
        }
        else
        {
            $this->displayTime = $displayTime;
        }
        $this->eventsToShow = [];

        array_unshift($classes, 'table-calendar');

        parent::__construct(array('class' => implode(' ', $classes), 'cellspacing' => 0));
    }

    /**
     * Add an event to the calendar
     *
     * @param integer $time A time in the day on which the event should be displayed
     * @param string $content The html content to insert in the month calendar
     */
    public function addEvent($time, $content)
    {
        $this->eventsToShow[$time][] = $content;
    }

    /**
     *
     * @param integer $time
     *
     * @return boolean
     */
    public function containsEventsForTime($time)
    {
        return count($this->eventsToShow[$time]) > 0;
    }

    /**
     *
     * @return integer
     */
    public function getDisplayTime()
    {
        return $this->displayTime;
    }

    /**
     *
     * @param integer $displayTime
     */
    public function setDisplayTime($displayTime)
    {
        $this->displayTime = $displayTime;
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     *
     * @return integer
     */
    abstract public function getEndTime();

    /**
     * Gets the list of events to show sorted by their starting time
     *
     * @return string[][]
     */
    public function getEventsToShow()
    {
        ksort($this->eventsToShow);

        return $this->eventsToShow;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     *
     * @return integer
     */
    abstract public function getStartTime();
}
