<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniDayCalendar extends DayCalendar
{

    /**
     *
     * @param integer $displayTime
     * @param integer $hourStep
     */
    public function __construct($displayTime, $hourStep = '1')
    {
        parent :: __construct($displayTime, $hourStep);
        $this->updateAttributes('class="calendar_table mini_calendar"');
    }

    public function getStartHour()
    {
        $workingStart = LocalSetting :: get('working_hours_start');
        $hide = LocalSetting :: get('hide_none_working_hours');
        $startHour = 0;

        if ($hide)
        {
            $startHour = $workingStart;
        }

        return $startHour;
    }

    public function getEndHour()
    {
        $workingEnd = LocalSetting :: get('working_hours_end');
        $hide = LocalSetting :: get('hide_none_working_hours');
        $endHour = 24;

        if ($hide)
        {
            $endHour = $workingEnd;
        }

        return $endHour;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     *
     * @return int
     */
    public function getStartTime()
    {
        return strtotime(date('Y-m-d ' . $this->getStartHour() . ':00:00', $this->getDisplayTime()));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     *
     * @return int
     */
    public function getEndTime()
    {
        return strtotime(date('Y-m-d ' . ($this->getEndHour() - 1) . ':59:59', $this->getDisplayTime()));
    }

    protected function buildTable()
    {
        $yearDay = date('z', $this->getDisplayTime()) + 1;
        $yearWeek = date('W', $this->getDisplayTime());

        $header = $this->getHeader();
        $header->addRow(
            array(
                Translation :: get('Day', null, Utilities :: COMMON_LIBRARIES) . ' ' . $yearDay . ', ' .
                     Translation :: get('Week', null, Utilities :: COMMON_LIBRARIES) . ' ' . $yearWeek));
        $header->setRowType(0, 'th');

        $startHour = $this->getStartHour();
        $endHour = $this->getEndHour();

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $startHour;

            $tableStartDate = mktime(
                $hour,
                0,
                0,
                date('m', $this->getDisplayTime()),
                date('d', $this->getDisplayTime()),
                date('Y', $this->getDisplayTime()));

            $tableEndDate = strtotime('+' . $this->getHourStep() . ' hours', $tableStartDate);
            $cellContents = $hour . 'u - ' . ($hour + $this->getHourStep()) . 'u <br />';
            $this->setCellContents($rowId, 0, $cellContents);

            // Highlight current hour
            if (date('Y-m-d') == date('Y-m-d', $this->getDisplayTime()))
            {
                if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
                {
                    $this->updateCellAttributes($rowId, 0, 'class="highlight"');
                }
            }

            // Is current table hour during working hours?
            if ($hour < 8 || $hour > 18)
            {
                $this->updateCellAttributes($rowId, 0, 'class="disabled_month"');
            }
        }
    }

    /**
     * Returns a html-representation of this minidaycalendar
     *
     * @return string
     */
    public function toHtml()
    {
        $html = parent :: toHtml();
        return str_replace('class="calendar_navigation"', 'class="calendar_navigation mini_calendar"', $html);
    }

    /**
     * Adds the events to the calendar
     */
    private function addEvents()
    {
        $events = $this->getEventsToShow();

        foreach ($events as $time => $items)
        {
            if ($time >= $this->getEndTime())
            {
                continue;
            }

            $row = (date('H', $time) / $this->getHourStep()) - ($this->getStartHour() / $this->getHourStep());

            foreach ($items as $index => $item)
            {
                $cellContent = $this->getCellContents($row, 0);
                $cellContent .= $item;
                $this->setCellContents($row, 0, $cellContent);
            }
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Table\Type\DayCalendar::render()
     */
    public function render()
    {
        $this->addEvents();
        return $this->toHtml();
    }
}
