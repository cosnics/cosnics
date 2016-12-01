<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A tabular representation of a day calendar
 * 
 * @package libraries\calendar\table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DayCalendar extends Calendar
{

    /**
     * The navigation links
     */
    private $navigationHtml;

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
     * Creates a new day calendar
     * 
     * @param int $displayTime A time in the day to be displayed
     * @param int $hourStep The number of hours for one table cell. Defaults to 1.
     * @param int $startHour
     * @param int $endHour
     * @param boolean $hideOtherHours
     */
    public function __construct($displayTime, $hourStep = 1, $startHour = 0, $endHour = 24, $hideOtherHours = false, 
        $classes = array())
    {
        $this->navigationHtml = '';
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
     * 
     * @return int
     */
    public function getStartTime()
    {
        if ($this->getHideOtherHours())
        {
            return strtotime(date('Y-m-d ' . $this->getStartHour() . ':00:00', $this->getDisplayTime()));
        }
        
        return strtotime(date('Y-m-d 00:00:00', $this->getDisplayTime()));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * 
     * @return int
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
     * Builds the table
     */
    protected function buildTable()
    {
        $yearDay = date('z', $this->getDisplayTime()) + 1;
        $yearWeek = date('W', $this->getDisplayTime());
        
        $header = $this->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->updateCellAttributes(0, 0, 'class="table-calendar-day-hours"');
        
        $header->setHeaderContents(
            0, 
            1, 
            Translation::get(date('l', $this->getDisplayTime()) . 'Short', null, Utilities::COMMON_LIBRARIES) . ' ' .
                 date('d/m', $this->getDisplayTime()));
        
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
            
            $classes = array();
            
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
            
            $tableStartDate = mktime(
                $hour, 
                0, 
                0, 
                date('m', $this->getDisplayTime()), 
                date('d', $this->getDisplayTime()), 
                date('Y', $this->getDisplayTime()));
            
            $tableEndDate = strtotime('+' . $this->getHourStep() . ' hours', $tableStartDate);
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
     * @return string[]
     */
    protected function determineCellClasses($hour)
    {
        $classes = array();
        
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
                catch (\Exception $exception)
                {
                }
            }
        }
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
}
