<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendar extends Calendar
{

    /**
     * The number of hours for one table cell.
     *
     * @var integer
     */
    private $hourStep;

    /**
     *
     * @var integer
     */
    private $startHour;

    /**
     *
     * @var integer
     */
    private $endHour;

    /**
     *
     * @var boolean
     */
    private $hideOtherHours;

    /**
     *
     * @param integer $displayTime A time in the day to be displayed
     * @param integer $hourStep The number of hours for one table cell. Defaults to 1.
     * @param integer $startHour
     * @param integer $endHour
     * @param boolean $hideOtherHours
     * @param string[] $classes
     */
    public function __construct(
        $displayTime, $hourStep = 1, $startHour = 0, $endHour = 24, $hideOtherHours = false, $classes = []
    )
    {
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->hideOtherHours = $hideOtherHours;

        parent::__construct($displayTime, $classes);
        $this->buildTable();
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $this->addEvents();

        return $this->toHtml();
    }

    /**
     * Adds the events to the calendar
     */
    protected function addEvents()
    {
        $events = $this->getEventsToShow();

        $start = 0;
        $end = 24;

        if ($this->getHideOtherHours())
        {
            $start = $this->getStartHour();
            $end = $this->getEndHour();
        }

        foreach ($events as $time => $items)
        {
            if ($time >= $this->getEndTime())
            {
                continue;
            }

            $row = (date('H', $time) - $start) / $this->hourStep;

            foreach ($items as $index => $item)
            {
                try
                {
                    $cellContent = $this->getCellContents($row, 1);
                    $cellContent .= $item;
                    $this->setCellContents($row, 1, $cellContent);
                }
                catch (Exception $exception)
                {
                }
            }
        }
    }

    /**
     * Builds the table
     */
    protected function buildTable()
    {
        $header = $this->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->updateCellAttributes(0, 0, 'class="table-calendar-day-hours"');

        $header->setHeaderContents(
            0, 1,
            Translation::get(date('l', $this->getDisplayTime()) . 'Short', null, StringUtilities::LIBRARIES) . ' ' .
            date('d/m', $this->getDisplayTime())
        );

        $startHour = 0;
        $endHour = 24;

        if ($this->getHideOtherHours())
        {
            $startHour = $this->getStartHour();
            $endHour = $this->getEndHour();
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $startHour;
            $cellContent = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $this->setCellContents($rowId, 0, $cellContent);

            $classes = [];

            $classes[] = 'table-calendar-day-hours';

            if ($hour % 2 == 0)
            {
                $classes[] = 'table-calendar-alternate';
            }

            $this->updateCellAttributes($rowId, 0, 'class="' . implode(' ', $classes) . '"');
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $startHour;

            $this->setCellContents($rowId, 1, '');

            $classes = $this->determineCellClasses($hour);

            if (count($classes) > 0)
            {
                $this->updateCellAttributes($rowId, 1, 'class="' . implode(' ', $classes) . '"');
            }
        }
    }

    /**
     *
     * @param integer $hour
     *
     * @return string[]
     */
    protected function determineCellClasses($hour)
    {
        $classes = [];

        // Highlight current hour
        if (date('Y-m-d') == date('Y-m-d', $this->getDisplayTime()))
        {
            if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
            {
                $classes[] = 'table-calendar-highlight';
            }
        }

        // Is current table hour during working hours?
        if ($hour < $this->getStartHour() || $hour >= $this->getEndHour())
        {
            $classes[] = 'table-calendar-disabled';
        }

        if ($hour % 2 == 0)
        {
            $classes[] = 'table-calendar-alternate';
        }

        return $classes;
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
     * Gets the end date which will be displayed by this calendar.
     *
     * @return integer
     */
    public function getEndTime()
    {
        if ($this->getHideOtherHours())
        {
            return strtotime(date('Y-m-d ' . ($this->getEndHour() - 1) . ':59:59', $this->getDisplayTime()));
        }

        return strtotime('+24 Hours', $this->getStartTime());
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
     * Gets the number of hours for one table cell.
     *
     * @return integer
     */
    public function getHourStep()
    {
        return $this->hourStep;
    }

    /**
     * Sets the number of hours for one table cell.
     *
     * @param integer $hourStep
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
     * Gets the first date which will be displayed by this calendar.
     *
     * @return integer
     */
    public function getStartTime()
    {
        if ($this->getHideOtherHours())
        {
            return strtotime(date('Y-m-d ' . $this->getStartHour() . ':00:00', $this->getDisplayTime()));
        }

        return strtotime(date('Y-m-d 00:00:00', $this->getDisplayTime()));
    }
}
