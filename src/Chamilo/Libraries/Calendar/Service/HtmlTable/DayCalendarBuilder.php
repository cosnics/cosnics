<?php
namespace Chamilo\Libraries\Calendar\Service\HtmlTable;

use Chamilo\Libraries\Calendar\HtmlTable\DayCalendar;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendarBuilder extends CalendarBuilder
{

    /**
     *
     * @param integer $displayTime
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\HtmlTable\DayCalendar
     */
    protected function getCalendar($displayTime, $classes = [])
    {
        return new DayCalendar($this->getCalendarConfiguration(), $displayTime, $classes);
    }

    /**
     *
     * @param integer $displayTime
     * @param string[] $displayParameters
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\HtmlTable\DayCalendar
     */
    public function buildCalendar($displayTime, $displayParameters = [], $classes = [])
    {
        $dayCalendar = $this->getCalendar($displayTime, $classes);

        $yearDay = date('z', $displayTime) + 1;
        $yearWeek = date('W', $displayTime);

        $header = $dayCalendar->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->updateCellAttributes(0, 0, 'class="table-calendar-day-hours"');

        $header->setHeaderContents(
            0,
            1,
            $this->getTranslator()->trans(date('l', $displayTime) . 'Short', [], Utilities::COMMON_LIBRARIES) . ' ' .
                 date('d/m', $displayTime));

        $startHour = 0;
        $endHour = 24;

        if ($this->getHideNonWorkingHours())
        {
            $startHour = $this->getWorkingHoursStart();
            $endHour = $this->getWorkingHoursEnd();
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $startHour;
            $cellContent = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $dayCalendar->setCellContents($rowId, 0, $cellContent);

            $classes = array();

            $classes[] = 'table-calendar-day-hours';

            if ($hour % 2 == 0)
            {
                $classes[] = 'table-calendar-alternate';
            }

            $dayCalendar->updateCellAttributes($rowId, 0, 'class="' . implode(' ', $classes) . '"');
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $startHour;

            $tableStartDate = mktime(
                $hour,
                0,
                0,
                date('m', $displayTime),
                date('d', $displayTime),
                date('Y', $displayTime));

            $tableEndDate = strtotime('+' . $this->getHourStep() . ' hours', $tableStartDate);
            $dayCalendar->setCellContents($rowId, 1, '');

            $classes = $this->determineCellClasses($hour, $displayTime);

            if (count($classes) > 0)
            {
                $dayCalendar->updateCellAttributes($rowId, 1, 'class="' . implode(' ', $classes) . '"');
            }
        }

        return $dayCalendar;
    }

    /**
     *
     * @param integer $hour
     * @param integer $displayTime
     * @return string[]
     */
    protected function determineCellClasses($hour, $displayTime)
    {
        $classes = array();

        // Highlight current hour
        if (date('Y-m-d') == date('Y-m-d', $displayTime))
        {
            if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
            {
                $classes[] = 'table-calendar-highlight';
            }
        }

        // Is current table hour during working hours?
        if ($hour < $this->getWorkingHoursStart() || $hour >= $this->getWorkingHoursEnd())
        {
            $classes[] = 'table-calendar-disabled';
        }

        if ($hour % 2 == 0)
        {
            $classes[] = 'table-calendar-alternate';
        }

        return $classes;
    }
}

