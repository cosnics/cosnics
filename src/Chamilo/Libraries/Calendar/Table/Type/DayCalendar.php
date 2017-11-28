<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendar extends Calendar
{

    /**
     * Gets the first date which will be displayed by this calendar.
     *
     * @return integer
     */
    public function getStartTime()
    {
        $calenderConfiguration = $this->getCalendarConfiguration();

        if ($calenderConfiguration->getHideNonWorkingHours())
        {
            return strtotime(
                date('Y-m-d ' . $calenderConfiguration->getWorkingHoursStart() . ':00:00', $this->getDisplayTime()));
        }

        return strtotime(date('Y-m-d 00:00:00', $this->getDisplayTime()));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     *
     * @return integer
     */
    public function getEndTime()
    {
        $calenderConfiguration = $this->getCalendarConfiguration();

        if ($calenderConfiguration->getHideNonWorkingHours())
        {
            return strtotime(
                date('Y-m-d ' . ($calenderConfiguration->getWorkingHoursEnd() - 1) . ':59:59', $this->getDisplayTime()));
        }

        return strtotime('+24 Hours', $this->getStartTime());
    }

    /**
     * Adds the events to the calendar
     */
    public function addEvents()
    {
        $calenderConfiguration = $this->getCalendarConfiguration();

        $events = $this->getEventsToShow();

        $start = 0;
        $end = 24;

        if ($calenderConfiguration->getHideNonWorkingHours())
        {
            $start = $calenderConfiguration->getWorkingHoursStart();
            $end = $calenderConfiguration->getWorkingHoursEnd();
        }

        foreach ($events as $time => $items)
        {
            if ($time >= $this->getEndTime())
            {
                continue;
            }

            $row = (date('H', $time) - $start) / $calenderConfiguration->getHourStep();

            foreach ($items as $index => $item)
            {
                try
                {
                    $cellContent = $this->getCellContents($row, 1);
                    $cellContent .= $item;
                    $this->setCellContents($row, 1, $cellContent);
                }
                catch (\Exception $exception)
                {
                }
            }
        }
    }
}
