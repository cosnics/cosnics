<?php
namespace Chamilo\Libraries\Calendar\Service\HtmlTable;

use Chamilo\Libraries\Calendar\HtmlTable\WeekCalendar;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekCalendarBuilder extends CalendarBuilder
{

    /**
     *
     * @param integer $displayTime
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\HtmlTable\WeekCalendar
     */
    protected function getCalendar($displayTime, $classes = [])
    {
        return new WeekCalendar($this->getCalendarConfiguration(), $displayTime, $classes);
    }

    /**
     *
     * @param integer $displayTime
     * @param string[] $displayParameters
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\HtmlTable\WeekCalendar
     */
    public function buildCalendar($displayTime, $displayParameters = [], $classes = [])
    {
        $weekCalendar = $this->getCalendar($displayTime, $classes);

        $header = $weekCalendar->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->updateCellAttributes(0, 0, 'class="table-calendar-week-hours"');

        $weekNumber = date('W', $displayTime);
        // Go 1 week back end them jump to the next monday to reach the first day of this week
        $firstDay = $weekCalendar->getStartTime();
        $lastDay = $weekCalendar->getEndTime();

        $workingStart = $this->getWorkingHoursStart();
        $workingEnd = $this->getWorkingHoursEnd();
        $hide = $this->getHideNonWorkingHours();
        $hourStep = $this->getHourStep();

        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        for ($hour = $start; $hour < $end; $hour += $hourStep)
        {
            $rowId = ($hour / $hourStep) - $start;
            $cellContent = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $weekCalendar->setCellContents($rowId, 0, $cellContent);

            $classes = array();

            $classes[] = 'table-calendar-week-hours';

            if ($hour % 2 == 0)
            {
                $classes[] = 'table-calendar-alternate';
            }

            $weekCalendar->updateCellAttributes($rowId, 0, 'class="' . implode(' ', $classes) . '"');
        }

        $dates[] = '';
        $today = date('Y-m-d');

        for ($day = 0; $day < 7; $day ++)
        {
            $weekDayTime = strtotime('+' . $day . ' days', $firstDay);
            $header->setHeaderContents(0, $day + 1, $this->getHeaderContent($weekDayTime, $displayParameters));

            for ($hour = $start; $hour < $end; $hour += $hourStep)
            {
                $row = ($hour / $hourStep) - $start;

                $classes = $this->determineCellClasses($today, $weekDayTime, $hour, $workingStart, $workingEnd);

                if (count($classes) > 0)
                {
                    $weekCalendar->updateCellAttributes($row, $day + 1, 'class="' . implode(' ', $classes) . '"');
                }

                $weekCalendar->setCellContents($row, $day + 1, '');
            }
        }

        return $weekCalendar;
    }

    /**
     *
     * @param integer $weekDayTime
     * @return string
     */
    protected function getHeaderContent($weekDayTime, $displayParameters = [])
    {
        $dayLabel = $this->getTranslator()->trans(date('l', $weekDayTime) . 'Short', [], Utilities::COMMON_LIBRARIES) .
             ' ' . date('d/m', $weekDayTime);

        $dayUrlTemplate = $this->getDayUrlTemplate($displayParameters);

        if (is_null($dayUrlTemplate))
        {
            return $dayLabel;
        }
        else
        {
            return '<a href="' . $this->getDayUrl($weekDayTime) . '">' . $dayLabel . '</a>';
        }
    }

    /**
     *
     * @param integer $today
     * @param integer $week_day
     * @param integer $hour
     * @param integer $workingStart
     * @param integer $workingEnd
     * @return string[]
     */
    protected function determineCellClasses($today, $weekDay, $hour, $workingStart, $workingEnd)
    {
        $classes = array();

        if ($today == date('Y-m-d', $weekDay))
        {
            if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
            {
                $class[] = 'table-calendar-highlight';
            }
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $weekDay) % 6 == 0)
        {
            $classes[] = 'table-calendar-weekend';
        }
        elseif ($hour % 2 == 0)
        {
            $classes[] = 'table-calendar-alternate';
        }

        if ($hour < $workingStart || $hour >= $workingEnd)
        {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
    }

    /**
     *
     * @param string[] $displayParameters
     * @return string
     */
    protected function getDayUrlTemplate($displayParameters = [])
    {
        $displayParameters[ViewRenderer::PARAM_TIME] = self::TIME_PLACEHOLDER;
        $displayParameters[ViewRenderer::PARAM_TYPE] = ViewRenderer::TYPE_WEEK;

        $dayUrlTemplate = new Redirect($displayParameters);

        return $dayUrlTemplate->getUrl();
    }

    /**
     *
     * @param integer $time
     * @return string
     */
    protected function getDayUrl($time)
    {
        return str_replace(self::TIME_PLACEHOLDER, $time, $this->getDayUrlTemplate());
    }
}

