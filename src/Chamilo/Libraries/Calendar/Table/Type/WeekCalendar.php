<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Configuration\Configuration;

/**
 * A tabular representation of a week calendar
 *
 * @package libraries\calendar\table$WeekCalendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WeekCalendar extends Calendar
{
    const TIME_PLACEHOLDER = '__TIME__';

    /**
     * The number of hours for one table cell.
     */
    private $hourStep;

    /**
     *
     * @var int
     */
    private $startHour;

    /**
     *
     * @var int
     */
    private $endHour;

    /**
     *
     * @var boolean
     */
    private $hideOtherHours;

    /**
     * Creates a new week calendar
     *
     * @param int $displayTime A time in the week to be displayed
     * @param int $hourStep The number of hours for one table cell. Defaults to 2.
     */
    public function __construct($displayTime, $dayUrlTemplate = null, $hourStep = 2, $startHour = 0, $endHour = 24,
        $hideOtherHours = false, $classes = array())
    {
        $this->dayUrlTemplate = $dayUrlTemplate;
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->hideOtherHours = $hideOtherHours;

        parent::__construct($displayTime, $classes);
        $this->buildTable();
    }

    /**
     * Gets the number of hours for one table cell.
     *
     * @return int
     */
    public function getHourStep()
    {
        return $this->hourStep;
    }

    /**
     * Sets the number of hours for one table cell.
     *
     * @return int
     */
    public function setHourStep($hourStep)
    {
        $this->hourStep = $hourStep;
    }

    /**
     *
     * @return integer
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     *
     * @param integer $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     *
     * @return integer
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     *
     * @param integer $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     *
     * @return boolean
     */
    public function getHideOtherHours()
    {
        return $this->hideOtherHours;
    }

    /**
     *
     * @param boolean $hideOtherHours
     */
    public function setHideOtherHours($hideOtherHours)
    {
        $this->hideOtherHours = $hideOtherHours;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday.
     *
     * @return int
     */
    public function getStartTime()
    {
        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $this->getDisplayTime()));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday.
     *
     * @return int
     */
    public function getEndTime()
    {
        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));

        if ($setting == 'sunday')
        {
            return strtotime('Next Saterday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Sunday', $this->getStartTime());
    }

    /**
     * Builds the table
     */
    private function buildTable()
    {
        $header = $this->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->updateCellAttributes(0, 0, 'class="table-calendar-week-hours"');

        $weekNumber = date('W', $this->getDisplayTime());
        // Go 1 week back end them jump to the next monday to reach the first day of this week
        $firstDay = $this->getStartTime();
        $lastDay = $this->getEndTime();

        $workingStart = $this->getStartHour();
        $workingEnd = $this->getEndHour();
        $hide = $this->getHideOtherHours();
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        for ($hour = $start; $hour < $end; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $start;
            $cellContent = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $this->setCellContents($rowId, 0, $cellContent);

            $classes = array();

            $classes[] = 'table-calendar-week-hours';

            if ($hour % 2 == 0)
            {
                $classes[] = 'table-calendar-alternate';
            }

            $this->updateCellAttributes($rowId, 0, 'class="' . implode(' ', $classes) . '"');
        }

        $dates[] = '';
        $today = date('Y-m-d');

        for ($day = 0; $day < 7; $day ++)
        {
            $weekDayTime = strtotime('+' . $day . ' days', $firstDay);
            $header->setHeaderContents(0, $day + 1, $this->getHeaderContent($weekDayTime));

            for ($hour = $start; $hour < $end; $hour += $this->getHourStep())
            {
                $row = ($hour / $this->getHourStep()) - $start;

                $classes = $this->determineCellClasses($today, $weekDayTime, $hour, $workingStart, $workingEnd);

                if (count($classes) > 0)
                {
                    $this->updateCellAttributes($row, $day + 1, 'class="' . implode(' ', $classes) . '"');
                }

                $this->setCellContents($row, $day + 1, '');
            }
        }
    }

    /**
     *
     * @param integer $weekDayTime
     * @return string
     */
    protected function getHeaderContent($weekDayTime)
    {
        $dayLabel = Translation::get(date('l', $weekDayTime) . 'Short', null, Utilities::COMMON_LIBRARIES) . ' ' .
             date('d/m', $weekDayTime);

        $dayUrlTemplate = $this->getDayUrlTemplate();

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
     * @param integer $hour
     * @return string[]
     */
    protected function determineCellClasses($today, $week_day, $hour, $workingStart, $workingEnd)
    {
        $classes = array();

        if ($today == date('Y-m-d', $week_day))
        {
            if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
            {
                $class[] = 'table-calendar-highlight';
            }
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $week_day) % 6 == 0)
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
     * Adds the events to the calendar
     */
    private function addEvents()
    {
        $events = $this->getEventsToShow();
        $workingStart = $this->getStartHour();
        $workingEnd = $this->getEndHour();
        $hide = $this->getHideOtherHours();
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        foreach ($events as $time => $items)
        {
            $row = (date('H', $time) / $this->hourStep) - $start;

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

    public function render()
    {
        $this->addEvents();
        return $this->toHtml();
    }

    /**
     *
     * @return string
     */
    public function getDayUrlTemplate()
    {
        return $this->dayUrlTemplate;
    }

    /**
     *
     * @param string $dayUrlTemplate
     */
    public function setDayUrlTemplate($dayUrlTemplate)
    {
        $this->dayUrlTemplate = $dayUrlTemplate;
    }

    /**
     *
     * @param integer $time
     * @return string
     */
    public function getDayUrl($time)
    {
        return str_replace(self::TIME_PLACEHOLDER, $time, $this->getDayUrlTemplate());
    }
}
