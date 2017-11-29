<?php
namespace Chamilo\Libraries\Calendar\Format\HtmlTable;

use Chamilo\Libraries\Calendar\CalendarConfiguration;

/**
 *
 * @package Chamilo\Libraries\Calendar\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendar extends Calendar
{

    /**
     * Keep mapping of dates and their corresponding table cells
     *
     * @var integer[]
     */
    private $cellMapping;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\CalendarConfiguration $calendarConfiguration
     * @param integer $displayTime
     * @param string[] $classes
     */
    public function __construct(CalendarConfiguration $calendarConfiguration, $displayTime)
    {
        parent::__construct($calendarConfiguration, $displayTime);

        $this->cellMapping = array();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar::getClasses()
     */
    protected function getClasses($classes = [])
    {
        return parent::getClasses(['table-calendar-month']);
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday. If the current month
     * doesn't start on a monday, the last monday of previous month is returned.
     *
     * @param string $firstDayOfTheWeek
     * @return integer
     */
    public function getStartTime()
    {
        $firstDay = mktime(0, 0, 0, date('m', $this->getDisplayTime()), 1, date('Y', $this->getDisplayTime()));

        if ($this->getCalendarConfiguration()->getFirstDayOfTheWeek() == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday. Of the current month doesn't
     * end on a sunday, the first sunday of next month is returned.
     *
     * @return integer
     */
    public function getEndTime()
    {
        $endTime = $this->getStartTime();

        while (date('Ym', $endTime) <= date('Ym', $this->getDisplayTime()))
        {
            $endTime = strtotime('+1 Week', $endTime);
        }

        return $endTime;
    }

    /**
     * Adds the events to the calendar
     */
    public function addEvents()
    {
        $events = $this->getEventsToShow();

        foreach ($events as $time => $items)
        {
            $cellMappingKey = date('Ymd', $time);
            $cellMapping = $this->getCellMapping();

            $row = $cellMapping[$cellMappingKey][0];
            $column = $cellMapping[$cellMappingKey][1];

            if (is_null($row) || is_null($column))
            {
                continue;
            }

            $this->handleItems($time, $items, $row, $column);
        }
    }

    /**
     *
     * @param integer $time
     * @param string[] $items
     * @param integer $row
     * @param integer $column
     */
    protected function handleItems($time, $items, $row, $column)
    {
        foreach ($items as $index => $item)
        {
            try
            {
                $cellContent = $this->getCellContents($row, $column);
                $cellContent .= $item;
                $this->setCellContents($row, $column, $cellContent);
            }
            catch (\Exception $exception)
            {
            }
        }
    }

    /**
     *
     * @return integer[]
     */
    public function getCellMapping()
    {
        return $this->cellMapping;
    }

    /**
     *
     * @param integer[] $cellMapping
     */
    public function setCellMapping($cellMapping)
    {
        $this->cellMapping = $cellMapping;
    }

    /**
     *
     * @param integer $key
     * @param integer[] $value
     */
    public function setCellMappingForKey($key, $value)
    {
        $this->cellMapping[$key] = $value;
    }
}
