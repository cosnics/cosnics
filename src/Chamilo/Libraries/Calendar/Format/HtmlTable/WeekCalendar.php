<?php
namespace Chamilo\Libraries\Calendar\Format\HtmlTable;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekCalendar extends Calendar
{
    const TIME_PLACEHOLDER = '__TIME__';

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar::getClasses()
     */
    protected function getClasses($classes = [])
    {
        return parent::getClasses(['table-calendar-week']);
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday.
     *
     * @return integer
     */
    public function getStartTime()
    {
        $startTime = $this->getStrictStartTime();
        $calenderConfiguration = $this->getCalendarConfiguration();

        if ($calenderConfiguration->getHideNonWorkingHours())
        {
            return strtotime(date('Y-m-d ' . $calenderConfiguration->getWorkingHoursStart() . ':00:00', $startTime));
        }

        return $startTime;
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday.
     *
     * @return integer
     */
    public function getEndTime()
    {
        $endTime = $this->getStrictEndTime();
        $calenderConfiguration = $this->getCalendarConfiguration();

        if ($calenderConfiguration->getHideNonWorkingHours())
        {
            return strtotime(date('Y-m-d ' . ($calenderConfiguration->getWorkingHoursEnd() - 1) . ':59:59', $endTime));
        }

        return $endTime;
    }

    /**
     * Adds the events to the calendar
     */
    public function addEvents()
    {
        $calendarConfiguration = $this->getCalendarConfiguration();
        $events = $this->getEventsToShow();

        $workingStart = $calendarConfiguration->getWorkingHoursStart();
        $workingEnd = $calendarConfiguration->getWorkingHoursEnd();
        $hide = $calendarConfiguration->getHideNonWorkingHours();

        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        foreach ($events as $time => $items)
        {
            $row = date('H', $time) - $start;

            if ($row > $end - $start - 1)
            {
                continue;
            }

            $column = date('w', $time);

            if ($column == 0)
            {
                $column = 7;
            }

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
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar::getStrictStartTime()
     */
    public function getStrictStartTime()
    {
        if ($this->getCalendarConfiguration()->getFirstDayOfTheWeek() == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $this->getDisplayTime()));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar::getStrictEndTime()
     */
    public function getStrictEndTime()
    {
        if ($this->getCalendarConfiguration()->getFirstDayOfTheWeek() == 'sunday')
        {
            return strtotime('Next Saterday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Sunday', $this->getStartTime());
    }
}
