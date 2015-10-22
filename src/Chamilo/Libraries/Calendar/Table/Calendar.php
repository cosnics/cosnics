<?php
namespace Chamilo\Libraries\Calendar\Table;

use HTML_Table;

/**
 * A tabular representation of a calendar
 *
 * @package libraries\calendar\table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
     */
    public function __construct($displayTime)
    {
        if (is_null($displayTime))
        {
            $this->displayTime = time();
        }
        else
        {
            $this->displayTime = $displayTime;
        }
        $this->eventsToShow = array();

        parent :: HTML_Table(array('class' => 'calendar_table calendar-container', 'cellspacing' => 0));
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
     * Add an event to the calendar
     *
     * @param integer $time A time in the day on which the event should be displayed
     * @param string $content The html content to insert in the month calendar
     */
    public function addEvent($time, $content)
    {
        $this->eventsToShow[$time][] = $content;
        sort($this->eventsToShow[$time]);
    }

    /**
     * Gets the list of events to show sorted by their starting time
     *
     * @return array
     */
    public function getEventsToShow()
    {
        ksort($this->eventsToShow);
        return $this->eventsToShow;
    }

    public function containsEventsForTime($time)
    {
        return count($this->eventsToShow[$time]) > 0;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     *
     * @return integer
     */
    abstract public function getStartTime();

    /**
     * Gets the end date which will be displayed by this calendar.
     *
     * @return integer
     */
    abstract public function getEndTime();
}
