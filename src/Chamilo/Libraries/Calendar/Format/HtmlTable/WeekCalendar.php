<?php
namespace Chamilo\Libraries\Calendar\Format\HtmlTable;

/**
 *
 * @package Chamilo\Libraries\Calendar\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekCalendar extends Calendar
{
    const TIME_PLACEHOLDER = '__TIME__';

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday.
     *
     * @return integer
     */
    public function getStartTime()
    {
        if ($this->getCalendarConfiguration()->getFirstDayOfTheWeek() == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $this->getDisplayTime()));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday.
     *
     * @return integer
     */
    public function getEndTime()
    {
        if ($this->getCalendarConfiguration()->getFirstDayOfTheWeek() == 'sunday')
        {
            return strtotime('Next Saterday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Sunday', $this->getStartTime());
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
}
